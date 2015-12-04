<?php

class Slack_Filter_Board implements Slack_Filter {

  protected $board_id;

  public function __construct ($board_id) {
    if (!is_int($board_id)) {
      throw new InvalidArgumentException('$board_id must be an integer');
    }
    $this->board_id = $board_id;
  }

  public function include_item(array $item) {
    return ($item['board']['id'] == $this->board_id);
  }

}
