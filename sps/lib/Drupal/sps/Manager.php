<?php
namespace Drupal\sps;

function test_sps_get_config() {
  $sps_config = array(
    'conditions' => array(
      'collection' => array(
        'title' => 'Collection',
        'widget' => 'collection_select',
        'override' => 'view_collection_override',
      ),
      'date' => array(
        'title' => 'Live Date',
        'widget' => 'live_date',
        'override' => 'view_live_date_override',
      ),
    ),
  );
  return $sps_config;
}


class Manager {
  protected $state_controller_site_state_key = 'sps_site_state_key';
  protected $state_controller;
  protected $config_controller;
  protected $override_controller;
  protected $root_condition;

  /**
  * Constructor for \Drupal\sps\Manager
  *
  * @param \Drupal\sps\StorageControllerInterface $state_controller
  *   The control to use when accessing State info (like site state)
  * @param \Drupal\sps\StorageControllerInterface $override_controller
  *   the control to use when accessing overrides
  * @param \Drupal\sps\StorageControllerInterface $config_controller
  *   the control to be used when accessing config
  * @param \Drupal\sps\PluginControllerInterface $plugin_controller
  *   The control to use when accessing plugins
  *
  * @return
  */
  public function __construct(StorageControllerInterface $state_controller, StorageControllerInterface $override_controller, StorageControllerInterface $config_controller, PluginControllerInterface $plugin_controller) {
    $this->setStateController($state_controller)
      ->setOverrideController($override_controller)
      ->setConfigController($config_controller)
      ->setPluginController($plugin_controller);
  }

  /**
   * store the state controller
   *
   * @param \Drupal\sps\StorageControllerInterface $controller
   *   The control to use when accessing State info (like site state)
   *
   * @return \Drupal\sps\Manager
   *   Self
   */
  protected function setStateController(StorageControllerInterface $controller) {
    $this->state_controller = $controller;
    return $this;
  }

  /**
   * store the config controller
   *
   * @param StorageControllerInterface $controller
   *   the control to be used when accessing config
   *
   * @return \Drupal\sps\Manager
   *   Self
   */
  protected function setConfigController(StorageControllerInterface $controller) {
    $this->config_controller = $controller;
    return $this;
  }

  /**
   * store the override controller
   *
   * @param \Drupal\sps\StorageControllerInterface $override_controller
   *   the control to use when accessing overrides
   *
   * @return \Drupal\sps\Manager
   *   Self
   */
  protected function setOverrideController(StorageControllerInterface $controller) {
    $this->override_controller = $controller;
    return $this;
  }

  /**
   * store the override controller
   *
   * @param \Drupal\sps\PluginControllerInterface $plugin_controller
   *   The control to use when accessing plugins
   * @return \Drupal\sps\Manager
   *   Self
   */
  protected function setPluginController(PluginControllerInterface $controller) {
    $this->plugin_controller = $controller;
    return $this;
  }

  /**
   * Pull the site state from site state controller
   *
   * Note the state controller is resposible for resonable caching of the site state
   *
   * @return Vary
   *   SiteState | NULL
   */
  public function getSiteState() {
    if($this->state_controller->is_set($this->state_controller_site_state_key)) {
      return $this->state_controller->get($this->state_controller_site_state_key);
    }
  }

  /**
   * Create A SiteState from an override, and store it.
   *
   * This might get made private
   *
   * @param \Drupal\sps\OverrideInterface  $override
   *   the override to use when creating the SiteState
   * @return \Drupal\sps\Manager
   *   Self
   */
  public function setSiteState(\Drupal\sps\Plugins\OverrideInterface $override) {
    $site_state = new SiteState($this->override_controller, $override);
    $this->state_controller->set($this->state_controller_site_state_key, $site_state);
    return $this;
  }

  /**
   * Get what should be a relatively static variable used for storing the site state
   *
   * This is mostly used for tests
   *
   * @return String
   *   the controller key, a string
   */
  public function getStateControllerSiteStateKey() {
    return $this->state_controller_site_state_key;
  }


  /**
   * Passthrough from Drupal form to the correct condition for building the preview form
   *
   * sps_condition_preview_form is a function defined in our .module which calls our callback
   *
   * @param $form
   *   The form array used in hook_form
   * @param $form_state
   *   The form_state array as used in hook_form
   *
   * @return
   *   A drupal form array created but the root condition
   *
   * @see sps_condition_preview_form
   */
  public function getPreviewForm() {
    $root_condition = $this->getRootCondition();
    $getForm = function($form, &$form_state) {
      return $root_condition->getElement($form, $form_state);
    };
    drupal_get_form('sps_condition_preview_form', $getForm);
  }

  /**
  * Notify the manager that the preview form submission is complete.
  *
  * @return
  *   Self
  */
  public function previewFormSubmitted() {
    $root_condition = $this->getRootCondition();
    $this->setSiteState($root_condition->getOverride());
    return $this;
  }

  /**
  * Helper method for getting and causing the root Condition
  *
  * The Root condition is the use as the basis for the constructing the preview form
  * It can be expect that it will be much more comilicated then the other conditions
  *
  * This method select the condition and its config using the config controller.
  *
  * @return Drupal\sps\Plugins\ConditionInterface
  *   the current root condition object
  */
  protected function getRootCondition() {
    if(!isset($this->root_condition_plugin)) {
      $settings = $this->config_controller->get(SPS_CONFIG_ROOT_CONDITION);
      $root_condition_plugin = $settings['name'];
      $this->root_condition_plugin = $this->getPlugin('condition', $root_condition_plugin);
      $this->root_condition_plugin->setConfig($settings['config']);
    }
    return $this->root_condition_plugin;
  }

  /**
   * call a reaction rect method
   *
   * @param String $reaction
   *   the name of a reaction plugin;
   * @param Vary $data
   *   data to be passed to the react method
   * @return Vary
   *   Data used by the item calling raction
   */
  public function react($reaction, $data) {
    return $this->getPlugin("reaction", $reaction)->react($data);
  }

  /**
   * factory for building a plugin object
   *
   * @param String $type
   *   the type of plugin as defined in hook_sps_plugin_types_info
   * @param String $name
   *   the name of the plugin as defined in hook_sps_PLUGIN_TYPE_plugin_info;
   * @return \Drupal\sps\PluginInterface
   *   An instance of the requested Plugin
   */
  public function getPlugin($type, $name) {
    return $this->plugin_controller->getPlugin($type, $name, $this);
  }

  /**
   * get meta info on a plugin
   * @param String $type
   *   the type of plugin as defined in hook_sps_plugin_types_info
   * @param String | Null $name
   *   the name of the plugin as defined in hook_sps_PLUGIN_TYPE_plugin_info;
   * @return Array
   *   an array of meta data for the plugin
   */
  public function getPluginInfo($type, $name=NULL) {
    return $this->plugin_controller->getPluginInfo($type, $name);
  }

  /**
   * get meta info on a plugin
   *
   * @param String $type
   *   the type of plugin as defined in hook_sps_plugin_types_info
   * @param String $property
   *   the meta property to compare to the value
   * @param Vary $value
   *   the value to compare to the meta property
   * @return Array
   *   an array of meta data for the plugins
   */
  public function getPluginByMeta($type, $property, $value) {
    return $this->plugin_controller->getPluginInfoByMeta($type, $property, $meta);
  }
}
