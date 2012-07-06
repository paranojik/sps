<?php

namespace Drupal\sps;

use Drupal\sps\Exception\InvalidPluginException;
use Drupal\sps\Exception\ClassLoadException;
use Drupal\sps\Exception\DoesNotImplementException;


/**
 * The plugin factory will load the plugins objects and info
 */
class PluginFactory implements PluginControllerInterface {
  // Array of plugin info
  protected $plugin_info = array();
  // The info array for the plugin type info
  protected $plugin_type_info = array();

  public function __construct() {
    $this->loadPluginTypeInfo();
  }

  /**
   * get meta info on a plugin
   *
   * @param $plugin_type
   *   the type of plugin as defined in hook_sps_plugin_types_info
   * @param $name
   *   the name of the plugin as defined in hook_sps_PLUGIN_TYPE_plugin_info;
   *
   * @throws \Drupal\sps\Exception\InvalidPluginException
   * @return mixed
   *  array of meta data for the plugin or an array of plugin arrays
   */
  public function getPluginInfo($plugin_type, $name = NULL) {
    if (empty($this->plugin_type_info[$plugin_type])) {
      throw new InvalidPluginException("Plugin Type $plugin_type does not exist");
    }

    $this->loadPluginInfo($plugin_type);

    if (isset($name)) {
      if (!isset($this->plugin_info[$plugin_type][$name])) {
        throw new InvalidPluginException("Plugin $name for Plugin type $plugin_type does not exist");
      }

      return $this->plugin_info[$plugin_type][$name];
    }

    return $this->plugin_info[$plugin_type];
  }


  /**
   * factory for building a plugin object
   *
   * @param $type
   *   the type of plugin as defined in hook_sps_plugin_types_info
   * @param $name
   *   the name of the plugin as defined in hook_sps_PLUGIN_TYPE_plugin_info;
   * @param \Drupal\sps\Manager $manager
   *
   * @return mixed
   *   An instance of the plugin object
   *
   * @throws \Drupal\sps\Exception\InvalidPluginException
   * @throws \Drupal\sps\Exception\ClassLoadException
   * @throws \Drupal\sps\Exception\DoesNotImplementException
   */
  public function getPlugin($type, $name, Manager $manager) {
    $plugin_type_info = $this->getPluginInfo($type);
    $plugin_info = $this->getPluginInfo($type, $name);

    $class_name = isset($plugin_info['class']) ? $plugin_info['class'] : $plugin_type_info['class'];

    try {
      $plugin_obj = new $class_name($plugin_info['instance_settings'], $manager);
    }
    catch (\Exception $e) {
      throw new ClassLoadException("Plugin $name was not loaded");
    }

    if (!(self::checkInterface($plugin_obj, $plugin_type_info['interface'])
      && self::checkInterface($plugin_obj, "Drupal\\sps\\Plugins\\PluginInterface"))) {

      throw new DoesNotImplementException("Plugin $name was not using the correct interface");
    }

    return $plugin_obj;
  }

  /**
   * Load the Plugin info into the objects cache
   *
   * @param $plugin_type
   *
   * @return PluginFactory
   *  self
   */
  protected function loadPluginInfo($plugin_type) {
    if (empty($this->plugin_info[$plugin_type])) {
      foreach (module_implements("sps_{$plugin_type}_plugins") as $module) {
        $function = "{$module}_sps_{$plugin_type}_plugins";
        $module_infos = $function();

        foreach ($module_infos as $plugin_name => $plugin_info) {
          $plugin_info += array(
            'plugin_type' => $plugin_type,
            'module' => $module,
            'name' => $plugin_name,
            'instance_settings' => array(),
          );

          drupal_alter("sps_plugin_info_{$plugin_type}_{$plugin_info['name']}", $plugin_info);
          $this->validatePluginInfo($plugin_info);

          $this->plugin_info[$plugin_type][$plugin_name] = $plugin_info;
        }
      }
    }

    return $this;
  }

  /**
   * Validate a plugin info array
   *
   * @param $plugin_info
   *
   * @return \Drupal\sps\PluginFactory
   *  self
   * @throws \Drupal\sps\Exception\InvalidPluginException
   */
  protected function validatePluginInfo($plugin_info) {
    $required_settings = $this->getPluginTypeInfo($plugin_info['plugin_type'], 'require_settings');
    foreach ($required_settings as $setting) {
      $this->validatePluginInfoElement($plugin_info, $setting);
    }

    module_invoke_all("sps_validate_plugin_info",
      $plugin_info, $plugin_info['plugin_type'],
      $this->getPluginTypeInfo($plugin_info['plugin_type']));

    return $this;
  }

  /**
   * @param $plugin_info
   * @param $element
   *
   * @return \Drupal\sps\PluginFactory
   *  self
   * @throws \Drupal\sps\Exception\InvalidPluginException
   */
  protected function validatePluginInfoElement($plugin_info, $element) {
    if (is_array($element)) {
      foreach ($element as $key => $value) {
        if (isset($plugin_info[$key])) {
          $this->validatePluginInfoElement($plugin_info[$key], $value);
        }
        else {
          throw new InvalidPluginException(
            "Plugin {$plugin_info['name']} does not contain required element $key");
        }
      }
    }
    else {
      if (!isset($plugin_type_info[$element])) {
        throw new InvalidPluginException(
          "Plugin {$plugin_info['name']} does not contain required element $element");
      }
    }

    return $this;
  }

  /**
   * @param $plugin_type
   *  The name of the plugin type
   *
   * @param null|string $key
   *  The element to retrieve
   *
   * @throws \Drupal\sps\Exception\InvalidPluginException
   * @return array
   */
  public function getPluginTypeInfo($plugin_type = NULL, $key = NULL) {
    if (isset($plugin_type)) {
      if (empty($this->plugin_type_info[$plugin_type])) {
        throw new InvalidPluginException("Plugin Type $plugin_type does not exist");
      }

      if (isset($key)) {
        return empty($this->plugin_type_info[$plugin_type][$key]) ?: $this->plugin_type_info[$plugin_type][$key];
      }

      return $this->plugin_type_info[$plugin_type];
    }

    return $this->plugin_type_info;
  }

  /**
   * Load the plugin type info
   *
   *
   * @return PluginFactory
   *  self
   */
  protected function loadPluginTypeInfo() {
    if (empty($this->plugin_type_info)) {
      foreach (module_implements('sps_plugin_types') as $module) {
        $function = $module . '_sps_plugin_types';
        $module_infos = $function();

        foreach ($module_infos as $plugin_type_name => $plugin_type_info) {
          $plugin_type_info += array(
            'module' => $module,
            'name' => $plugin_type_name,
            'require_settings' => array(),
          );

          drupal_alter("sps_plugin_type_info_{$plugin_type_info['name']}", $plugin_type_info);
          module_invoke_all("sps_validate_plugin_type_info", $plugin_type_info);

          $this->plugin_type_info[$plugin_type_name] = $plugin_type_info;
        }
      }
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
   *   the type of plugin as defined in hook_sps_plugin_types_info
   * @param $property
   *   the meta property to compare to the value
   * @param $value
   *   the value to compare to the meta property
   *
   * @return array
   *   an array of meta data for the plugins
   */
  public function getPluginByMeta($type, $property, $value) {
    $this->loadPluginInfo($type);

    $plugin_matches = array();
    foreach ($this->plugin_info as $plugin => $info) {
      if ($this->checkPluginMeta($info, $property, $value)) {
        $plugin_matches[$plugin] = $info;
      }
    }

    return $plugin_matches;
  }

  /**
   * Recursive function to search the meta data
   *
   * @param $plugin_info
   * @param $property
   * @param $value
   *
   * @return bool
   */
  protected function checkPluginMeta($plugin_info, $property, $value) {
    foreach ($plugin_info as $plugin_info_key => $plugin_info_value) {
      if (is_array($plugin_info_value)) {
        return $this->checkPluginMeta($plugin_info[$plugin_info_key], $property, $value);
      }

      if ($plugin_info_key == $property && $plugin_info_value == $value) {
        return TRUE;
      }
      else {
        return FALSE;
      }
    }

    return FALSE;
  }
}
