<?php

class Slack_Filter_Everything implements Slack_Filter {

  public function include_item(array $item) {
    return true;
  }

}
