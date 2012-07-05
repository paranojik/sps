<?php
namespace Drupal\sps\Test;

class Reaction implements \Drupal\sps\Plugins\PluginInterface, \Drupal\sps\Plugins\ReactionInterface {

  protected $react_callback;
  /**
   * the construct that is expect by the plugin system
   * @Param setting 
   * @param $manager an object of class Drupal\sps\Manager
   */
  public function __construct($settings, $manager) {
    $this->react_callback = $settings['callback'];
  }

  /**
   * React in some way
   * This could be to alter the $data, or return some data, or even a sideeffect of some kind
   *
   * @param $data Vary
   * @return Vary
   */
  public function react($data) {
    return $this->react_callback->__invoke($data);
  }
}
