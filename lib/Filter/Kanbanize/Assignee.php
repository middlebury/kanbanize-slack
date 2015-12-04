<?php

class Filter_Kanbanize_Assignee implements Filter {

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
