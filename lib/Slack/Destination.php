<?php

class Slack_Destination {

  protected $filter;

  public function __construct($channel, Slack_Filter $filter) {
    if (!strlen($channel)) {
      throw new InvalidArgumentException("You must specify a channel.");
    }
    $this->channel = $channel;
    $this->filter = $filter;
  }

  public function get_channel() {
    return $this->channel;
  }

  public function item_matches(array $item) {
    return $this->filter->include_item($item);
  }

}
