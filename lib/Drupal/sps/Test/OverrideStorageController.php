<?php

namespace \Drupal\sps\Test;

class TableOverrideStorageController implements TableOverrideStorageControllerInterface {

  public function addOverrideJoin($query, $base_alais, $base_id, $overrides_alais) {
    $alias = $query->addJoin("LEFT OUTER", 'test_override', $overrides_alais, "$base_alias.$base_id = overrides.id");
    $tables =& $query->getTables();
    $new_tables = array();
    $found_base = FALSE;
    foreach($tables as $key => $table) {

      if ($table['alias'] == $base_alais) {
        $new_tables[$key] = $table;
        $new_tables[$alias] = $tables[$alias];
      }
      else if ($key == $alias) {

      }
      else {
        $new_tables[$key] = $table;
      }
    }
    $tables = $new_tables;

    return $alias;
  }
}

interface TableOverrideStorageControllerInterface {
  public function addOverrideJoin($base_alais, $base_id);
}
