<?php

class Slack_Destination_Everything implements Slack_Destination {

  protected $channel;

  public function __construct($channel) {
    if (!strlen($channel)) {
      throw new InvalidArgumentException("You must specify a channel.");
    }
    $this->channel = $channel;
  }
  public function get_channel() {
    return $this->channel;
  }

  public function item_matches(array $item) {
    return true;
  }

}
