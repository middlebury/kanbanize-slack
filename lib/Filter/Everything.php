<?php

class Filter_Everything implements Filter {

  public function include_item(array $item) {
    return true;
  }

}
