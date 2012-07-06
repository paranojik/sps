<?php
namespace Drupal\sps;

/**
 * The Manager is the heart of the SPS system, taking inputs from different
 * parts of the system and pushing them to the correct object for processing
 * it can be orginized in to a few different sections
 *
 * Controller Access
 * The Manager managest access to drupal systems via different controllers. The
 * SPS system use the manger to access there when they need access to Drupal
 *  .---------------------------------------------------------------.
 *  |                            Systems                            |
 *  '---------------------------------------------------------------'
 *                                  |
 *                                  v
 *                             .---------.
 *                             | Manager |
 *                             |---------|
 *                             '---------'
 * .---------------------------.    |
 * |     State Controller      |    |    .---------------------------.
 * |---------------------------|    |    |     Plugin Controller     |
 * | Controls the interface    |    |    |---------------------------|
 * | to the State cache        |<---|    | Controls the interface    |
 * | used to hold the current  |    '--->| to the plugin system      |
 * | site state                |    |    | holds method for getting  |
 * '---------------------------'    |    | pluign info and objects   |
 * .---------------------------.    |    '---------------------------'
 * |     Config Controller     |    |
 * |---------------------------|    |    .---------------------------.
 * | Controls the interface    |    |    |    Override Controller    |
 * | to the config for sps     |<---|    |---------------------------|
 * | hold the root condition   |    '--->| Controls the interface    |
 * | and infomation of plugins |         | to the store of the       |
 * '---------------------------'         | current overrides         |
 *                                       '---------------------------'
 *
 * Site State
 * THe Manager can create a site state object, and uses the State Controller
 * to keep it around from page load to page load. When creating site state it
 * hand off the Override Controller So that the Site state can Compile the
 * override data and store it in the Override Controller
 *
 *  @TODO this part of the system should be reviews when we start needing
 *  access to the site state
 *
 *
 *
 * Preview Form
 * The Manager is the interface between the form hooks in the sps module
 * and the Root Conditon that does most of the Form creation and processing
 * .-----------------------------------------.
 * |    preview form hooks in sps.module     |
 * |-----------------------------------------|
 * | sps_preview_form()                      |
 * | sps_preview_form_validate()             |
 * | sps_preview_form_submit()               |
 * '-----------------------------------------'
 *                      |
 *                      |
 *                      v
 * .-----------------------------------------.
 * |                 Manager                 |
 * |-----------------------------------------|
 * | getPreviewForm($form, $form_state)      |
 * | validatePreviewForm($form, $form_state) |
 * | submitPreviewForm($form, $form_state)   |
 * '-----------------------------------------'
 *                      |
 *                      |
 *                      v
 * .-----------------------------------------.
 * |            Condition (Root)             |
 * |-----------------------------------------|
 * | getElement($form, $form_state)          |
 * | validateElement($form, $form_state)     |
 * | submitElement($form, $form_state)       |
 * '-----------------------------------------'
 *
 *
 * Reactions
 * The manager is use as an interface for Drupal hooks that need to have a
 * reaction react
 *                    .-----------------------.   .--------------.
 * .--------------.   |        Manager        |   |   Reaction   |
 * | Drupal hooks |-->|-----------------------|-->|--------------|
 * '--------------'   | react($plugin, $data) |   | react($data) |
 *                    '-----------------------'   '--------------'
 *
 * Plugins
 * The Manager is a passthough to the plugin controller
 */
class Manager {
  protected $state_controller_site_state_key = 'sps_site_state_key';
  protected $state_controller;
  protected $config_controller;
  protected $override_controller;
  protected $root_condition;
  protected $plugin_controller;

  /**
   * Constructor for \Drupal\sps\Manager
   *
   * @param \Drupal\sps\StorageControllerInterface $state_controller
   *  The control to use when accessing State info (like site state)
   * @param \Drupal\sps\StorageControllerInterface $override_controller
   *   the control to use when accessing overrides
   * @param \Drupal\sps\StorageControllerInterface $config_controller
   *   the control to be used when accessing config
   * @param \Drupal\sps\PluginControllerInterface  $plugin_controller
   *   The control to use when accessing plugins
   *
   * @return \Drupal\sps\Manager
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
   * @param \Drupal\sps\StorageControllerInterface $controller
   *  the control to be used when accessing config
   *
   * @return \Drupal\sps\Manager
   *           Self
   */
  protected function setConfigController(StorageControllerInterface $controller) {
    $this->config_controller = $controller;
    return $this;
  }

  /**
   * store the override controller
   *
   * @param \Drupal\sps\StorageControllerInterface $controller
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
   * @param \Drupal\sps\PluginControllerInterface $controller
   *   The control to use when accessing plugins
   *
   * @return \Drupal\sps\Manager
   *   Self
   */
  protected function setPluginController(PluginControllerInterface $controller) {
    $this->plugin_controller = $controller;
    return $this;
  }

  /**
   * Pull the site state form site state controller
   *
   * Note the state controller is resposible for resonable caching of the site state
   *
   * @return \Drupal\sps\SiteState | NULL
   */
  public function getSiteState() {
    if ($this->state_controller->exists($this->state_controller_site_state_key)) {
      return $this->state_controller->get($this->state_controller_site_state_key);
    }

    return NULL;
  }

  /**
   * Create A SiteState form an override, and store it.
   *
   * This might get made private
   *
   * @param \Drupal\sps\Plugins\OverrideInterface  $override
   *   the override to use when creating the SiteState
   *
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
  * @param $form
  *   The form array used in hook_form
  * @param $form_state
  *   The form_state array as used in hook_form
  *
  * @return
  *   A drupal form array created but the root condition
  */
  public function getPreviewForm(&$form, &$form_state) {
    $root_condition = $this->getRootCondition();
    return $root_condition->getElement($form, $form_state);

  }

  /**
   * Passthrough from Drupal form to the correct condition used for validate a preview form
   *
   * @param $form
   *   The form array passed to drupal validate functions
   * @param $form_state
   *   The form_state array passed to drupal validate functions
   *
   * @return \Drupal\sps\Manager
   *  Self
   */
  public function validatePreviewForm($form, &$form_state) {
    $root_condition = $this->getRootCondition();
    $root_condition->validateElement($form, $form_state);
    return $this;
  }

  /**
  * Passthrough from Drupal form to the correct condition's submit method.
  *
  * Also save the correct override after submit.
  *
  * @param $form
  *   The form array passed to drupal submit functions
  * @param $form_state
  *   The form_state array passed to drupal submit functions
  *
  * @return \Drupal\sps\Manager
  *   Self
  */
  public function submitPreviewForm($form, &$form_state) {
    $root_condition = $this->getRootCondition();
    $root_condition->submitElement($form, $form_state);
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
  * @return \Drupal\sps\Plugins\ConditionInterface
  *   the current root condition object
  */
  protected function getRootCondition() {
    if(!isset($this->root_condition)) {
      $settings = $this->config_controller->get(SPS_CONFIG_ROOT_CONDITION);
      $root_condition_plugin = $settings['name'];
      $this->root_condition = $this->getPlugin('condition', $root_condition_plugin);
      $this->root_condition->setConfig($settings['config']);
    }
    return $this->root_condition;
  }

  /**
   * call a reaction rect method
   *
   * @param String $reaction
   *   the name of a reaction plugin;
   * @param mixed $data
   *   data to be passed to the react method
   *
   * @return mixed
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
   *
   * @return \Drupal\sps\Plugins\PluginInterface
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
   *
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
   * @param mixed $value
   *   the value to compare to the meta property
   *
   * @return Array
   *   an array of meta data for the plugins
   */
  public function getPluginByMeta($type, $property, $value) {
    return $this->plugin_controller->getPluginInfoByMeta($type);
  }
}
