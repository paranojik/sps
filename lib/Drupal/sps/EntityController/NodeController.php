<?php
namespace Drupal\sps\EntityController;
/*
 * @file
 * We are overriding the node controller to ensure that when fields are
 * attached that field_attach_revision is used in stead of field_attach.
 * But as the method to do that is to set a a condition on the revision key we
 * also have to override the buildQuery so that the load query is not build 
 * with one id.  We can not use the actually revision id because load can take
 * multiple ids but there can only be one revision.
 */
class NodeController extends \NodeController {
  /*
  public function load($ids = array(), $conditions = array()) {
    // If not loading a specific revision, look for and load a revision matching
    // the currently active revision tag.
    
    if (empty($conditions[$this->revisionKey]) &&
       ($key = sps_get_manager()->react('entity_load', array()))) {
      $conditions[$this->revisionKey] = $key;
    }

    return parent::load($ids, $conditions);
  }
  protected function buildQuery($ids, $conditions = array(), $revision_id = FALSE) {
    if (($key = sps_get_manager()->react('entity_load', array()))) {
      $revision_id = ($revision_id == $key) ? FALSE : $revision_id;
    }

    $q = parent::buildQuery($ids, $conditions, $revision_id);
    return $q;
    return parent::buildQuery($ids, $conditions, $revision_id);
  }
  */
}

