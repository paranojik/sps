<?php

namespace Drupal\sps\Plugin;

use Drupal\sps\Plugin\PluginTypeInterface;
use Drupal\sps\Plugin\PluginCollection;

class PluginType implements PluginTypeInterface {
  protected $definition;
  protected $plugin_definitions;

  /**
   * Constrcutor
   *
   * @param $definition
   *  The plugin definition
   */
  public function __construct($definition) {
    $this->definition = $definition;
  }

  /**
   * Get all of the plugins
   *
   * @return \Drupal\sps\Plugin\PluginCollection
   */
  public function getCollection() {
    return new PluginCollection($this);
  }

  /**
   * Get a value for the definition
   */
  public function getDefinition($param = NULL) {
    if (isset($param)) {
      return $this->definition[$param];
    }
    else {
      return $this->definition;
    }
  }

  /**
   * Get the info for all of plugins
   */
  public function getPluginInfo($plugin = NULL) {
    if (empty($this->plugin_definitions)) {
      $plugin_type_name = $this->getDefinition('name');
      foreach (module_implements("sps_{$plugin_type_name}_plugins") as $module) {
        $function = "{$module}_sps_{$plugin_type_name}_plugins";
        $module_infos = $function();

        foreach ($module_infos as $plugin_name => $plugin_name_info) {
          $plugin_name_info += array(
            'module' => $module,
            'name' => $plugin_name,
            'class' => $this->getDefinition('plugin_class'),
            'defaults' => array(),
          );

          $this->plugin_definitions[$plugin_name] = $plugin_name_info;
        }
      }
    }

    if (empty($plugin)) {
      return $this->plugin_definitions;
    }

    return $this->plugin_definitions[$plugin];
  }
}
