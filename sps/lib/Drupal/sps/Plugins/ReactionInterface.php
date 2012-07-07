<?php
namespace Drupal\sps\Plugins;

interface ReactionInterface {
  /**
   * React in some way
   * This could be to alter the $data, or return some data, or even a sideeffect of some kind
   *
   * @param $data mixed
   * @return mixed
   */
  function react($data);
}
