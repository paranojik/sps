<?php

namespace Drupal\sps\Exception;

class SPSException extends \Exception {
  /**
  * Recusive method to fine the original exception
  *
  * @param $ex
  *  Null to start with this exception or and Exception to start with
  * @return Excpetion
  *   The Original Exception
  */
  public function getOriginal($ex = NULL) {
    $ex = $ex ?: $this;
    if(($previous = $this->getPrevious()) &&
       ($previous !== $ex)) {
      return $this->getOriginal($previous);
    }
    else {
      return $ex;
    }
  }

  /**
  * Check to see if the orginal exception was of a particlular class
  *
  * @param $class
  *   name of a class
  *
  * @return bool
  */
  public function originalIs($class) {
    return is_a($this->getOriginal(), $class);
  }
}
