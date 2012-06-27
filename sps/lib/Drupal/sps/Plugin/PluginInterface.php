<?php

namespace Drupal\sps\Plugin;

interface PluginInterface {
  /**
   * @param $definition
   *  The plugin definition
   */
  public function __construct($definition);

  /**
   * Get a value from the Plugin Definition
   *
   * @param null|string $name
   *
   * @return mixed
   */
  public function getDefinition($param = NULL);

}
