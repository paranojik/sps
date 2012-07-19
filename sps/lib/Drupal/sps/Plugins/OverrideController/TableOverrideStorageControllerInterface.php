<?php
namespace Drupal\sps\Plugins\OverrideController;

interface TableOverrideStorageControllerInterface implements OverrideControllerInterface {
  public function addOverrideJoin(\SelectQueryInterface $query, $base_alais, $base_id, $overrides_alais);

}
