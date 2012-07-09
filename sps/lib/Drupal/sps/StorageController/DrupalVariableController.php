<?php

namespace Drupal\sps\StorageController;

class DrupalVariableController implements \Drupal\sps\StorageControllerInterface {

  protected $prefix = '';

  /**
   * store our prefix for use with variables
   *
   * @param $prefix
   *   A string to add to the front of variables when using variable_set/get
   *
   */
  public function __construct($prefix = '') {
    if ($prefix) {
      $this->prefix = $prefix .'_';
    }
  }

  /**
   * construct the name to use in variable get and sets
   *
   * @param $name string
   *
   * @return string
   *   Used it variable set and gets
   */
  protected function getVariableName($name) {
    return $this->prefix . $name;
  }

  /**
   * Cache away a object
   *
   * @param $name string
   *   A string name use for retrieval
   * @param $cache string
   *   an object to be cached
   *
   * @return \Drupal\sps\Test\StorageController
   *  Self
   */
  public function set($name, $cache) {
    variable_set($this->getVariableName($name), $cache);
    return $this;
  }

  /**
   * Test if we have an object cached
   *
   * This should be less expensive then using get
   *
   * @param $name string
   *   A string name use for retrieval
   *
   * @return bool
   */
  public function exists($name) {
    $variable = variable_get($this->getVariableName($name), NULL);
    return ($variable !== NULL);
  }

  /**
   * Retrieve a cached object
   *
   * @param $name string
   *   A string name use for retrieval
   *
   * @return mixed
   *   the object that
   */
  public function get($name) {
    return variable_get($this->getVariableName($name), NULL);
  }
}
