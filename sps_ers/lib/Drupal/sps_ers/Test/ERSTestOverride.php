<?php
namespace Drupal\sps_ers\Test;

<<<<<<< HEAD
use \Drupal\sps_ers\ERSOverride;
=======
use \Drupal\sps\Plugins\Override\ERSOverride;
>>>>>>> e7c3a54d8dcbc0d0e3db623214e783ed2de8705f

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