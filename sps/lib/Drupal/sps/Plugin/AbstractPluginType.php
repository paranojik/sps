<?php

namespace Drupal\sps\Plugin;

abstract class AbstractPluginType implements PluginTypeInterface {
  protected $info;

  public function __construct() {
    $this->setUpInfo();
  }

  protected function setUpInfo() {
    $provided_info = self::info();

    $required_keys = self::requiredInfo();
    foreach ($required_keys as $required_key) {
      if (!in_array($required_key, $provided_info)) {
        throw new \Drupal\sps\Exception\InvalidPluginException("The Required Key $required_key was not found");
      }
    }

    $this->info = array(
      'name' => get_class($this),
      'interface' => $provided_info['interface'] ?: "Drupal\\sps\\Plugin\\Type\\{$provided_info['name']}Interface",
    );
  }

  /**
   * Required elements in the info array
   *
   * @return array
   */
  public static function requiredInfo() {
    return array();
  }

  /**
   * Get a info value
   *
   * @param $key
   *
   * @return mixed
   */
  public function getInfoValue($key) {
    return $this->info[$key];
  }

  public function __toString() {
    $class = explode('\\', __CLASS__);
    return end($class);
  }
}
