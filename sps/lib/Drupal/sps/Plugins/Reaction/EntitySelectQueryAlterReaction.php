<?php
namespace Drupal\sps\Plugins\Reaction;

class EntitySelectQueryAlterReaction {
  protected $entities = array();
  protected $alias = array();

  /**
   * 
   */
  /**
  * The EntitySelectQueryAlterReaction can work with any number of Entites.
  * Each entity must be shown in the $config['entities'] array.  It will 
  * then alter any query (it can touch) and use the override revision id
  * instead of the one in base
  *
  * @param $config
  *  has one entry entites which list info about each entity for which 
  *  query should be altered
  * enities = array(
  *   array(
  *     'base_table' =>  'node', //the base table of the entity
  *     'revision_table' => 'node_revision', //The table for the revision
  *     'revision_fields' => array('uid', 'sticky', 'promote', 'status'),
  *     // The fields that are in both base and entity that should pull
  *     // from revision if we are altering the query
  *     'base_id' => 'nid',  // the base table id 
  *     'revision_id => 'vid'// the revision table's id in the base table
  *   )
  * )
  * @param $manager
  *   the Current Manager Object
  * @return 
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
  *   @see EntitySelectQueryAlterReaction::__construct()
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
  * NULL
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
  *   This is data form a query
  *
  * @return 
  * NULL
  */
  protected function recusiveReplace(&$data, $alias) {
    $reorder = array();
    foreach($data as $key => &$datum) {

      //check to see if we need to rewrite the keys.
      foreach($this->entities as $entity) {
        if(isset($alias[$entity['revision_table']])) {
          $new_key = preg_replace("/".$alias[$entity['base_table']]."\.(".implode("|", $entity['revision_fields']).")/", $alias[$entity['revision_table']] .'.$1', $key);
          if($new_key != $key) {
            $reorder[$key] = $new_key;
          }
        }
      }

      //if an array lets run the kids
      if (is_array($datum)) {
        $this->recusiveReplace($datum, $alias);
      }
      //@TODO this is for exceptions basicly
      else if (is_object($datum)) {
        if(in_array("QueryConditionInterface", class_implements($datum))){  
          $sub_condition =& $datum->conditions();
          $this->recusiveReplace($sub_condition, $alias);
        }
      }
      //ok we have a single datum lets work on it.
      else {
        if($datum !== NULL) {
          foreach($this->entities as $entity) {
            //replace revision_id 
            $datum = preg_replace("/(".$alias[$entity['base_table']]."\.{$entity['revision_id']})/", "COALESCE(". $this->getOverrideAlias($entity) .".{$entity['revision_id']}, $1)", $datum);
            if(isset($alias[$entity['revision_table']])) {


              $datum = preg_replace("/".$alias[$entity['base_table']]."\.(".implode("|", $entity['revision_fields']).")/", $alias[$entity['revision_table']] .'.$1', $datum);
            }
          }
        }
      }
    }
    if($reorder) {
      $data = $this->keyRename($data, $reorder);
    }
  }

  /**
  * helper function to rename keys in an assoc array but keep the order
  *
  * @param $data
  *   array
  * @param $moves
  *   array of old key => new key items
  *
  * @return 
  */
  protected function keyRename($data, $moves) {
    $new_data = array();
    foreach($data as $key => $datum) {
      if (isset($moves[$key])) {
        $new_data[$moves[$key]] = $datum;
      }
      else {
        $new_data[$key] = $datum;
      }
    }
    return $new_data;
  }

  /**
  * Check a query for tables that match our entities tables
  * and return the alais for everyone that was found
  *
  * @param $query
  *   a SelectQuery is expected
  *
  * @return 
  *   and array whose keys are table names and values are alias
  */
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
  
  /**
  * Alter a query to use the overridden revision id as well as 
  * revision fields.
  *
  * @param $query
  *   This expect a SelectQuery Object to alter
  * @param $override_controller
  *   This is an override controller to use to find 
  *   override data
  *
  * @return 
  *   NULL
  */
  public function react($query, $override_controller) {
    //exit prematurly if we ha a no alter tag
    if($query->hasTag(SPS_NO_ALTER_QUERY_TAG)) {
      return;
    }
    $alias = $this->extractAlias($query);
    if($alias) {
      $this->addOverrideTable($query, $override_controller);
      $fields =& $query->getFields();
      $this->fieldReplace($fields, $alias);

      $tables =& $query->getTables();
      $this->recusiveReplace($tables, $alias);

      $where =& $query->conditions();
      $this->recusiveReplace($where, $alias);

      $expressions =& $query->getExpressions();
      $this->recusiveReplace($expressions, $alias);

      $order =& $query->getOrderBy();
      $this->recusiveReplace($order, $alias);

      $group =& $query->getGroupBy();
      $this->recusiveReplace($group, $alias);

      $having =& $query->havingConditions();
      $this->recusiveReplace($having, $alias);
      /* 
      $this->recusiveReplace($fields);

      */
    }
  }
}
