<?php

namespace Drupal\sps\Plugins;

interface PluginInterface {
  /**
   * the construct that is expect by the plugin system
   * @Param $setting
   * @param $manager Drupal\sps\Manager
   */
  public function __construct($settings, \Drupal\sps\Manager $manager);
}
