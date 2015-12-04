<?php

class Filter_Not implements Filter {

  protected $arg;

  public function __construct (Filter $arg) {
    $this->arg = $arg;
  }

  public function include_item(array $item) {
    return !$this->arg->include_item($item);
  }

}
