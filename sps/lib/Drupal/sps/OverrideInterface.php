<?php
namespace Drupal\sps;
interface OverrideInterface {
  /**
   * construct an array of override arrays
   *
   * @return an array of override arrays
   */
  public function getOverrides();
}
