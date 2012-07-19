<?php

namespace Drupal\sps;

class SiteState {
  protected $controller_key = "sps_override_controller";
  protected $condition;
  protected $overrides;
  protected $override_controller;

  /**
   * Constructor for SiteState
   *
   * @param \Drupal\sps\Plugins\ConditionInterface $condition
   *  The RootConditions that is used by the site state
   */
  public function __construct(Plugins\ConditionInterface $condition) {
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
   * Store the Root Condition
   *
   * @param Plugins\ConditionInterface $condition
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


