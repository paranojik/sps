<?php
namespace Drupal\sps\Plugins\Reaction;

class QueryAlterReaction {
  protected $tables = array();
  protected $alias = array();
  public function __construct($config, $manager) {
    $this->tables = $config['tables'];
  }
  /**
  * runs through a field array and find any thing that use
  * fields on the base table that we want to change to the revision 
  * table
  *
  * @param $fields
  * a field array from a SelectQuery object
  * @return 
  *   NULL
  */
  protected function fieldReplace(&$fields) {
    foreach($fields as &$field) {
      if (($field['table'] == $this->alias['base']) && (in_array($field['field'], array('title', 'status')))){
        $field['table'] = $this->alias['revision'];
      }
    }
  }

  protected function addOverrideTable($query) {
    $override_table = db_query_temporary("SELECT 1 as nid, 5 as vid UNION SELECT 3,7");
    $query->addJoin("LEFT OUTER", $override_table, "overrides", $this->alias['base'] .".nid = overrides.nid");
    $tables =& $query->getTables();
    $new_tables = array();
    $found_base = FALSE;
    foreach($tables as $key => $table) {

      if ($table['alias'] == $this->alias['base']) {
        $new_tables[$key] = $table;
        $new_tables['overrides'] = $tables['overrides'];
      }
      else if ($key == 'overrides') {}
      else {
        $new_tables[$key] = $table;
      }
    }
    $tables = $new_tables;
  }

  /**
  * Recusivily cycle through data replacing base.vid with revision.vid
  *
  * This function finds any ref to the base table vid fields and replace it
  * with a reference to the override vid field. 
  *
  * also if there is a revision table it looks for base table version of title and status
  * and changes them to the revision table
  *
  *
  *
  * @param $data
  *
  * @return 
  */
  protected function recusiveReplace(&$data) {
    foreach($data as $key => &$datum) {
      if (is_array($datum)) {
        $this->recusiveReplace($datum);
      }
      else if (is_object($datum)) {
        $sub_condition =& $datum->conditions();
        $this->recusiveReplace($sub_condition);
      }
      else {
        if($datum !== NULL) {
          $datum = preg_replace("/(".$this->alias['base']."\.vid)/", "COALESCE(overrides.vid, $1)", $datum);
          if(isset($this->alias['revision'])) {
            $datum = preg_replace("/".$this->alias['base']."\.(title|status)/", $this->alias['revision'] .'.$1', $datum);
          }
        }
      }
    }
  }
  protected function setAlias($query) {
    $tables = $query->getTables();
    foreach($tables as $alias => $table) {
      if ($table['table'] == $this->tables['base']) {
        $this->alias['base'] = $alias;
      }
      if ($table['table'] == $this->tables['revision']) {
        $this->alias['revision'] = $alias;
      }
    }
  }
  public function react($query) {
    $this->setAlias($query);
    if($this->alias) {
      $fields =& $query->getFields();
      $this->recusiveReplace($fields);
      $this->fieldReplace($fields);

      $expressions =& $query->getExpressions();
      $this->recusiveReplace($expressions);

      $tables =& $query->getTables();
      $this->recusiveReplace($tables);

      $order =& $query->getOrderBy();
      $this->recusiveReplace($order);

      $where =& $query->conditions();
      $this->recusiveReplace($where);

      $having =& $query->havingConditions();
      $this->recusiveReplace($having);

      $this->addOverrideTable($query);
    }
  }
}
