<?php

namespace Drupal\sps\Plugin;

interface PluginTypeInterface {
  /**
   * Constrcutor
   *
   * @param $definition
   *  The plugin definition
   */
  public function __construct($definition);

  /**
   * Get all of the plugins
   *
   * @return \Drupal\sps\Plugin\PluginCollection
   */
  public function getCollection();

  /**
   * Get a value for the definition
   */
  public function getDefinition($param = NULL);

  /**
   * Get the info for all of plugins
   */
  public function getPluginInfo($plugin_name = NULL);

}
