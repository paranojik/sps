<?php
namespace Drupal\sps\Test;
class Override implements \Drupal\sps\OverrideInterface{
  public $table = array();
  public function __construct($table) {
    $this->table = $table;
  }
  public function getOverrides() {
    return $this->table;
  }
}
