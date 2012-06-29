<?php
namespace Drupal\sps\Test;
class Override extends \Drupal\sps\Plugins\Override\Override {
  public $table = array();
  public function __construct($settings, $manager) {
  }

  public function getOverrides() {
    return $this->table;
  }

  public function setData($table) {
    $this->table = $table;
  }

  public function getDataConsumerApi() {
    return 'test';
  }
}
