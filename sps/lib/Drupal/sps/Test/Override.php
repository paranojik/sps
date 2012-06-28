<?php
namespace Drupal\sps\Test;
class Override extends Drupal\sps\Override\Override {
  public $table = array();

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
