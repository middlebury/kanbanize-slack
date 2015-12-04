<?php

class Slack_Destination {

  public function __construct($channel, Slack_Filter $filter = null) {
    if (!strlen($channel)) {
      throw new InvalidArgumentException("You must specify a channel.");
    }
    $this->channel = $channel;
    if (empty($filter)) {
      $this->filter = new Slack_Filter_Everything();
    } else {
      $this->filter = $filter;
    }
  }

  public function get_channel() {
    return $this->channel;
  }

  public function item_matches(array $item) {
    return $this->filter->include_item($item);
  }

}
