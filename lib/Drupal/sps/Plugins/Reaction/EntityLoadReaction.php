<?php
namespace Drupal\sps\Plugins\Reaction;

class EntityLoadReaction implements \Drupal\sps\Plugins\ReactionInterface {
  protected $type;
  /**
   * The EntitySelectQueryAlterReaction can work with any number of Entities.
   * Each entity must be shown in the $config['entities'] array.  It will
   * then alter any query (it can touch) and use the override revision id
   * instead of the one in base
   *
   * @param $config
   *   has a value saying which type it is for
   * @param $manager \Drupal\sps\Manager
   *                 the Current Manager Object
   *
   * @return \Drupal\sps\Plugins\Reaction\EntityLoadReaction
   */
  public function __construct(array $config, \Drupal\sps\Manager $manager) {
  }

  /**
   * Return the revision id for a passed in id for the type stored from the constructor
   *
   * @param int $data
   *   the entity id
   * @param $override_controller
   *   This is an override controller to use to find
   *   override data
   *
   * @return int | NULL
   *  the revision id;
   */
  public function react($data, \Drupal\sps\Plugins\OverrideControllerInterface $override_controller) {
    return 'sps';
    $vids= array();
    foreach($data->ids as $id) {
      $row = $override_controller->getRevisionId($id, $data->type);
      $vids[] = $row['revision_id'];
    }
    return $vids;
  }
}
