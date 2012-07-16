<?php
namespace Drupal\sps\Plugins\Reaction;

class QueryAlterReaction {
  protected $entities = array();
  protected $alias = array();

  /**
   * enties = array(
   *   array(
   *     base
   *     revision
   *     revision_fields
   *     base_id
   *     revision_id
   * )
   */
  public function __construct($config, $manager) {
    $this->entities = $config['entities'];
  }
  /**
  * Alters a fields array change the table in which fields should be pulled
  *
  * For each entity, all fields in revision_field have the table change to the 
  * revision table
  *
  * For each entity the entity_id field's table is changed to the pverrides table
  *
  * @param $fields
  * a field array from a SelectQuery object
  *
  * @param Array $alias
  *   a map of the querys alias to table
  * @return 
  *   NULL
  */
  protected function fieldReplace(&$fields, $alias) {
    foreach($fields as &$field) {
      foreach($this->entities as $entity) {
        //check for revision fields in base
        if (
          isset($alias[$entity['revision_table']]) &&
          ($field['table'] == $alias[$entity['base_table']]) && 
          (in_array($field['field'], $entity['revision_fields']))
          ){
          $field['table'] = $alias[$entity['revision_table']];
        }

        //Check for vid in base
        if (
          ($field['table'] == $alias[$entity['base_table']]) && 
          ($field['field'] == $entity['revision_id'])
          ){
          $field['table'] = $this->getOverrideAlias($entity);
        }
      }
    }
  }

  /**
  * construct on override table alias 
  *
  * @param $entity
  *   an array representing an alias 
  *   @see QueryAlterReaction::__construct()
  *
  * @return 
  *   an alias for the overrides table;
  */
  protected function getOverrideAlias($entity) {
    return $entity['base_table'] . "_overrides";
  }

  /**
  * add override tables to a query
  *
  * This is mostly a passthrough to the override_controller
  *
  * @param $query
  *   The query to alter
  * @param $override_controller
  *   The Override controller that will provide the override tables;
  *
  * @return 
  */
  protected function addOverrideTable($query, $override_controller) {
    $alias = $this->extractAlias($query);
    foreach($this->entities as $entity) {
      $base_alias = $alias[$entity['base_table']];
      $base_id = $entity['base_id'];
      $overrides_alias = $this->getOverrideAlias($entity);
      if($base_alias) {
        $override_controller->addOverrideJoin($query, $base_alias, $base_id, $overrides_alias);
      }
    }
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

  protected function extractAlias($query) {
    $tables = $query->getTables();
    $aliases = array();
    foreach($tables as $alias => $table) {
      foreach($this->entities as $entity) {
        if ($table['table'] == $entity['base_table']) {
          $aliases[$entity['base_table']] = $alias;
        }
        if ($table['table'] == $entity['revision_table']) {
          $aliases[$entity['revision_table']] = $alias;
        }
      }
    }
    return $aliases;
  }
  

  public function react($query, $override_controller) {
    $alias = $this->extractAlias($query);
    if($alias) {
      $this->addOverrideTable($query, $override_controller);
      $fields =& $query->getFields();
      $this->fieldReplace($fields, $alias);
      /* 
      $this->recusiveReplace($fields);

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
      */
    }
  }
}
