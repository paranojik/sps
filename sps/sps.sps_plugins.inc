<?php


/**
 * implements hook_sps_reaction_plugins()
 */
function sps_sps_reaction_plugins() {
  return array(
  /*
    'node_load'=> array(
      'class' => 'Drupal\sps\Plugins\Reaction\EntityLoadReaction',
      'use_controller_api' =>'simple_cache',
      'instance_settings' => array('type' => 'node'),
    ),
  */
    'node_select_query_alter' => array(
      'class' => 'Drupal\sps\Plugins\Reaction\EntitySelectQueryAlterReaction',
      'use_controller_api' =>'mysql_table',
      'instance_settings' => array(
        'entities' => array(
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

/**
 * implements hook_sps_override_controller_plugins()
 */
function sps_sps_override_controller_plugins() {
  return array(
    'simple_cache' => array(
      'class' => '\Drupal\sps\Plugins\OverrideController\SimpleCacheOverrideController',
      'implements_controller_api' => 'simple_cache',
    ),
    'temp_table' => array(
      'class' => '\Drupal\sps\Plugins\OverrideController\TempTableOverrideController',
      'implements_controller_api' => 'mysql_table',
    ),
  );
}
