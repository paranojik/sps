<?php
namespace Drupal\sps;
class NodeController extends \NodeController {
public function load($ids = array(), $conditions = array()) {
    // If not loading a specific revision, look for and load a revision matching
    // the currently active revision tag.
    if (empty($conditions[$this->revisionKey]) &&
        !path_is_admin(current_path()) ){
        $key = sps_get_manager()->react('node_load', $ids[0]);
        if($key) {
          $conditions[$this->revisionKey] = $key;
        }


      // Use the Manager to get the override information for this node
    }

    return parent::load($ids, $conditions);
  }
}

