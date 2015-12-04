<?php

class Slack_Filter_Author implements Slack_Filter {

  protected $author;

  public function __construct ($author) {
    if (empty($author)) {
      throw new InvalidArgumentException('$author must not be empty');
    }
    $this->author = $author;
  }

  public function include_item(array $item) {
    return ($item['author'] == $this->author);
  }

}
