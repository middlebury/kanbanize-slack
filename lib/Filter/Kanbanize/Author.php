<?php

class Filter_Kanbanize_Author implements Filter {

  protected $authors = array();

  public function __construct ($author1, $author2 = null, $author_n = null) {
    if (empty($author1) || (!is_string($author1) && !is_numeric($author1))) {
      throw new InvalidArgumentException('$author must be a string');
    }
    foreach (func_get_args() as $k => $arg) {
      if (!is_null($arg) && (empty($arg) || (!is_string($arg) && !is_numeric($arg)))) {
        throw new InvalidArgumentException("\$author $k must be a string, $arg given.");
      }
      $this->authors[] = $arg;
    }
  }

  public function include_item(array $item) {
    return in_array($item['author'], $this->authors);
  }

}
