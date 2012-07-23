<?php
namespace Drupal\sps\Plugins\OverrideController;

class TempTableOverrideController implements \Drupal\sps\Plugins\OverrideController\TableOverrideStorageControllerInterface {
  public $table = array();
  public function addOverrideJoin(\SelectQueryInterface $query, $base_alias, $base_id, $overrides_alias) {

    $querys = array();
    foreach($this->table as $row) {
      $querys[] = "SELECT {$row['id']} as id, {$row['revision_id']} as revision_id, '{$row['type']}' as type";
    }
    $table = db_query_temporary(implode(" UNION ", $querys));
    $alias = $query->addJoin("LEFT OUTER", $table, $overrides_alias, "$base_alias.$base_id = $overrides_alias.id");
    $tables =& $query->getTables();
    $new_tables = array();
    $found_base = FALSE;
    foreach($tables as $key => $table) {

      if ($table['alias'] == $base_alias) {
        $new_tables[$key] = $table;
        $new_tables[$alias] = $tables[$alias];
      }
      else if ($key == $alias) {}
      else {
        $new_tables[$key] = $table;
      }
    }
    $tables = $new_tables;
    return $alias;
  }

  public function set($table) {
    $this->table = $table;
  }

  public function __construct(array $config, \Drupal\sps\Manager $manager) {}
}

