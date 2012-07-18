<?php
namespace Drupal\sps;

interface TableOverrideStorageControllerInterface {
  public function addOverrideJoin(\SelectQueryInterface $query, $base_alais, $base_id, $overrides_alais);

}
