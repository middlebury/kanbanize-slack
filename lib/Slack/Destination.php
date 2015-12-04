<?php

class Slack_Destination {

  protected $filters = array();

  public function __construct($channel) {
    if (!strlen($channel)) {
      throw new InvalidArgumentException("You must specify a channel.");
    }
    $this->channel = $channel;
  }

  public function add_filter(Slack_Filter $filter) {
    $this->filters[] = $filter;
  }

  public function get_channel() {
    return $this->channel;
  }

  public function item_matches(array $item) {
    if (empty($this->filters)) {
      throw new Exception("No filters configured for $this->channel");
    }
    // We AND our filters
    foreach ($this->filters as $filter) {
      if (!$filter->include_item($item)) {
        return false;
      }
    }
    return true;
  }

}
