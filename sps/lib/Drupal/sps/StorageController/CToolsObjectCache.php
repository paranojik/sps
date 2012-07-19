<?php
namespace Drupal\sps\StorageController;

define(SPS_SITE_STATE_STORAGE_KEY, "sps_site_state_storage_key");
/**
 * Defines a PersistentStorage Controller that uses ctools_object_cache
 *
 *
 * TODO This class is not functional, and more work is need to be done
 * here. the current code is just for helping to describe direction.
 *
 */
class CToolsObjectCache implements \Drupal\sps\StateControllerInterface {
 protected static $obj = 'sps-ctools-object-cache';
 protected static $key = 'sps_site_state_storage_key';
 /**
  * Cache away a object
  *
  * @param $name
  *   A string name use for retrieval
  * @param $cache
  *   an object to be cached
  * @return NULL
  */
public function set($cache) {
   $_SESSION[$this->obj][$this->key] = TRUE;
   ctools_object_cache_set($this->obj, $this->key, $cache);
 }
 /**
  * Test if we have an object cached
  * This should be less expensive then using get
  *
  * @param $name
  *   A string name use for retrieval
  * @return bool
  */
 public function exists() {
   return isset($_SESSION[$this->obj][$this->key]))
 }
 /**
  * Retrieve a cached object
  *
  * @param $name
  *   A string name use for retrieval
  * @return the object that was cached
  */
 public function get() {
   return ctools_object_cache_get($this->obj, $this->key);
 }
}
