<?php

/**
 * implements hook_sps_reaction_plugins()
 */
function sps_sps_reaction_plugins() {
  return array(
    'node_load'=> array(
      'class' => 'Drupal\sps\Plugins\Reaction\EntityLoadReaction',
      'use_controller_api' =>'simple_cache',
      'instance_settings' => array('type' => 'node'),
    ),
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

/**
 * Implements hook_sps_widget_plugins().
 *
 * Provide some default widgets.
 */
function sps_sps_widget_plugins() {
  return array(
    'date_widget'=> array(
      'class' => 'Drupal\sps\Plugins\Widget\DateWidget',
      'instance_settings' => array(
        'title' => 'Preview Date'
      ),
    ),
  );
}

/**
 * Implements hook_sps_condition_plugins().
 *
 * Provide a basic condition to show.
 */
function sps_sps_condition_plugins() {
  return array(
    'date_condition'=> array(
      'instance_settings' => array(
        'name' => 'date_condition',
        'widget' => 'date_widget',
      ),
    ),
    'default_root_condition'=> array(
      'class' => '\Drupal\sps\Plugins\Condition\WrapperCondition',
      'instance_settings' => array(
      ),
      'root_condition' => 'True',
    ),
  );
}

