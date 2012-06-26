<?php
namespace Drupal\sps\Test;
class StorageController implements \Drupal\sps\StorageControllerInterface{
  protected $table;
  public function save($table) {
    $this->table = $table;
  }
  public function getRevisionId($type, $id) {
    return array_reduce($this->table, function($result, $item) use ($type, $id) { 
      if(($item['id'] == $id) && ($item['type'] == $type)) {
        $result = $item['revision_id'];
      }
      return $result;
    });
  }
  public function getMap() { return $this->table; }
  public function hasValidCache() { return (bool) $this->table ;}

}
