<?php

class Slack_Filter_Assignee implements Slack_Filter {

  protected $assignee;

  public function __construct ($assignee) {
    if (empty($assignee)) {
      throw new InvalidArgumentException('$assignee must not be empty');
    }
    $this->assignee = $assignee;
  }

  public function include_item(array $item) {
    return ($item['task']['assignee'] == $this->assignee);
  }

}
