<?php

namespace Drupal\sps;

class SiteState {
  protected $override;
  protected $cache_controller;
  
  /**
   * SiteState::__construct
   *
   *
   * PARAM cache_controller: a object that implements Drupal\sps\StorageControllerInterface
   * PARAM override: a object that implements Drupal\sps\OverrideInterface
   */
  public function __construct($cache_controller, $override) {
    $this->setCacheController($cache_controller);
    $this->setOverride($override);
  }

  /*
   * SiteState::setCacheController
   * PARAM controller: an object that implements Drupal\sps\StorageControllerInterface
   */ 
  protected function setCacheController($controller) {
    if($controller instanceof \Drupal\sps\StorageControllerInterface) {
      $this->cache_controller = $controller;
    }
    else { 
      throw new \Drupal\sps\Exception\DoesNotImplementException('Expects Drupal\sps\StorageControllerInterface for $cache_controller');
    }
  }

  /*
   * SiteState::setOverride
   * PARAM override: an object that implements Drupal\sps\OverrideInterface
   */ 
  protected function setOverride($override) {
    $this->override = $override;
    if($override instanceof \Drupal\sps\OverrideInterface) {
      $this->override = $override;
    }
    else { 
      throw new \Drupal\sps\Exception\DoesNotImplementException('Expects Drupal\sps\OverrideInterface for $override');
    }
  }

  /*
   * SiteState:getOverrides
   *
   * RETURN array of assoc arrays
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


