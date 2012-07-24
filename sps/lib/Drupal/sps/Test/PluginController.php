<?php
namespace Drupal\sps\Test;
class PluginController extends \Drupal\sps\PluginFactory{
  public function setInfo($plugin_info) {
    $this->plugin_info = $plugin_info;
    foreach($plugin_info as $type => $stuff) {
      $this->plugin_type_info[$type] = array( 'name' => $type);
    }
  }

  public function __construct($plugin_info, $manager) {
    $this->setInfo($plugin_info);
  }
  protected function loadPluginInfo($plugin_type) {
    return $this;
  }
  protected function loadPluginTypeInfo() {
    return $this;
  }
  
}
