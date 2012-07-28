<?php
namespace Drupal\sps\Plugins\Reaction;

class EntitySelectQueryAlterReaction implements \Drupal\sps\Plugins\ReactionInterface {
  protected $entities = array();
  protected $alias = array();

  /**
   * The EntitySelectQueryAlterReaction can work with any number of Entities.
   * Each entity must be shown in the $config['entities'] array.  It will
   * then alter any query (it can touch) and use the override revision id
   * instead of the one in base
   *
   * @param $config
   *   has one entry entities which list info about each entity for which
   *   query should be altered
   *   entities = array(
   *     array(
   *       'base_table' =>  'node', //the base table of the entity
   *       'revision_table' => 'node_revision', //The table for the revision
   *       'revision_fields' => array('uid', 'sticky', 'promote', 'status'),
   *       // The fields that are in both base and entity that should pull
   *       // from revision if we are altering the query
   *       'base_id' => 'nid',  // the base table id
   *       'revision_id => 'vid'// the revision table's id in the base table
   *     )
   *   )
   * @param $manager \Drupal\sps\Manager
   *                 the Current Manager Object
   *
   * @return \Drupal\sps\Plugins\Reaction\EntitySelectQueryAlterReaction
   */
  public function __construct(array $config, \Drupal\sps\Manager $manager) {
    $this->entities = $config['entities'];
  }

  /**
   * Alters a fields array change the table in which fields should be pulled
   *
   * For each entity, all fields in revision_field have the table change to the
   * revision table
   *
   * For each entity the entity_id field's table is changed to the overrides table
   *
   * @param $fields
   *   a field array from a SelectQuery object
   *
   * @param Array $alias
   *   a map of the queries alias to table
   *
   * @return \Drupal\sps\Plugins\Reaction\EntitySelectQueryAlterReaction
   *  Self
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

    return $this;
  }

  /**
   * construct on override table alias
   *
   * @param $entity
   *   an array representing an alias
   *
   * @see EntitySelectQueryAlterReaction::__construct()
   *
   * @return string
   *    an alias for the overrides table
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
   * @return \Drupal\sps\Plugins\Reaction\EntitySelectQueryAlterReaction
   *  Self
   */
  protected function addOverrideTable($query, $override_controller) {
    $alias = $this->extractAlias($query);
    foreach($this->entities as $entity) {
      $base_alias = $alias[$entity['base_table']];
      $base_id = $entity['base_id'];
      $overrides_alias = $this->getOverrideAlias($entity);
      if($base_alias) {
        $type = $entity['base_table'];
        $override_controller->addOverrideJoin($query, $base_alias, $base_id, $overrides_alias, $type);
      }
    }

    return $this;
  }


  protected function replaceDatum($datum, $alias, $override_property_map = array()) {
    foreach($this->entities as $entity) {
      //replace revision_id
      $datum = preg_replace("/(".$alias[$entity['base_table']]."\.{$entity['revision_id']})/", "COALESCE(". $this->getOverrideAlias($entity) .".revision_id, $1)", $datum);
        

      //filter the override_property_map to only inlcude items that in revision fields
      $coalesce_revision_map = array_intersect_key($override_property_map, array_fill_keys($entity['revision_fields'], TRUE));
      
      //we have a revision table so lets set values to use that table
      if(isset($alias[$entity['revision_table']])) {
          $datum = $this->replaceTableSwitch($datum, $entity['revision_fields'],$alias[$entity['base_table']], $alias[$entity['revision_table']]);
        // we have propties to override lets give the override table priority
        // on those fields
        if(!empty($coalesce_revision_map)) {
          $datum = $this->replaceCoalesce($datum, $coalesce_revision_map,$alias[$entity['revision_table']],  $this->getOverrideAlias($entity));
        }
      }
      else {
        // we have propties to override lets give the override table priority
        // on those fields
        if(!empty($coalesce_revision_map)) {
          $datum = $this->replaceCoalesce($datum, $coalesce_revision_map,$alias[$entity['base_table']],  $this->getOverrideAlias($entity));
        }
      }
    }
    return $datum;
  }

  /**
   * Recursivily cycle through data replacing base.vid with revision.vid
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
   * @param $alias
   *
   * @return  EntitySelectQueryAlterReaction
   *  Self
   */
  protected function recusiveReplace(&$data, $alias, $override_property_map = array()) {
    $reorder = array();
    foreach($data as $key => &$datum) {

      //check to see if we need to rewrite the keys.
          //$new_key = preg_replace("/".$alias[$entity['base_table']]."\.(".implode("|", $entity['revision_fields']).")/", $alias[$entity['revision_table']] .'.$1', $key);
          $new_key = $this->replaceDatum($key, $alias, $override_property_map);
          if($new_key != $key) {
            $reorder[$key] = $new_key;
      }

      //if an array lets run the kids
      if (is_array($datum)) {
        $this->recusiveReplace($datum, $alias, $override_property_map);
      }
      //@TODO this is for exceptions basicly
      else if (is_object($datum)) {
        if(in_array("QueryConditionInterface", class_implements($datum))){
          $sub_condition =& $datum->conditions();
          $this->recusiveReplace($sub_condition, $alias, $override_property_map);
        }
      }
      //ok we have a single datum lets work on it.
      else {
        if($datum !== NULL) {
          $datum = $this->replaceDatum($datum, $alias, $override_property_map);
        }
      }
    }
    if($reorder) {
      $data = $this->keyRename($data, $reorder);
    }

    return $this;
  }

  protected function replaceTableSwitch($datum, $fields, $base_alias, $revision_alias) {
    return preg_replace("/".$base_alias."\.(".implode("|", $fields).")/", $revision_alias.'.$1', $datum);
  }
  protected function replaceCoalesce($datum, $fields_map, $base_alias, $override_alias) {
    return preg_replace_callback(
      "/".$base_alias."\.(".implode("|", array_keys($fields_map)).")/", 
      function ($m) use ($override_alias, $base_alias, $fields_map) {
        return 'COALESCE('.$override_alias .'.'. $m[1]  .', '. $base_alias.'.'.$fields_map[$m[1]] .')';
      },
      $datum
    );
  }

  /**
   * helper function to rename keys in an assoc array but keep the order
   *
   * @param $data
   *   array
   * @param $moves
   *   array of old key => new key items
   *
   * @return array
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
   * @param $query \SelectQueryInterface
   *   a SelectQuery is expected
   *
   * @return array
   *   an array whose keys are table names and values are alias
   */
  protected function extractAlias(\SelectQueryInterface $query) {
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
  * Move Items in a field for a query to a expression
  *
  * @param $query
  *   the query to manipulate
  * @param $fields_to_move
  *   name of fields to move
  *
  * @return 
  * NULL
  */
  protected function fieldsToExpressions(&$query, $fields_to_move) {
    $fields = & $query->getFields();
    foreach ($fields as $field_alias => $field) {
      if (in_array($field['field'], array_keys($fields_to_move))) {
        $query->addExpression($field['table'] .'.'. $field['field'], $field_alias);
        unset($fields[$field_alias]);
      }
    }
  }

  /**
   * Alter a query to use the overridden revision id as well as
   * revision fields.
   *
   * @param \SelectQueryInterface $query
   *   This expect a SelectQuery Object to alter
   * @param $override_controller
   *   This is an override controller to use to find
   *   override data
   *
   * @return \Drupal\sps\Plugins\Reaction\EntitySelectQueryAlterReaction
   *  Self
   */
  public function react($data, \Drupal\sps\Plugins\OverrideControllerInterface $override_controller) {
    $query = $data->query;
    //exit prematurly if we ha a no alter tag
    if($query->hasTag(SPS_NO_ALTER_QUERY_TAG)) {
      return;
    }
    $alias = $this->extractAlias($query);
    if($alias) {
      $property_map = $override_controller->getPropertyMap();
      $this->addOverrideTable($query, $override_controller);
      $fields =& $query->getFields();
      $this->fieldReplace($fields, $alias, $property_map);

      $expressions =& $query->getExpressions();
      $this->fieldsToExpressions($query,  array_keys($property_map));

      $this->recusiveReplace($expressions, $alias, $property_map);

      $tables =& $query->getTables();
      $this->recusiveReplace($tables, $alias, $property_map);

      $where =& $query->conditions();
      $this->recusiveReplace($where, $alias, $property_map);

      $order =& $query->getOrderBy();
      $this->recusiveReplace($order, $alias, $property_map);

      $group =& $query->getGroupBy();
      $this->recusiveReplace($group, $alias, $property_map);

      $having =& $query->havingConditions();
      $this->recusiveReplace($having, $alias, $property_map);
      /*
      $this->recusiveReplace($fields);

      */
    }
    return $this;
  }
}
