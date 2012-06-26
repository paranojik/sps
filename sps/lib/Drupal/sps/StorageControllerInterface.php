<?php

namespace Drupal\sps;

interface StorageControllerInterface {
  public function save($table);
  public function getRevisionID($type, $id);
  public function getMap();
  public function hasValidCache();
}
