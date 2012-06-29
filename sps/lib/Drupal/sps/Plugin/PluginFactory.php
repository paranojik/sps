<?php

namespace Drupal\sps\Plugin;

use Drupal\sps\Plugins;
use Drupal\sps\Plugin\PluginTypeInterface;
use Drupal\sps\Exception\InvalidPluginException;
use Drupal\sps\Plugin\PluginType;

class PluginFactory implements  \Drupal\sps\PluginControllerInterface {
  // Array of Collection of plugins
  protected $plugins = array();
  // The info array for the plugin type definitions
  protected $plugin_type_info = array();
  // Array of the Plugin Type Objects
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

    if (empty($this->plugin_type_info[$type])) {
      throw new \Drupal\sps\Exception\InvalidPluginException("Plugin Type $type Does not exist");
    }

    return $this->plugin_type_info[$type];
  }

  /**
   * Load the Plugin Type
   *
   * @param $type string
   *  The name of the plugin type
   *
   * @throws \Drupal\sps\Exception\ClassLoadException
   * @return \Drupal\sps\Plugin\PluginType
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
      try {
        $this->plugin_types[$type] = new $plugin_type_class_name($plugin_info);
      }
      catch (\Exception $e) {
        throw new \Drupal\sps\Exception\ClassLoadException("Plugin Type Class $plugin_type_class_name was not loaded");
      }


    }

    return $this->plugin_types[$type];
  }

  /**
   * Get a Plugin Class
   *
   * @param $type
   * @param $plugin_name
   *
   * @return mixed
   *  The class returned will depend on the plugin type
   */
  public function getPlugin($type, $plugin_name) {
    return $this->checkPluginType($type)->plugins[$type][$plugin_name];
  }

  /**
   * @param $type string
   * @return PluginCollection
   */
  public function getPlugins($type) {
    return $this->checkPluginType($type)->plugins[$type];
  }

  /**
   * Check if the plugin collection is set for a type
   */
  protected function checkPluginType($type) {
    if (empty($this->plugins[$type])) {
      $this->plugins[$type] = $this->loadPluginType($type)->getCollection();
    }

    return $this;
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
    $ref_class = new \ReflectionClass($object);
    if (in_array($interface, $ref_class->getInterfaceNames())) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * get meta info on a plugin
   *
   * @param $type
   *  the type of plugin as defined in hook_sps_plugin_types_info
   * @param $name
   *  the name of the plugin as defined in hook_sps_PLUGIN_TYPE_plugin_info;
   *
   * @return array
   *  an array of meta data for the plugin or an array of plugin arrays
   */
  public function getPluginInfo($type, $name = NULL) {
    // TODO: Implement getPluginInfo() method.
  }}
