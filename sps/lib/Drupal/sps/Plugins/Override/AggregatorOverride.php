<?php
namespace Drupal\sps\Plugins\Override;

class AggregatorOverride implements \Drupal\sps\Plugins\OverrideInterface {
  protected $override_table = array();
  protected $overrides_set = FALSE;

  /**
   * Implementation of OverrideInterface::setData().
   *
   * Take in other overrides and put their overrides together.
   *
   * @param array $data
   *  An array of overrides.
   *
   * @throws \Drupal\sps\Exception\InvalidOverrideException
   *  If 2 overrides passed as data contain overrides for the same type.
   */
  public function setData($data) {
    foreach ($data as $override) {
      $overrides = $override->getOverrides();
      foreach ($overrides as $type => $items) {
        if (!empty($this->override_table[$type]) && !empty($items)) {
          throw new \Drupal\sps\Exception\InvalidOverrideException('AggregatorOverride may not be passed two overrides that handle the same type.');
        }
        $this->override_table[$type] = $items;
      }
    }
    $this->overrides_set = TRUE;
  }

  /**
   * Implementation of OverrideInterface::getOverrides().
   *
   * @return
   *  An array of overrides keyed by type with subarrays keyed by id and values
   *  representing the revision ids.
   *  Example:
   *  array(
   *    'node' => array(
   *      11 => 23,
   *    ),
   *  );
   */
  public function getOverrides() {
    if ($this->overrides_set) {
      return $this->override_table;
    }
    return FALSE;
  }

  /**
   * Declares what type of data this override takes.
   *
   * OverridesArray means an array of things implementing
   * \Drupal\sps\Plugins\OverrideInterface.
   * These overrides should already have their data set.
   *
   * @return
   *  A string.
   */
  public function getDataConsumerApi() {
    return 'OverridesArray';
  }
}