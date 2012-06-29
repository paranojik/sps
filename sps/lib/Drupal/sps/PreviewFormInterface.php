<?php
namespace Drupal\sps

interface PreviewFormInterface {

   /**
   * Set the configuration for the PreviewForm
   *
   * @param $config
   *   an array of the following form
   *   array (
   *     'conditions' => array(
   *       'collection' => array(
   *         'title' => 'Collection',
   *         'widget' => 'collection_select', (widget plugin)
   *         'override' => 'view_collection_override', (override plugin)
   *       ),
   *       'date' => array(
   *         'title' => 'Live Date',
   *         'widget' => 'live_date', (widget plugin)
   *         'override' => 'view_live_date_override', (override plugin)
   *      ),
   *    ),
   *   );
   */
   public function setConfig($config);

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
  function getPreviewForm($form, $form_state);

  /**
   * retrieve the Conditions override object
   *
   * @return a \Drupal\sps\Override object
   */
  public function getOverride();

  /**
   * Validates the preview form.
   * 
   * @param $form
   *   The FAPI form to validate 
   * @param $form_state
   *   The state for the form
   *
   * @return
   *   self
   */
  public function validatePreviewForm($form, $form_state);

  /**
   * Extracts the values for the conditions from the FormAPI array.
   *  
   * @param $form
   *   The FAPI form to validate 
   * @param $form_state
   *   The state for the form
   * @return
   *   self
   */
  public function sumbitPreviewForm($form, $form_state);
}
