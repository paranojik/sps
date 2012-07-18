<?php

namespace Drupal\sps;

class SiteState {
  protected $controller_key = "sps_override_controller";
  protected $override;
  protected $override_controller;

  /**
  * Constructor for SiteState
  *
  * @param $controller
  *   The StorageController to use for storing overrides
  * @param $override
  *   The Override to use to generate overrides
  */
  public function __construct(Plugins\ConditionInterface $condition) {
    //$this->setOverrideController($controller);
    $this->setCondition($condition);
  }

  /**
  * Set the Controller for storing overrrides
  *
  * @param $controller
  *   The StorageController to use for storing Overrides
  *
  * @return \Drupal\sps\SiteState
  *   Self
  */
  protected function setOverrideController(StorageControllerInterface $controller) {
    $this->override_controller = $controller;

    return $this;
  }

  /**
   * Store the Override to use for generating overrides
   *
   * @param $override
   *   The Override to use to generate overrides
   *
   * @return \Drupal\sps\SiteState
   *   Self
   */
  protected function setCondition(Plugins\ConditionInterface $condition) {
    $this->condition = $condition;

    return $this;
  }

  /**
  * Retrieve Stored Overrides
  *
  * @return array
  *   Array of overrides
  *   @TODO make this a iterator?
  */
  public function getOverride() {
    if(!$this->override_controller->exists($this->controller_key)) {
      $this->cacheOverride();
    }
    return $this->override_controller->get($this->controller_key);
  }

  /**
   * Generate overrides from the stored Override and save it to the Override Controller
   *
   * @return \Drupal\sps\SiteState
   *   Self
   */
  protected function cacheOverride() {
    $this->override_controller->set($this->controller_key, $this->override->getOverrides());

    return $this;
  }
}


