<?php
namespace Drupal\sps\Test;

use \Drupal\sps\Plugins\Override\ERSOverride;

class ERSTestOverride extends ERSOverride {
  /**
   * Override the getOverrides function to do nothing but
   * call processOverrides so we can test it.
   */
  public function getOverrides() {
    return $this->processOverrides();
  }

  /**
   * Provide an easy way to set data for processOverrides to deal with
   */
  public function setResults($data) {
    $this->results = $data;
  }
}