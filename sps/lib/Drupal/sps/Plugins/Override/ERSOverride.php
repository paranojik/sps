<?php
namespace Drupal\sps\Plugins\Override;

use \Drupal\sps\Plugins\Override\NodeDateOverride;

class ERSOverride extends NodeDateOverride {
  /**
   * Returns a list of vid's to override the default vids to load.
   *
   * @return
   *  An array of override vids.
   */
  public function getOverrides() {
    $select = db_select('ers_schedule', 'es')
      ->fields('es', array('schedule_id', 'entity_type', 'entity_id', 'revision_id'))
      ->condition('publish_date', $this->timestamp, '<=')
      ->condition('completed', 0)
      ->orderBy('publish_date')
      ->orderBy('revision_id');

    $results = $select->execute()->fetchAllAssoc('entity_type', \PDO::FETCH_ASSOC);

    $list = array();
    foreach($results as $key => $result) {
      if (isset($result['entity_id'])) {
        $results[$key] = $result = array($result);
      }

      foreach($result as $sub => $row) {
        $transform = array();
        $transform['id'] = $row['entity_id'];
        $transform['type'] = $row['entity_type'];
        $transform['revision_id'] = $row['revision_id'];
        $list[$row['entity_type'].'-'.$row['entity_id']] = $transform;
      }
    }

    return $list;
  }
}