<?php

namespace Drupal\sps;

class SiteState {
  protected $override;
  protected $cache_controller;

  /**
   * SiteState::__construct
   *
   * @param $cache_controller StorageControllerInterface
   * @param $override OverrideInterface
   */
  public function __construct(StorageControllerInterface $cache_controller, OverrideInterface $override) {
    $this->setCacheController($cache_controller);
    $this->setOverride($override);
  }

  /**
   * SiteState::setCacheController
   *
   * @param $controller StorageControllerInterface
   * @return SiteState
   */
  protected function setCacheController(StorageControllerInterface $controller) {
    $this->cache_controller = $controller;

    return $this;
  }

  /**
   * SiteState::setOverride
   *
   * @param $override OverrideInterface
   * @return SiteState
   */
  protected function setOverride(OverrideInterface $override) {
    $this->override = $override;

    return $this;
  }

  /**
   * SiteState:getOverrides
   *
   * @param array
   *   of assoc arrays
   */
  public function getOverride() {
    if(!$this->cache_controller->hasValidCache()) {
      $this->cacheOverride();
    }
    return $this->cache_controller->getMap();
  }

  /**
   * SiteState::cacheOverrides
   *
   * @return SiteState
   */
  protected function cacheOverride() {
    $this->cache_controller->save($this->override->getOverrides());

    return $this;
  }
}


