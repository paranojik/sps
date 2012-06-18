<?php

namespace Drupal\sps\Plugin;

use Drupal\sps\Plugins;
use Drupal\sps\Exception\InvalidPluginException;

class PluginFactory {
  protected $pluginMap;
  protected $pluginTypeMap;

  /**
   * @param $type
   *
   * @return PluginTypeInterface
   */
  public function getPluginType($type) {
    if (!isset($this->pluginTypeMap[$type])) {
      $this->pluginTypeMap[$type] = $this->loadPluginType($type);
    }

    return $this->pluginTypeMap[$type];
  }

  /**
   * Load a PluginTypeClass
   *
   * @param $type string
   * @return @return PluginTypeInterface
   */
  protected function loadPluginType($type) {
    $class_name = "Drupal\\sps\\Plugin\\{$type}";
    $plugin = new $class_name();

    if (!($plugin instanceof PluginTypeInterface)) {
      throw new InvalidPluginException("Plugin of type $type was not loaded");
    }

    return $plugin;
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
    if (!isset($this->pluginMap[$type][$plugin_name])) {
      $this->pluginTypeMap[$type][$plugin_name] = $this->loadPlugin($type, $plugin_name);
    }

    return $this->pluginTypeMap[$type][$plugin_name];
  }

  /**
   * Load a Plugin Object
   *
   * @param $type
   * @param $plugin_name
   *
   * @return PluginInterface
   */
  protected function loadPlugin($type, $plugin_name) {
    $class_name = "Drupal\\sps\\Plugins\\{$type}\\{$plugin_name}";

    $class = new $class_name();

    $interface_name = "Drupal\\sps\\Plugin\\{$type}\\{$type}Interface";
    if (!($class instanceof PluginInterface) && !($class instanceof $interface_name)) {
      throw new InvalidPluginException("Plugin of type $type named $plugin_name was not loaded");
    }

    return $class;
  }

  /**
   * @param $type string
   * @return PluginCollection
   */
  public function getPlugins($type) {
    return new PluginCollection($type);
  }
}
