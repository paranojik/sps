<?php

namespace Drupal\sps\Plugin;

use Drupal\sps\Plugins;
use Drupal\sps\Plugin\PluginTypeInterface;
use Drupal\sps\Exception\InvalidPluginException;
use Drupal\sps\Plugin\PluginType;

class PluginFactory {
  // Array of Collection of plugins
  protected $plugins = array();
  // The info array for the plugin type definitions
  protected $plugin_type_info = array();
  // Array of the Plugin Type Objectgs
  protected $plugin_types = array();

  /**
   * Load the Plugin Type Info
   */
  public function getPluginTypeInfo($type = NULL) {
    if (empty($this->plugin_type_info)) {
      foreach (module_implements('sps_plugin_types') as $module) {
        $function = $module . '_sps_plugin_types';
        $module_infos = $function();

        foreach ($module_infos as $plugin_type_name => $plugin_type_info) {
          $plugin_type_info += array(
            'module' => $module,
            'name' => $plugin_type_name,
            'class' => "Drupal\\sps\\Plugin\\PluginType",
            'plugin_class' => "Drupal\\sps\\Plugin\\Plugin",
            'interface' => "Drupal\\sps\\Plugin\\PluginInterface",
            'defaults' => array(),
          );
          $this->plugin_type_info[$plugin_type_name] = $plugin_type_info;
        }
      }
    }

    return $this->plugin_type_info[$type];
  }

  /**
   * Load the Plugin Type
   *
   * @return PluginType
   */
  public function loadPluginType($type) {
    if (empty($this->plugin_types[$type])) {
      $plugin_info = $this->getPluginTypeInfo($type);

      if (class_exists($plugin_info['class']) &&
          self::checkInterface($plugin_info['class'],  "Drupal\\sps\\Plugin\\PluginTypeInterface")) {

        $plugin_type_class_name = $plugin_info['class'];
      }
      else {
        $plugin_type_class_name = "Drupal\\sps\\Plugin\\PluginType";
      }

      $this->plugin_types[$type] = new $plugin_type_class_name($plugin_info);
    }

    return $this->plugin_types[$type];
  }

  /**
   * Get a Plugin Class
   *
   * @param $type
   * @param $plugin_name
   *
   * @return PluginTypeInterface
   */
  public function getPlugin($type, $plugin_name) {
    $this->checkPluginType($type);
    return $this->plugins[$type][$plugin_name];
  }

  /**
   * @param $type string
   * @return PluginCollection
   */
  public function getPlugins($type) {
    $this->checkPluginType($type);
    return $this->plugins[$type];
  }

  /**
   * Check if the plugin collection is set for a type
   */
  protected function checkPluginType($type) {
    if (empty($this->plugins[$type])) {
      $this->plugins[$type] = $this->loadPluginType($type)->getCollection();
    }
  }

  /**
   * @static
   *
   * @param $object
   * @param $interface
   *
   * @return bool
   */
  public static function checkInterface($object, $interface) {
    $ref_class = new ReflectionClass($object);
    if (in_array($interface, $ref_class->getInterfaceNames())) {
      return TRUE;
    }
    return FALSE;
  }
}
