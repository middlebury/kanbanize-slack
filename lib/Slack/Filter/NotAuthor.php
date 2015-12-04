<?php

class Slack_Filter_NotAuthor extends Slack_Filter_Author {

  public function include_item(array $item) {
    return !parent::include_item($item);
  }

}
