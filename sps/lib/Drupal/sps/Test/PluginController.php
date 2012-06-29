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
   * @param $type the type of plugin as defined in hook_sps_plugin_types_info
   * @param $name the name of the plugin as defined in hook_sps_PLUGIN_TYPE_plugin_info;
   * @return an array of meta data for the plugin
   */
  public function getPlugin($type, $name, $manager) {
    $info = $this->getPluginInfo($type, $name);
    $class = $info['class'];
    $r = new \ReflectionClass($info['class']);
    return $r->newInstanceArgs(array($info['instance_settings'], $manager));


  }

  /**
   * get meta info on a plugin
   * @param $type the type of plugin as defined in hook_sps_plugin_types_info
   * @param $name the name of the plugin as defined in hook_sps_PLUGIN_TYPE_plugin_info;
   * @return an array of meta data for the plugin or an array of plugin arrays
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
}
