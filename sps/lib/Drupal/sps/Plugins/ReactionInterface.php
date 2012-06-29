<?php
namespace Drupal\sps\Plugins

interface ReactionInterface {
  /**
   * React in some way
   * This could be to alter the $data, or return some data, or even a sideeffect of some kind
   *
   * @param $data Vary
   * @return Vary
   */
  function react($data);
  /**
   * Get the FormAPI preview form for the condition.
   *
   * @param $form
   *   the form structure array passed in by drupal_get_form().
   * @param $form_state 
   *   the array of state data for the form.
   *
   * @return 
   *   a FAPI render array
   */
}
