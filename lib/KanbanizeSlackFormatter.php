<?php

require_once(dirname(__FILE__).'/SlackFormatter.php');

class KanbanizeSlackFormatter implements SlackFormatter {

  protected $username;
  protected $icon_url = null;

  public function __construct($username, $icon_url = null) {
    if (!strlen($username)) {
      throw new InvalidArgumentException("You must specify a username.");
    }
    $this->username = $username;
    $this->icon_url = $icon_url;
  }

  public function can_handle(array $item) {
    // Check for properties we'll need.
    $required = array('author', 'event', 'text');
    foreach ($required as $key) {
      if (!isset($item[$key])) {
        return false;
      }
    }
    return true;
  }

  public function format(array $item) {
    $d = array(
      "username" => $this->username,
    );
    if ($this->icon_url) {
      $d['icon_url'] = $this->icon_url;
    }
    $d['attachments'] = array();

    $a = array(
      "fallback" => $item['event'].' by '.$item['author'].': '.$item['text'],
      "title" => $item['event'].' by '.$item['author'],
      "text" => $item['text'],
      "fields" => array(),
    );
    $a['fields'][] = array(
      'title' => 'Author',
      'value' => $item['author'],
      'short' => true,
    );
    $d['attachments'][] = $a;

    return $d;
  }

}
