<?php
namespace Drupal\sps\StorageController;
/**
 * Defines a PersistentStorage Controller that uses ctools_object_cache
class CToolsObjectCache implements PersistentStorageControllerInterface {
 protected static $obj = 'sps-ctools-object-cache';
 /**
  * Cache away a object
  *
  * @param $name
  *   A string name use for retrieval
  * @param $cache
  *   an object to be cached
  * @return NULL
  */
 public function set($name, $cache) {
   $_SESSION[$this->obj]['name'] = TRUE;
   ctools_object_cache_set($this->obj, $name, $cache);
 }
 /**
  * Test if we have an object cached
  * This should be less expensive then using get
  *
  * @param $name
  *   A string name use for retrieval
  * @return bool
  */
 public function is_set($name);
   return isset($_SESSION[$this->obj]['name']) && $_SESSION[$this->obj]['name'];
 /**
  * Retrieve a cached object
  *
  * @param $name
  *   A string name use for retrieval
  * @return the object that was cached
  */
 public function get($name);
   return ctools_object_cache_get($this->obj, $name);
}
