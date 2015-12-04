<?php

class Slack_Filter_Or implements Slack_Filter {

  protected $args = array();

  public function __construct (Slack_Filter $arg1, Slack_Filter $arg2) {
    foreach (func_get_args() as $arg) {
      if (!($arg instanceof Slack_Filter)) {
        throw new InvalidArgumentException('Arguement must be an instance of Slack_Filter, '.$arg.' given.');
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
