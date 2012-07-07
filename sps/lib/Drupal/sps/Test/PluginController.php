<?php
namespace Drupal\sps\Test;

class PluginController implements \Drupal\sps\PluginControllerInterface{
  protected $infos = array();
  public function __construct($infos) {
    $this->infos = $infos;
  }

  /**
   * factory for building a plugin object
   *
   * @param $type
   *   the type of plugin as defined in hook_sps_plugin_types_info
   * @param $name
   *   the name of the plugin as defined in hook_sps_PLUGIN_TYPE_plugin_info;
   * @return
   *   an array of meta data for the plugin
   */
  public function getPlugin($type, $name, \Drupal\sps\Manager $manager) {
    $info = $this->getPluginInfo($type, $name);
    $class = $info['class'];
    $r = new \ReflectionClass($info['class']);
    return $r->newInstanceArgs(array($info['instance_settings'], $manager));


  }

  /**
   * get meta info on a plugin
   *
   * @param $type
   *   the type of plugin as defined in hook_sps_plugin_types_info
   * @param $name
   *   the name of the plugin as defined in hook_sps_PLUGIN_TYPE_plugin_info;
   * @return
   *   an array of meta data for the plugin or an array of plugin arrays
   */
  public function getPluginInfo($type, $name=NULL) {
    $type = $this->infos[$type];
    if($name) {
      return $type[$name];
    }
    else {
      return $type;
    }
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
   * @return
   *   an array of meta data for the plugins
   */
  public function getPluginByMeta($type, $property, $value) {
    $plugins = $this->getPluginInfo($type);
    return array_filter(function($plugin) use($property, $value) { return (isset($plugin[$property]) && ($plugin[$property] == $value));}, $plugins);
  }

}
