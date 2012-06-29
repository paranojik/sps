<?php
namespace Drupal\sps

/**
 * a helper class for the PreviewForm
 * this wrap up the methods around each condition
 */
class Condition {
  protected $widget;
  protected $override;
  protected $title;
  public function __constuct($config) {
    $override = $config['override'];
    $widget = $config['widget'];

    $this->widget = $config['widget'];
    $this->override = $config['override'];
    $this->title = $config['title'];
  }
  public getWidget() {
    $this->getPlugin("widget", $this->widget);
  }
  public getOverride() {
    $this->getPlugin("override", $this->override);
  }
  protected Plugin($type, $name) {
    return "cool stuff"
  }
}

/**
 * PreviewForm is used for constructing and processing the form used to set the site state
 */
class PreviewForm {
  protected $conditions;
  protected $override;
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
   public function setConfig($config) { 
     $this->conditions = array_map(function($data) { return new Condition($data);}, $config['conditions']);

   /**
   * Construct the Form using the conditions
   *
   * @param $form
   *   the form structure array passed in by drupal_get_form().
   * @param $form_state 
   *   the array of state data for the form.
   *
   * @return 
   *   a FAPI render array
   */
  function getPreviewForm($form, $form_state) {
    return $form;
  }

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
  function validatePreviewForm($form, $form_state) {
    return $this;
  }

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
  function sumbitPreviewForm($form, $form_state) {
    return $this;
  }

  /**
   * retrieve the Conditions override object
   *
   * @return a \Drupal\sps\Override object
   */
  function getOverride() {
    return $this->override;
  }
}
