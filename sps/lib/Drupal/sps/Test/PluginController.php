<?php
namespace Drupal\sps\Test;
class PluginController extends \Drupal\sps\PluginFactory{
  public function __construct($plugin_info) {
    $this->plugin_info = $plugin_info;
    foreach($plugin_info as $type => $stuff) {
      $this->plugin_type_info[$type] = array( 'name' => $type);
    }
  }
  protected function loadPluginInfo($plugin_type) {
    return $this;
  }
  protected function loadPluginTypeInfo() {
    return $this;
  }
  
}
