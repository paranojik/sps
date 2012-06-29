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
   * Manger::__construct
   *
   * @param $cache_controller 
   *   a \Drupal\sps\StorageControllerInterface object used to build site state
   * @param $cache_persistent_controller 
   *   a \Drupal\sps\PersistentStorageControllerInterface used to stor and retrieve the current site state
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
   * @param $controller 
   *   an object that implements Drupal\sps\StorageControllerInterface
   */ 
  protected function setStateController(StorageControllerInterface $controller) {
    $this->state_controller = $controller;
    return $this;
  }

  /**
   * store the config controller
   *
   * @param $controller 
   *   an object that implements Drupal\sps\StorageControllerInterface
   */ 
  protected function setConfigController(StorageControllerInterface $controller) {
    $this->config_controller = $controller;
    return $this;
  }

  /**
   * store the override controller
   *
   * @param $controller 
   *   an object that implements Drupal\sps\StorageControllerInterface
   */ 
  protected function setOverrideController(StorageControllerInterface $controller) {
    $this->override_controller = $controller;
    return $this;
  }

  /**
   * store the override controller
   *
   * @param $controller  
   *   an object that implements Drupal\sps\StorageControllerInterface
   */ 
  protected function setPluginController(PluginControllerInterface $controller) {
    $this->plugin_controller = $controller;
    return $this;
  }

  /**
   * Pull the site state from site state controller
   * Note the state controller is resposible for resonable caching of the site state
   *
   * @return 
   *   SiteState | NULL
   */
  public function getSiteState() {
    if($this->state_controller->is_set($this->state_controller_site_state_key)) {
      return $this->state_controller->get($this->state_controller_site_state_key);
    }
  }

  /**
   * Get which 
   * Note the state controller is resposible for resonable caching of the site state
   *
   * @return 
   *   SiteState | NULL
   */
  public function getPreviewForm() {
    $preview_form_plugin = $this->config_controller->get(SPS_CONFIG_PREVIEW_FORM_PLUGIN_KEY);
    $preview_form_settings = $this->config_controller->get(SPS_CONFIG_PREVIEW_FORM_SETTINGS);
    $this->preview_form = $this->getPlugin("preview_form", $preview_form_plugin);
    $preview_form->setConfig($this->config($preview_form_settings));
  }

  /**
   * Manager::setSiteState
   *
   * Create A SiteState from an override, and store it.
   *
   * This might get made private
   *
   * @PARAM $override 
   *   a \Drupal\sps\OverrideInterface object
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
   * @return 
   *   the controller key, a string
   */
  public function getStateControllerSiteStateKey() {
    return $this->state_controller_site_state_key;
  }

  /**
   * call a reaction rect method
   *
   * @param $reaction 
   *   the name of a reaction plugin;
   * @param $data 
   *   Vary, data to be passed to the react method
   * @return 
   *   Vary 
   */
  public function react($reaction, $data) {
    return $this->getPlugin("reaction", $reaction)->react($data);
  }

  /**
   * factory for building a plugin object
   *
   * @param $type 
   *   the type of plugin as defined in hook_sps_plugin_types_info
   * @param $name 
   *   the name of the plugin as defined in hook_sps_PLUGIN_TYPE_plugin_info;
   * @return 
   *   an array of meta data for the plugin
   */
  public function getPlugin($type, $name) {
    return $this->plugin_controller->getPlugin($type, $name, $this);
  }

  /**
   * get meta info on a plugin
   * @param $type 
   *   the type of plugin as defined in hook_sps_plugin_types_info
   * @param $name 
   *   the name of the plugin as defined in hook_sps_PLUGIN_TYPE_plugin_info;
   * @return 
   *   an array of meta data for the plugin
   */
  public function getPluginInfo($type, $name=NULL) {
    return $this->plugin_controller->getPluginInfo($type, $name);
  }

  /**
   * get meta info on a plugin
   *
   * @param $type 
   *   the type of plugin as defined in hook_sps_plugin_types_info
   * @param $property 
   *   the meta property to compare to the value
   * @param $value 
   *   the value to compare to the meta property
   * @return 
   *   an array of meta data for the plugins
   */
  public function getPluginByMeta($type, $property, $value) {
    return $this->plugin_controller->getPluginInfoByMeta($type);
  }
}
