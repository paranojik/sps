<?php
namespace Drupal\sps\Plugins;

interface OverrideControllerInterface {
  /**
   * Set what data should be cached way for retrival.
   *
   * @param $overrides
   *   a array of arrays with the following keys 
   *     id, revision_id, type
   */
  function set($overrides);
}
