<?php

namespace Drupal\sps\Plugins

interface ConditionInterface {
  /**
   * Provide the config to allow this Condition to construct itself.
   *
   * @param $config
   *  An associative array of configuration, generally provided by the
   *  manager
   * @return
   *  null
   */
  public function setConfig($config);

  /**
   * Returns the consolidated Override for this Condition
   *
   * @return
   *  An instance of a class which implements OverrideInterface
   */
  public function getOverride();

  /**
   * Returns the consolidated preview form for this Condition.
   *
   * @param $element
   *  Either the full form being build, or a subform of the fullform.
   *  Must have #parent set to designate parent keys which tree.
   *
   * @param $form_state
   *  The full form_state for the form which is being built.
   *
   * @return
   *  A FAPI array containing the form for this condition.
   */
  public function getElement(&$element, &$form_state);

  /**
   * Validates this Conditions preview form.
   *
   * This function should use form_set_error() to mark any fields
   * which do not validate.
   *
   * @param $element
   *  The form portion (element) which should be validated
   * @param $form_state
   *  The full form_state for the form which is being built. Note
   *  that values my be treed as described by $elements #parent key
   *
   * @return
   *  null
   */
  public function validateElement($element, &$form_state);

  /**
   * Submit this Conditions preview form.
   *
   * This function should take the values from the widgets and
   * hand them off to the respective overrides.
   *
   * @param $element
   *  The element (subform) which is being submitted.
   * @param $form_state
   *  The form state containing the submitted values. Note
   *  that values my be treed as described by $elements #parent key
   *
   * @return
   *  null
   */
  public function submitElement($element, &$form_state);
}
