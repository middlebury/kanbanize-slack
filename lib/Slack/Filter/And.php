<?php

class Slack_Filter_And extends Slack_Filter_Or {

  public function include_item(array $item) {
    foreach ($this->args as $arg) {
      if (!$arg->include_item($item)) {
        return false;
      }
    }
    return true;
  }

}
