<?php

namespace Drupal\sps\Plugin;

interface PluginTypeInterface {
  /**
   * Constrcutor
   *
   * @param $definition
   *  The plugin Type definition
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
   *
   * @return array
   */
  public function getPluginInfo($plugin_name = NULL);

}
