<?php
namespace Drupal\sps\Override;

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
      ->fields('es')
      ->condition('publish_date', $this->timestamp, '<=')
      ->condition('completed', 0);

    $results = $select->fetchAllAssoc('entity_type');
    return $results;
  }
}