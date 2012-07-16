<?php
namespace Drupal\sps\Test;
use \Drupal\sps\HookControllerInterface;
class HookController implements HookControllerInterface {
  public $invoke_all = array();
  public function setInvokeAll($hook, $callable) {
    $this->invoke_all[$hook] = $callable;
    
  }
  /**
   * @see module_invoke_all()
   */
  public function moduleInvokeAll($hook) {
    $args = func_get_args();
    // Remove $module and $hook from the arguments.
    unset($args[0]);
    if (isset($this->invoke_all[$hook])) {
      return call_user_func_array($this->invoke_all[$hook], $args);
    }

  }
  public function setModuleInvoke($module, $hook, $callable) {
    $this->invoke[$module][$hook] = $callable;

  }
  /**
   * @see module_invoke()
   */
  public function moduleInvoke($module, $hook) {
    $args = func_get_args();
    // Remove $module and $hook from the arguments.
    unset($args[0], $args[1]);
    if (isset($this->invoke[$module][$hook])) {
      return call_user_func_array($this->invoke[$module][$hook], $args);
    }
  }
  /**
   * @see drupal_alter()
   */
  public function drupalAlter($type, &$data, &$context1 = NULL, &$context2 = NULL){
    if (isset($this->alter[$type])) {
      $callable = $this->alter[$type];
      if(is_array($callable)) {
        $callable[0]->{$callable[1]}($data, $context1, $context2);
        
      }
      else {
        $callable($data, $context1, $context2);
      }
    }
    
  }

  public function setDrupalAlter($type, $callable) {
    $this->alter[$type] = $callable;
  }
}
