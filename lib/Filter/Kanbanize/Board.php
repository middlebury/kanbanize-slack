<?php

class Filter_Kanbanize_Board implements Filter {

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
