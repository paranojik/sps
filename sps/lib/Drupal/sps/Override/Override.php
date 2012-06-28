<?php
namespace Drupal\sps\Override;

use Drupal\sps\OverrideInterface;

abstract class Override implements OverrideInterface{
	/**
   * Construct an array of override arrays.
   *
   * @return
   *    An array of override arrays
   */
  abstract public function getOverrides();

  /**
   * Set the data for this override.
   *
   * This method should be called before get overrides and provides the
   * data which the override will use to find the available overrides.
   *
   * @param $variables
   *    The data in the format specified by this overrides implementation
   *    of getDataConsumerApi().
   */
  abstract public function setData($variables);

  /**
   * Report which data api this Override can consume.
   *
   * This allows overrides and widgets to be matched based on the
   * type of data which they consume and provide (respectively).
   *
   * @return
   *    A string designating the data api this override accepts
   */
  abstract public function getDataConsumerApi();
}