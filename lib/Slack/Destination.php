<?php

interface Slack_Destination {

  public function get_channel();

  public function item_matches(array $item);

}
