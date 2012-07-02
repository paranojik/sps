<?php

namespace Drupal\sps\Plugins;

interface PluginInterface {
  /**
   * the construct that is expect by the plugin system
   * @Param setting 
   * @param $manager an object of class Drupal\sps\Manager
   */
  public function __contructor($settings, $manager);
}
