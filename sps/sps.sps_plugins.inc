<?php


/**
 * implements hook_sps_reaction_plugins()
 */
function sps_sps_reaction_plugins() {
  return array(
    'node_select_query_alter' => array(
      'class' => 'Drupal\sps\Plugins\Reaction\EntitySelectQueryAlterReaction',
      'instance_settings' => array(
        'entites' => array(
          array(
            'base_table' => 'node',
            'base_id' => 'nid',
            'revision_table' => 'node_revision',
            'revision_id' => 'vid',
            'revision_fields' => array('title', 'uid', 'status', 'promote', 'sticky'),
          ),
        ),
      ),
    ),
  );
}
