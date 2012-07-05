<?php

namespace Drupal\sps\Plugins;

interface PluginInterface {
  /**
   * The construct that is expect by the plugin system
   *
   * @param $settings array
   * @param $manager \Drupal\sps\Manager
   */
  public function __construct($settings, $manager);
}
