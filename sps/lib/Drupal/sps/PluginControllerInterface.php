<?php
namespace Drupal\sps;

interface PluginControllerInterface {
  /**
   * factory for building a plugin object
   *
   * @param $type the type of plugin as defined in hook_sps_plugin_types_info
   * @param $name the name of the plugin as defined in hook_sps_PLUGIN_TYPE_plugin_info;
   * @return an array of meta data for the plugin
   */
  public function getPlugin($type, $name, $manager);

  /**
   * get meta info on a plugin
   * @param $type the type of plugin as defined in hook_sps_plugin_types_info
   * @param $name the name of the plugin as defined in hook_sps_PLUGIN_TYPE_plugin_info;
   * @return an array of meta data for the plugin or an array of plugin arrays
   */
  public function getPluginInfo($type, $name=NULL);
}
