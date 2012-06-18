<?php

namespace Drupal\sps\Plugin\Type\Test\TestType;

use Drupal\sps\Plugin\AbstractPluginType;

class TestType extends AbstractPluginType {
  public static function info() {
    return array(
      'name' => "TestType",
      'title' => 'Test Type',
    );
  }
}
