<?php

interface SlackDestination {

  public function get_channel();

  public function item_matches(array $item);

}
