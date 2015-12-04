<?php

class Slack_Filter_Not implements Slack_Filter {

  protected $arg;

  public function __construct (Slack_Filter $arg) {
    $this->arg = $arg;
  }

  public function include_item(array $item) {
    return !$this->arg->include_item($item);
  }

}
