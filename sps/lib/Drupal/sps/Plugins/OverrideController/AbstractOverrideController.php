<?php
namespace Drupal\sps\Plugins\OverrideController;

abstract class AbstractOverrideController implements \Drupal\sps\Plugins\OverrideControllerInterface,\Drupal\sps\Plugins\PluginInterface  {

  public function validateRow($row) {
    if (!isset($row['revision_id'])) {
      throw new \Drupal\sps\Exception\InvalidOverrideRowException("Override row must have revision_id field");
    }
    if (!isset($row['id'])) {
      throw new \Drupal\sps\Exception\InvalidOverrideRowException("Override row must have id field");
    }
    if (!isset($row['type'])) {
      throw new \Drupal\sps\Exception\InvalidOverrideRowException("Override row must have type field");
    }
  
  }

}

