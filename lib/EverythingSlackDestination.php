<?php

require_once(dirname(__FILE__).'/SlackDestination.php');

class EverythingSlackDestination implements SlackDestination {

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
