<?php
namespace Drupal\sps\Plugins\Condition;

class BasicCondition implements \Drupal\sps\Plugins\ConditionInterface,\Drupal\sps\Plugins\PluginInterface {
  protected $overrides;
  protected $widget;
  protected $manager;

  protected $override_set = FALSE;

  /**
   * Implements PluginInterface::__construct().
   *
   * Create a new BasicCondition.
   *
   * @param $config
   *  An array of configuration which includes the widgetto use
   *  These should be specified as the 'widget' key.
   *  The widget key may be specified as class names or instantiated
   *  classes.
   * @param $manager
   *  The current instance of the sps manager.
   */
  public function __construct($config, \Drupal\sps\Manager $manager) {
    $this->overrides = $manager->getPluginByMeta('Override', 'condition', $config['name']);

    if (!empty($config['widget']) && is_string($config['widget'])) {
      $config['widget'] = $manager->getPlugin('widget', $config['widget']);
    }

    $this->widget = $config['widget'];
    $this->manager = $manager;
  }

  /**
   * Implements ConditionInterface::getOverride().
   *
   * Retrieve the override if it is set.
   *
   * @return
   *  The override with its values set or FALSE if the form has not been
   *  sucessfully submitted.
   */
  public function getOverride() {
    if ($this->override_set) {
      $override = new \Drupal\sps\Plugins\Override\AggregatorOverride();
      $override->setData($this->overrides);
      return $override;
    }
    return FALSE;
  }

  /**
   * Implements ConditionInterface::getElement().
   *
   * Gets the form for the condition.
   */
  public function getElement($element, &$form_state) {
    $sub_state = $form_state;
    $sub_state['values'] = isset($form_state['values']['widget']) ? $form_state['values']['widget'] : array();
    $element['widget'] = $this->widget->getPreviewForm(array(), $sub_state);
    $element['widget']['#tree'] = TRUE;

    $element['preview'] = array(
      '#type' => 'submit',
      '#value' => 'Preview',
    );

    $element['#validate'] = array($this, 'validateElement');
    $element['#submit'] = array($this, 'submitElement');
  }

  /**
   * Implements ConditionInterface::validateElement().
   *
   * Validates the form for the condition by calling the widget's validate function.
   * The widget will be passed only its portion of the form and the values section of
   * $form_state.
   */
  public function validateElement($element, &$form_state) {
    $this->handleWidgetForm($element, $form_state, 'validatePreviewForm');
  }

  /**
   * Implements ConditionInterface::submitElement().
   *
   * Submits the form for the condition by calling the widget's submit function.
   * The widget will be passed only its portion of the form and the values section of
   * $form_state.
   */
  public function submitElement($element, &$form_state) {
    $this->handleWidgetForm($element, $form_state, 'submitPreviewForm');
    $values = $this->handleWidgetForm($element, $form_state, 'extractValues');

    foreach($this->overrides as $key=>$override) {
      $override = $this->manager->getPlugin('Override', $override);
      $override->setData($values);
      $this->overrides[$key] = $override;
    }

    $this->override_set = TRUE;
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
   * @return
   *  Null
   */
  protected function handleWidgetForm($element, &$form_state, $method) {
    $widget_el = $element['widget'];

    $full_values = $form_state['values'];
    $form_state['values'] = $form_state['values']['widget'];

    $return = $this->widget->{$method}($widget_el, $form_state);

    $full_values['values']['widget'] = $form_state['values'];
    $form_state['values'] = $full_values;

    return $return;
  }
}