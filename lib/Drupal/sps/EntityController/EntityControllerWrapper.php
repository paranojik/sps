<?php

namespace Drupal\sps\EntityController;


class EntityControllerWrapper implements  \DrupalEntityControllerInterface {
  
  protected $controller;
  protected $info;
  protected $type;
  /**
   * Constructor.
   *
   * @param $entityType
   *   The entity type for which the instance is created.
   */
  public function __construct($entityType) {
    $this->info = entity_get_info($entityType);
    $class = $this->info['controller class base'];
    $this->controller = new $class($entityType);
    $this->type = $entityType;
     
  }

  /**
   * Resets the internal, static entity cache.
   *
   * @param $ids
   *   (optional) If specified, the cache is reset for the entities with the
   *   given ids only.
   */
  public function resetCache(array $ids = NULL) {
    return $this->controller($ids);
  }

  /**
   * Loads one or more entities.
   *
   * @param $ids
   *   An array of entity IDs, or FALSE to load all entities.
   * @param $conditions
   *   An array of conditions in the form 'field' => $value.
   *
   * @return
   *   An array of entity objects indexed by their ids. When no results are
   *   found, an empty array is returned.
   */
  public function load($ids = array(), $conditions = array()) {
    // If not loading a specific revision, look for and load a revision matching
    // the currently active revision tag.
    $revision_key = $this->info['entity keys']['revision'];
       //dpm(print_r($ids,TRUE));
     /*
    if (empty($conditions[$revision_key])) {
       $vids = sps_get_manager()->react('entity_load', (object)array('ids'=>$ids, 'type'=> $this->type));
       dpm($ids, $this->type);
       dpm($vids, $this->type);
      $conditions[$revision_key] = $vids;
    }
*/
    if (empty($conditions[$revision_key]) &&
       ($key = sps_get_manager()->react('entity_load', array()))) {
      $conditions[$revision_key] = 0;
    }
    return $this->controller->load($ids, $conditions);
  }

  public function __call($name, $args) {
    return call_user_func_array(array($this->controller, $name), $args);
  }

}
