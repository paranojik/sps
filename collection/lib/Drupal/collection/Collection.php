<?php

namespace Drupal\collection;

class Collection extends \Entity {
  public $name;
  public $label;
  public $status;
  public $module;
  public $cid;

  public function __construct(array $values = array(), $entityType = NULL) {
    parent::__construct($values, 'Collection');
  }

  protected function defaultUri() {
    return "admin/content/collection/{$this->identifier()}";
  }
}
