<?php
namespace Drupal\sps\Plugins\Condition;
define('SPS_CONFIG_WRAPPER_CONDITION_SUB_CONDITIONS', "wrapper_condition_sub_conditions");

use Drupal\sps\Plugins\AbstractPlugin;
use Drupal\sps\Plugins\ConditionInterface;

class WrapperCondition extends BasicCondition {
  public $conditions = array();
  protected $manager;
  public $active_condition;
  protected $override_set = FALSE;
  public $override;
  public $form_state;
  public $conditions_config = array();

  /**
   * Implements PluginInterface::__construct().
   *
   * Create a new BasicCondition.
   *
   * @param $config
   *  An array of configuration which includes the widget to use
   *  These should be specified as the 'widget' key.
   *  The widget key may be specified as class names or instantiated
   *  classes.
   * @param $manager
   *  The current instance of the sps manager.
   */
  public function __construct(array $config, \Drupal\sps\Manager $manager) {
    $this->manager = $manager;
    if (!$this->manager->getConfigController()->exists(SPS_CONFIG_WRAPPER_CONDITION_SUB_CONDITIONS)) {
      $this->setDefaultConditions();
    }
    else {
      $this->conditions_config = $this->manager->getConfigController()->get(SPS_CONFIG_WRAPPER_CONDITION_SUB_CONDITIONS);
      foreach($this->conditions_config as $name => $config) {
        $this->conditions[$name] = $this->manager->getPlugin('condition', $name);
      }
    }

  }

  /**
   * Pull Conditions from the plugin system and load them all in as sub conditions
   *
   * @return \Drupal\sps\Plugins\Condition\WrapperCondition
   *  Self
   */
  protected function setDefaultConditions() {
    foreach($this->manager->getPluginInfo('condition') as $name => $info) {
      if(!isset($info["root_condition"])) {
        $this->conditions[$name] = $this->manager->getPlugin('condition', $name);
        $this->conditions_config[$name]['title'] = $name;
      }
    }
    return $this;
  }

  /**
   * Implements ConditionInterface::getOverride().
   *
   * Retrieve the override if it is set.
   *
   * @return bool|\Drupal\sps\Plugins\OverrideInterface
   *  The override with its values set or FALSE if the form has not been
   */
  public function getOverride() {
    return $this->override;
  }

  /**
  * generate a key to use as the basis for the form items
  *
  * This is here so that when recusion is added this can be change to
  * something that varies for each instance.
  *
  * @return String
  */
  protected function getActiveConditionKey() {
    return 'active_condition';
  }

  /**
   * Implements ConditionInterface::getElement().
   *
   * Gets the form for the condition.
   * This uses ajax to allow the user to select from the other conditions
   * and then submit the settings of that sub condition
   *
   * @see sps_condition_form_validate_callback
   * @see sps_condition_form_submit_callback
   */
  public function getElement($element, &$form_state) {

    //check and see if we have a form_state from previous runs
    if(!isset($form_state['values']) &&
       isset($this->form_state['values'])) {
      $form_state['values'] = $this->form_state['values'];
    }

    $active_condition_key = $this->getActiveConditionKey();
    $wrapper = $active_condition_key .'wrapper';
    $selector = $active_condition_key . 'selector';
    $reset = $active_condition_key .'wrapper_reset';
    $wrapper_wrapper = "$wrapper-wrapper";

    $element[$wrapper] = array(
      '#type' => 'container',
      '#tree' => TRUE,
      '#prefix' => "<div id = '$wrapper_wrapper'>",
      '#suffix' => "</div>",
    );

    // this should be set after an ajax call to select a condition
    if(isset($form_state['values'][$wrapper][$selector])) {
      $this->active_condition = $form_state['values'][$wrapper][$selector];
      $condition = $this->conditions[$this->active_condition];
      $sub_state = $form_state;
      $sub_state['values'] = isset($form_state['values'][$wrapper][$this->active_condition]) ? $form_state['values'][$wrapper][$this->active_condition] : array();
      $element[$wrapper][$this->active_condition] = $condition->getElement(array(), $sub_state);
      $element[$wrapper][$this->active_condition]['#tree'] = TRUE;

      $element[$wrapper][$reset] = array(
        '#type' => 'button',
        '#value' => t('Change Condition'),
        '#ajax' => array(
          'callback' => 'sps_wrapper_condition_ajax_callback',
          'wrapper' => $wrapper_wrapper,
          'method' => 'replace',
          'effect' => 'fade',
        ),
        '#attributes' => array(
          'class' => array('sps-change-condition'),
        ),
      );
    }
    else {
      $element[$wrapper][$selector] = array(
        '#type' => 'select',
        '#title' => 'Condition',
        '#options' => array('none' => 'Select Condition'),
        '#ajax' => array(
          'callback' => 'sps_wrapper_condition_ajax_callback',
          'wrapper' => $wrapper_wrapper,
          'method' => 'replace',
          'effect' => 'fade',
        ),
        '#tree' => TRUE,
      );
      foreach($this->conditions as $name => $condition) {
        $element[$wrapper][$selector]['#options'][$name] = $name;
      }
    }
    $element['#sps_validate'] = array($this, 'validateElement');
    $element['#sps_submit'] = array($this, 'submitElement');
    return $element;
  }


  /**
   * @param $element
   * @param $form_state
   *
   * @return array
   */
  protected function extractSubState($element, $form_state) {

    $active_condition_key = $this->getActiveConditionKey();
    $wrapper = $active_condition_key .'wrapper';
    $selector = $active_condition_key . 'selector';

    $sub_state = $form_state;
    $sub_state['values'] = isset($form_state['values'][$wrapper][$this->active_condition]) ?
      $form_state['values'][$wrapper][$this->active_condition] : array();

    $sub_element = $element[$wrapper][$this->active_condition];

    return array($sub_element, $sub_state);
  }

  /**
   * Implements ConditionInterface::validateElement().
   *
   * Validates the form for the condition by calling the widget's validate function.
   * The widget will be passed only its portion of the form and the values section of
   * $form_state.
   */
  public function validateElement($element, &$form_state) {
    list($sub_element, $sub_state) = $this->extractSubState($element, $form_state);
    if($this->active_condition) {
      $this->conditions[$this->active_condition]->validateElement($sub_element, $sub_state);
    }

    return $this;
  }

  /**
   * Implements ConditionInterface::submitElement().
   *
   * Submits the form for the condition by calling the widget's submit function.
   * The widget will be passed only its portion of the form and the values section of
   * $form_state.
   */
  public function submitElement($element, &$form_state) {

    list($sub_element, $sub_state) = $this->extractSubState($element, $form_state);
    $this->conditions[$this->active_condition]->submitElement($sub_element, $sub_state);
    $this->override = $this->conditions[$this->active_condition]->getOverride();

    $this->override_set = TRUE;

    $active_condition_key = $this->getActiveConditionKey();
    $wrapper = $active_condition_key .'wrapper';
    $selector = $active_condition_key . 'selector';
    $this->form_state = $form_state;
    $this->form_state['values'][$wrapper][$selector] = $this->active_condition;
    return $this;
  }

  /**
   * Utility function to help with widget form methods.
   *
   * Creates a sub form array and subsection of the $form_state['values']
   * and calls the given method of the Condition with these sub items.
   *
   * @param $element
   *  The full form as passed to the element method.
   * @param $form_state
   *  The full form_state as poassed to the element method
   * @param $method
   *  A string which is the name of the method to call on the widget
   *
   * @return mixed
   *  Null
   */
  protected function handleWidgetForm($element, &$form_state, $method) {
    $widget_el = $element['widget'];
    $widget_state = $form_state;

    if (isset($form_state['values']['widget'])) {
        $widget_state['values'] = $form_state['values']['widget'];
    }
    else {
      $widget_state['values'] = array();
    }

    $return = $this->widget->{$method}($widget_el, $widget_state);

    $form_state['values']['widget'] = $widget_state['values'];

    return $return;
  }
}
