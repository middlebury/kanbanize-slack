<?php

class Kanbanize_ActivityList implements Iterator {

  protected $items = array();
  protected $position = 0;

  protected $total_items = 0;
  protected $current_page = 0;
  protected $num_pages = null;

  function __construct(EtuDev_KanbanizePHP_API $kanbanize, $board_id, DateTime $from_date, DateTime $to_date) {
    $this->kanbanize = $kanbanize;
    $this->board_id = $board_id;
    $this->from_date = $from_date->format("Y-m-d");
    $this->to_date = $to_date->format("Y-m-d");

    $this->fetch();
  }

  protected function fetch() {
    $per_page = 30;
    $this->current_page++;
    $result = $this->kanbanize->getBoardActivities($this->board_id, $this->from_date, $this->to_date, $this->current_page, array('resultsperpage' => $per_page));
    $this->total_items = $result["allactivities"];
    $this->num_pages = ceil(intval($result["allactivities"]) / $per_page);
    foreach ($result["activities"] as $activity) {
      $activity['hash'] = md5(serialize($activity));
      $this->items[] = $activity;
    }
  }

  /* SPL Iterator methods */
  function rewind() {
    $this->position = 0;
  }

  function current() {
    return $this->items[$this->position];
  }

  function key() {
    return $this->position;
  }

  function next() {
    ++$this->position;
    // Fetch the next page of results if it exists
    if ($this->position >= count($this->items) && $this->current_page < $this->num_pages) {
      $this->fetch();
    }
  }

  function valid() {
    return isset($this->items[$this->position]);
  }

}
