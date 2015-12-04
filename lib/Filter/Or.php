<?php

class Filter_Or implements Filter {

  protected $args = array();

  public function __construct (Filter $arg1, Filter $arg2) {
    foreach (func_get_args() as $arg) {
      if (!($arg instanceof Filter)) {
        throw new InvalidArgumentException('Arguement must be an instance of Filter, '.$arg.' given.');
      }
      $this->args[] = $arg;
    }
  }

  public function include_item(array $item) {
    foreach ($this->args as $arg) {
      if ($arg->include_item($item)) {
        return true;
      }
    }
    return false;
  }

}
