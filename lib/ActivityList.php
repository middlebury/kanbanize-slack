<?php

class ActivityList {

  function __construct(EtuDev_KanbanizePHP_API $kanbanize, $board_id, DateTime $from_date, DateTime $to_date) {
    $this->items = array();
    $this->current_page = 0;
    $this->board_id = $board_id;
    $this->from_date = $from_date->format("Y-m-d");
    $this->to_date = $to_date->format("Y-m-d");
    $this->pages = null;
    $this->last_page = null;
    var_dump($kanbanize);
    $this->kanbanize = $kanbanize;
    $this->fetch();
  }

  protected function fetch() {
    $this->current_page++;
    $items = $this->kanbanize->getBoardActivities($this->board_id, $this->from_date, $this->to_date, $this->current_page);
    var_dump($items);
  }
}
