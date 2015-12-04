<?php

class Filter_Kanbanize_Author implements Filter {

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
