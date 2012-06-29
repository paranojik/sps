<?php
namespace Drupal\sps;

function test_sps_get_config() {
  $sps_config = array(
    'conditions' => array(
      'collection' => array(
        'title' => 'Collection',
        'widget' => 'collection_select',
        'overide' => 'view_collection_override',
      ),
      'date' => array(
        'title' => 'Live Date',
        'widget' => 'live_date',
        'overide' => 'view_live_date_override',
      ),
    ),
  );
  return $sps_config;
}


class Manager {
  protected $controller_key = 'sps_site_state_key';
  protected $site_state_controller;
  protected $config_controller;
  protected $override_controller;
  /**
   * Manger::__construct
   *
   * @param $cache_controller a \Drupal\sps\StorageControllerInterface object used to build site state
   * @param $cache_persistent_controller a \Drupal\sps\PersistentStorageControllerInterface used to stor and retrieve the current site state
   */
  public function __construct(StorageControllerInterface $site_state_controller, StorageControllerInterface $override_controller, StorageControllerInterface $config_controller) {
    $this->setSiteStateController($site_state_controller);
    $this->setOverrideController($override_controller);
    $this->setConfigController($config_controller);
  }

  /**
   * store the site_state controller
   *
   * @PARAM $controller: an object that implements Drupal\sps\StorageControllerInterface
   */ 
  protected function setSiteStateController(StorageControllerInterface $controller) {
    $this->site_state_controller = $controller;
    return $this;
  }

  /**
   * store the config controller
   *
   * @PARAM $controller: an object that implements Drupal\sps\StorageControllerInterface
   */ 
  protected function setConfigController(StorageControllerInterface $controller) {
    $this->config_controller = $controller;
    return $this;
  }

  /**
   * store the override controller
   *
   * @PARAM $controller: an object that implements Drupal\sps\StorageControllerInterface
   */ 
  protected function setOverrideController(StorageControllerInterface $controller) {
    $this->override_controller = $controller;
    return $this;
  }

  /**
   * Manager::getSiteState
   *
   * Pull the site state from persistent storage
   * Note the Persistent Storage is resposible for resonable caching of the site state
   *
   * @return SiteState | NULL
   */
  public function getSiteState() {
    if($this->site_state_controller->is_set($this->controller_key)) {
      return $this->site_state_controller->get($this->controller_key);
    }
  }
  /**
   * Manager::setSiteState
   *
   * This takes an override and compleates the Site state
   *
   * This might get made private
   * @PARAM $override : a \Drupal\sps\OverrideInterface object
   */
  public function setSiteState(\Drupal\sps\OverrideInterface $override) {
    $site_state = new SiteState($this->override_controller, $override);
    $this->site_state_controller->set($this->controller_key, $site_state);
    return $this;
  }
  public function getControllerKey() {
    return $this->controller_key;
  }
}
