<?php

class Filter_And extends Filter_Or {

  public function include_item(array $item) {
    foreach ($this->args as $arg) {
      if (!$arg->include_item($item)) {
        return false;
      }
    }
    return true;
  }

}
