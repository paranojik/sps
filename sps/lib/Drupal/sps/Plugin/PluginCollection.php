<?php

namespace Drupal\sps\Plugin;

class PluginCollection implements \Iterator, \Countable, \ArrayAccess {
  protected $plugins;
  protected $type;

  /**
   * @param $type PluginTypeInterface
   */
  public function __construct(PluginTypeInterface $type) {
    $this->type = $type;
    $this->plugins = array();

    foreach ($this->type->getPluginInfo() as $plugin) {
      $this->plugins[$plugin['name']] = $plugin['class'];
    }
  }

  protected function checkCurrentPluginClass() {
    if (is_string(current($this->plugins))) {
      $class_name = current($this->plugins);
      $this->plugins[key($this->plugins)] = new $class_name($this->type->getPluginInfo(key($this->plugins)));
    }
  }

  protected function checkPluginClass($plugin_name) {
    if (is_string($this->plugins[$plugin_name])) {
      $class_name = $this->plugins[$plugin_name];
      $this->plugins[$plugin_name] = new $class_name($this->type->getPluginInfo($plugin_name));
    }
  }

  /**
   * Return the current element
   *
   * @link http://php.net/manual/en/iterator.current.php
   * @return mixed Can return any type.
   */
  public function current() {
    $this->checkCurrentPluginClass();
    return current($this->plugins);
  }

  /**
   * Move forward to next element
   *
   * @link http://php.net/manual/en/iterator.next.php
   * @return void Any returned value is ignored.
   */
  public function next() {
    next($this->plugins);
    $this->checkCurrentPluginClass();
  }

  /**
   * Return the key of the current element
   *
   * @link http://php.net/manual/en/iterator.key.php
   * @return scalar
   *  scalar on success, or null on failure.
   */
  public function key() {
    return key($this->plugins);
  }

  /**
   * Checks if current position is valid
   *
   * @link http://php.net/manual/en/iterator.valid.php
   * @return boolean
   *  The return value will be casted to boolean and then evaluated.
   *  Returns true on success or false on failure.
   */
  public function valid() {
    return ($this->current() !== FALSE);
  }

  /**
   * Rewind the Iterator to the first element
   *
   * @link http://php.net/manual/en/iterator.rewind.php
   * @return void
   *  Any returned value is ignored.
   */
  public function rewind() {
    reset($this->plugins);
  }

  /**
   * Whether a offset exists
   *
   * @link http://php.net/manual/en/arrayaccess.offsetexists.php
   * @param mixed $offset
   *   An offset to check for.
   * @return boolean
   *  true on success or false on failure.
   *  The return value will be casted to boolean if non-boolean was returned.
   */
  public function offsetExists($offset) {
    return isset($this->plugins);
  }

  /**
   * Offset to retrieve
   *
   * @link http://php.net/manual/en/arrayaccess.offsetget.php
   * @param mixed $offset
   *  The offset to retrieve.
   * @return mixed Can return all value types.
   */
  public function offsetGet($offset) {
    $this->checkPluginClass($offset);
    return $this->plugins[$offset];
  }

  /**
   * Offset to set
   *
   * @link http://php.net/manual/en/arrayaccess.offsetset.php
   * @param mixed $offset
   *  The offset to assign the value to.
   * @param mixed $value
   *   The value to set.
   * @return void
   */
  public function offsetSet($offset, $value) {
    // TODO: Implement offsetSet() method.
  }

  /**
   * Offset to unset
   *
   * @link http://php.net/manual/en/arrayaccess.offsetunset.php
   * @param mixed $offset
   *   The offset to unset.
   * @return void
   */
  public function offsetUnset($offset) {
    // TODO: Implement offsetUnset() method.
  }

  /**
   * Count elements of an object
   *
   * @link http://php.net/manual/en/countable.count.php
   * @return int The custom count as an integer.
   *  The return value is cast to an integer.
   */
  public function count() {
    return count($this->plugins);
  }

}
