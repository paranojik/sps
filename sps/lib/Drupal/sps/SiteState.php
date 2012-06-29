<?php

namespace Drupal\sps;

class SiteState {
  protected $controller_key = "sps_override_controller";
  protected $override;
  protected $override_controller;

  /**
   * SiteState::__construct
   *
   * @param $cache_controller StorageControllerInterface
   * @param $override OverrideInterface
   */
  public function __construct(StorageControllerInterface $controller, Plugins\OverrideInterface $override) {
    $this->setOverrideController($controller);
    $this->setOverride($override);
  }

  /**
   * SiteState::setOverrideController
   *
   * @param $controller StorageControllerInterface
   * @return SiteState
   */
  protected function setOverrideController(StorageControllerInterface $controller) {
    $this->override_controller = $controller;

    return $this;
  }

  /**
   * SiteState::setOverride
   *
   * @param $override OverrideInterface
   * @return SiteState
   */
  protected function setOverride(Plugins\OverrideInterface $override) {
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
    if(!$this->override_controller->is_set($this->controller_key)) {
      $this->cacheOverride();
    }
    return $this->override_controller->get($this->controller_key);
  }

  /**
   * SiteState::cacheOverrides
   *
   * @return SiteState
   */
  protected function cacheOverride() {
    $this->override_controller->set($this->controller_key, $this->override->getOverrides());

    return $this;
  }
}


