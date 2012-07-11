<?php
namespace Drupal\sps\Test;
class Manager extends \Drupal\sps\Manager{
  public $state_controller_site_state_key = 'sps_site_state_key';
  public $state_controller;
  public $config_controller;
  public $override_controller;
  public $root_condition;
  public $plugin_controller;
  public function __construct(StorageControllerInterface $state_controller = NULL, StorageControllerInterface $override_controller = NULL, StorageControllerInterface $config_controller = NULL, PluginControllerInterface $plugin_controller = NULL) {


    $state_controller = $state_controller ?: new \Drupal\sps\Test\StorageController();
    $override_controller = $override_controller ?: new \Drupal\sps\Test\StorageController();
    $config_controller = $config_controller ?: new \Drupal\sps\Test\StorageController();
    $plugin_controller = $plugin_controller ?: new \Drupal\sps\Test\PluginController(array());

    $this->setStateController($state_controller)
      ->setOverrideController($override_controller)
      ->setConfigController($config_controller)
      ->setPluginController($plugin_controller);
  }
}
