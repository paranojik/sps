<?php

namespace Drupal\sps_test\Plugin;

use Drupal\sps\Plugins\AbstractPlugin;

class GoodPlugin extends AbstractPlugin implements TestTypeInterface {
  public function testMethod() {}
}
