<?php
namespace Drupal\sps;
interface TableOverrideStorageControllerInterface {
  public function addOverrideJoin($query, $base_alais, $base_id, $overrides_alais);

}
