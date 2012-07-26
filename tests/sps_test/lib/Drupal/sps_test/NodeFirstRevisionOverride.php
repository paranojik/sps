<?php
namespace Drupal\sps_test;

use \Drupal\sps\Plugins\Override\Override;

abstract class NodeDateOverride extends Override {
  protected $timestamp;

  /**
   *  Create our NodeDateOverride.
   *  Defaults timestamp to jan 1, 1970.
   */
  public function __construct(array $settings, \Drupal\sps\Manager $manager) {
    parent::__construct($settings, $manager);
  }

  /**
   * Set the data for this override.
   *
   * This method should be called before get overrides and provides the
   * data which the override will use to find the available overrides.
   *
   * @param $variables
   *  A unix timestamp
   *
   * @return \Drupal\sps\Override\NodeDateOverride
   *           Self
   */
  public function setData($variables) {
    $this->first = $variables;

    return $this;
  }

  /**
   * Overrides Override::getDataConsumerApi()
   * Provides the data type for this override.
   *
   * @return string
   *   A string defining the data type
   */
  public function getDataConsumerApi() {
    return 'bool';
  }
}
