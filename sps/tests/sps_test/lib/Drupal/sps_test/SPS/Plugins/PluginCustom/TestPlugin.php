<?php

namespace Drupal\sps_test\SPS\Plugins\PluginCustom;

use Drupal\sps\Plugin\PluginInterface;

class TestPlugin implements PluginInterface {
  protected $definition = array();

  public function __construct($definition) {
    $this->definition = $definition;
  }

  public function getDefinition($param = NULL) {
    if (isset($param)) {
      return $this->definition[$param];
    }

    return $this->definition;
  }
}
