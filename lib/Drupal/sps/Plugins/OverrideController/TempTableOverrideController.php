<?php
namespace Drupal\sps\Plugins\OverrideController;

class TempTableOverrideController implements \Drupal\sps\Plugins\OverrideController\TableOverrideStorageControllerInterface {
  public $table = array();

  /**
  * Create a temp table of the override data we have
  *
  * @param $type
  *   the type of overrides that should be in the temp table
  *
  * @return 
  *   name of the temp table
  */
  protected function createTempTable($type) {
   $querys = array();
    foreach($this->table as $row) {
      if($row['type'] == $type) {
        $columns = array();
        foreach($this->getPropertyMap() as $property => $field) {
          $value = isset($row[$property]) ? $row[$property] : 'NULL';
          $columns[] = "$value as $field";
        }
      }
      $querys[] = "SELECT " . implode(",", $columns);

    }
    // if we do not have any overrides we need to add a dummy one so that the temp table can be created
    if(empty($querys)) {
      $querys[] = "SELECT 0 as id";
    }
    print_r(implode(" UNION ", $querys));
    return  db_query_temporary(implode(" UNION ", $querys));
  }

  /**
  * @brief 
  *
  * @param $query
  *   The query to alter by adding the override table
  * @param $base_alais
  *   The base table to join on
  * @param $base_id
  *   The base id to use in the join
  * @param $overrides_alais
  *   The alais to use for the override table
  * @param $type
  *   The type of entity to be overriding
  *
  * @return 
  *   Name of table alias
  */
  public function addOverrideJoin(\SelectQueryInterface $query, $base_alias, $base_id, $overrides_alias, $type) {

    $table = $this->createTempTable($type);
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
    foreach($table as $row) {
      
    }
  }

  /**
  * @brief 
  *
  * @return 
  * A dictionary of properties and the field name on the override table
  *
  */
  public function getPropertyMap() {
    $properties = array_keys(call_user_func_array('array_merge', $this->table));
    $properties = array_combine($properties, $properties);
    unset($properties['type']);
    unset($properties['id']);
    return $properties;
  
  }

  public function __construct(array $config, \Drupal\sps\Manager $manager) {}
}

