<?php

class Filter_Kanbanize_Board implements Filter {

  protected $board_ids = array();

  public function __construct ($board_id1, $board_id2 = null, $board_id_n = null) {
    if (!is_int($board_id1) || $board_id1 < 0) {
      throw new InvalidArgumentException('$board_id must be a positive integer, '.$board_id1.' given.');
    }
    foreach (func_get_args() as $k => $arg) {
      if (!is_null($arg) && (!is_int($board_id1) || $board_id1 < 0)) {
        throw new InvalidArgumentException("\$board_id $k must be a positive integer, $arg given.");
      }
      $this->board_ids[] = $arg;
    }
  }

  public function include_item(array $item) {
    return in_array($item['board']['id'], $this->board_ids);
  }
}
