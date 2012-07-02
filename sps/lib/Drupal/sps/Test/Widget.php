<?php
namespace Drupal\sps\Test;

class Widget extends \Drupal\sps\Widget\Widget {
  function getPreviewForm(&$form, &$form_state) {
    $form['text_input'] = array(
      '#type' => 'textfield',
      '#title' => t('Text Input'),
      '#description' => t('This is simply text input for testing.'),
      '#default_value' => empty($form_state['values']['text_input']) ? '' : $form_state['values']['text_input'],
    );
  }
}