<?php

namespace Drupal\sps\Plugin;

class Plugin implements PluginInterface {
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
