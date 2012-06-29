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
  protected $state_controler;
  protected $config_controller;
  protected $override_controller;

  /**
  * Constructor for \Drupal\sps\Manager
  *
  * @param $state_controler \Drupal\sps\StorageControllerInterface
  *   The control to use when accessing State info (like site state)
  * @param $override_controller \Drupal\sps\StorageControllerInterface
  *   the control to use when accessing overrides
  * @param $config_controller \Drupal\sps\StorageControllerInterface
  *   the control to be used when accessing config
  * @param $plugin_controller \Drupal\sps\PluginControllerInterface
  *   The control to use when accessing plugins
  *
  * @return 
  */
  public function __construct(StorageControllerInterface $state_controler, StorageControllerInterface $override_controller, StorageControllerInterface $config_controller, PluginControllerInterface $plugin_controller) {
    $this->setStateController($state_controler);
    $this->setOverrideController($override_controller);
    $this->setConfigController($config_controller);
    $this->setPluginController($plugin_controller);
  }

  /**
   * store the state controller
   *
   * @param $controller Drupal\sps\StorageControllerInterface
   *   The control to use when accessing State info (like site state)
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
   * @param $config_controller \Drupal\sps\StorageControllerInterface
   *   the control to be used when accessing config
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
   * @param $override_controller \Drupal\sps\StorageControllerInterface
   *   the control to use when accessing overrides
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
   * @param $plugin_controller \Drupal\sps\PluginControllerInterface
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
   * @param $override \Drupal\sps\OverrideInterface 
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
   * call a reaction rect method
   *
   * @param $reaction String
   *   the name of a reaction plugin;
   * @param $data Vary
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
   * @param $type String
   *   the type of plugin as defined in hook_sps_plugin_types_info
   * @param $name String
   *   the name of the plugin as defined in hook_sps_PLUGIN_TYPE_plugin_info;
   * @return \Drupal\sps\PluginInterface
   *   An instance of the requested Plugin
   */
  public function getPlugin($type, $name) {
    return $this->plugin_controller->getPlugin($type, $name, $this);
  }

  /**
   * get meta info on a plugin
   * @param $type String
   *   the type of plugin as defined in hook_sps_plugin_types_info
   * @param $name String | Null
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
   * @param $type String
   *   the type of plugin as defined in hook_sps_plugin_types_info
   * @param $property String
   *   the meta property to compare to the value
   * @param $value Vary
   *   the value to compare to the meta property
   * @return Array
   *   an array of meta data for the plugins
   */
  public function getPluginByMeta($type, $property, $value) {
    return $this->plugin_controller->getPluginInfoByMeta($type);
  }
}
