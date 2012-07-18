<?php
/**
 * @file Provides the basic developer documentation for working with iib.
 *
 * IIB provides a centralized way of adding addition administrative information and
 * tasks, including full forms, to pages in drupal.  It provides a hook, a function
 * similar to drupal_set_message and an alter hook to allow modules to control what
 * is rendered in the bar.
 *
 * @see iib.module
 */

/**
 * Allows modules to add items into the render array for the IIB.
 *
 * @param $items
 *  The current render array for the preview bar.  This will have other
 *  modules information in it, but the order is controlled by the system
 *  weight for this hook.
 *
 * @return
 *  The return value is ignored as the $items are passed by reference.
 */
function hook_iib_item_view(&$items) {
  $items['left'] = array(
    '#weight' => -10,
    '#prefix' => '<div>',
    '#markup' => t('Hi this is the left side.'),
    '#suffix' => '</div>',
  );
}

/**
 * Allows modules to alter the result of all the iib_item_view hook invocation
 *
 * @param $items
 *  A render array as returned from module_invoke_all for the iib_item_view hook.
 */
function hook_iib_items_view_alter(&$items) {
  $items['left']['#weight'] = 0;
}

/**
 * iib_set_item allows modules to add items to the bar from any hook that runs before
 * hook_footer.
 *
 * @param $item
 *  A render array to be placed into the items array.  Modules may use weight
 *  to control the placement of the items.  If more control is needed, a module should
 *  alter the results.
 *
 * @return
 *  NULL
 */
function example_module_node_view($vars) {
  iib_set_item($vars['node']->body);
}
