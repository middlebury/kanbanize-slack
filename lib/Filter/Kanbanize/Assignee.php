<?php

class Filter_Kanbanize_Assignee implements Filter {

  protected $assignees = array();

  public function __construct ($assignee1, $assignee2 = null, $assignee_n = null) {
    if (empty($assignee1) || (!is_string($assignee1) && !is_numeric($assignee1))) {
      throw new InvalidArgumentException('$assignee must be a string, '.$assignee1.' given.');
    }
    foreach (func_get_args() as $k => $arg) {
      if (!is_null($arg) && (empty($arg) || (!is_string($arg) && !is_numeric($arg)))) {
        throw new InvalidArgumentException("\$assignee $k must be a string, $arg given.");
      }
      $this->assignees[] = $arg;
    }
  }

  public function include_item(array $item) {
    return in_array($item['task']['assignee'], $this->assignees);
  }

}
