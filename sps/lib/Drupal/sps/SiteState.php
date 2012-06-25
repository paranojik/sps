<?php

namespace Drupal\sps;

class SiteState {
  protected $override;
  protected $cache_controller;
  
  /**
   * SiteState::__construct
   *
   *
   * @PARAM $cache_controller: a object that implements Drupal\sps\StorageControllerInterface
   * @PARAM $override: a object that implements Drupal\sps\OverrideInterface
   */
  public function __construct(\Drupal\sps\StorageControllerInterface $cache_controller, \Drupal\sps\OverrideInterface $override) {
    $this->setCacheController($cache_controller);
    $this->setOverride($override);
  }

  /**
   * SiteState::setCacheController
   * @PARAM $controller: an object that implements Drupal\sps\StorageControllerInterface
   */ 
  protected function setCacheController(\Drupal\sps\StorageControllerInterface $controller) {
    $this->cache_controller = $controller;
  }

  /**
   * SiteState::setOverride
   * @PARAM $override: an object that implements Drupal\sps\OverrideInterface
   */ 
  protected function setOverride(\Drupal\sps\OverrideInterface $override) {
    $this->override = $override;
  }

  /**
   * SiteState:getOverrides
   *
   * @RETURN array of assoc arrays
   */
  public function getOverride() {
    if(!$this->cache_controller->hasValidCache()) {
      $this->cacheOverride();
    }
    return $this->cache_controller->getMap();
  }

  /**
   * SiteState::cacheOverrides
   */
  protected function cacheOverride() {
    $this->cache_controller->save($this->override->getOverrides());
  }


}


