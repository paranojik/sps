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
    );
  )
  return $sps_config;
}

define('SPS_SITE_STATE_KEY', 'sps_site_state_key');

class Manager {
  protected $cache_controller;
  protected $persistent_controller;
  /**
   * Manger::__construct
   *
   * @param $cache_controller a \Drupal\sps\StorageControllerInterface object used to build site state
   * @param $cache_persistent_controller a \Drupal\sps\PersistentStorageControllerInterface used to stor and retrieve the current site state
   */
  public function __construct(\Drupal\sps\StorageControllerInterface $cache_controller, \Drupal\sps\PersistentStorageControllerInterface $persistent_controller) {
    $this->setCacheController($cache_controller);
    $this->setPersistentStorageController($persistent_controller);
  }
  /**
   * Manager::setCacheController
   * @PARAM $controller: an object that implements Drupal\sps\StorageControllerInterface
   */ 
  protected function setCacheController(\Drupal\sps\StorageControllerInterface $controller) {
    $this->cache_controller = $controller;
  }

  /**
   * Manager::setPersistentStorageController
   * @PARAM $controller: an object that implements Drupal\sps\StorageControllerInterface
   */ 
  protected function setPersistentStorageController(\Drupal\sps\PersistentStorageControllerInterface $controller) {
    $this->persistent_controller = $controller;
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
    if($this->persistent_controller->is_set(SPS_SITE_STATE_KEY)) {
      return $this->persistent_controller->get(SPS_SITE_STATE_KEY);
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
    $site_state = new SiteState($this->cache_controller, $override);
    $this->persistent_controller->set(SPS_SITE_STATE_KEY, $site_state);
  }
}
