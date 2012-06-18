<?php

namespace Drupal\sps\Plugin;

interface PluginTypeInterface {

  /**
   * Info about the plugin
   *
   *  returns array(
   *    'name' => Unique name of the plugin type Required
   *    'interface' => Interface name to use.
   *
   *
   * @abstract
   * @return array
   */
  public static function info();

  /**
   * Required elements in the info array
   *
   * @return array
   */
  public static function requiredInfo();
}
