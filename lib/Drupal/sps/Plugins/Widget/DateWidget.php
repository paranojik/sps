<?php
namespace Drupal\sps\Plugins\Widget;


class DateWidget extends Widget {

  /**
   * Implements WidgetInterface::getPreviewForm().
   *
   * Return a form to collect the date information from the user.
   */
  public function getPreviewForm($element, &$form_state) {
    $element['#type'] = 'fieldset';
    $element['#title'] = t('Date/Time:');

    $element['preview_date'] = array(
      '#type' => 'date',
      '#title' => t('Date to Preview'),
      '#description' => t('Preview nodes published on or after this date.'),
      '#default_value' => isset($form_state['values']['preview_date']) ? $form_state['values']['preview_date'] : NULL,
    );

    $element['preview_time'] = array(
      '#type' => 'textfield',
      '#title' => t('Time on given date to show.'),
      '#description' => t('Limit the preview to items published after this time.'),
      '#default_value' => isset($form_state['values']['preview_time']) ? $form_state['values']['preview_time'] : '00:00:00',
    );

    return $element;
  }

  /**
   * Implements WidgetInterface::validatePreviewForm().
   *
   * Right now no validation is necessary.
   */
  public function validatePreviewForm($form, &$form_state) {
    $date = self::getTimeStamp($form_state);
    if (!$date) {
      form_set_error('preview_date_wrapper', t('Invalid Date/Time given.'));
    }
  }

  /**
   * Implements WidgetInterface::extractValues().
   *
   * @param $form
   * @param $form_state
   *
   * @return int|Bool
   *  A unix timestamp, 0 for an empty value or FALSE for
   */
  public function extractValues($form, $form_state) {
    return self::getTimeStamp($form_state);
  }

  /**
   * Helper function to retrieve the values from the form_state and create a
   * timestamp from them.
   *
   * @param $form_state
   *  The form state with a value set for preview_date
   *
   * @return int|Bool
   *  A unix timestamp, 0 for an empty value or FALSE for
   */
  protected static function getTimeStamp($form_state) {
    $date_arr = $form_state['values']['preview_date'];
    $date = $date_arr['month'] . '/' . $date_arr['day'] . '/' . $date_arr['year'];
    if (!empty($form_state['values']['preview_time'])) {
      $date .= ' ' . $form_state['values']['preview_time'];
    }
    return strtotime($date);
  }
}
