<?php

class Slack_Formatter_Kanbanize implements Slack_Formatter {

  protected $username;
  protected $icon_url = null;

  public function __construct($username, $icon_url = null) {
    if (!strlen($username)) {
      throw new InvalidArgumentException("You must specify a username.");
    }
    $this->username = $username;
    $this->icon_url = $icon_url;
  }

  public function can_handle(array $group) {
    // Check for properties we'll need.
    if (!count($group)) {
      return false;
    }
    $required = array('author', 'event', 'text');
    foreach ($group as $item) {
      foreach ($required as $key) {
        if (!isset($item[$key])) {
          return false;
        }
      }
    }
    return true;
  }

  public function format(array $group) {
    $d = array(
      "username" => $this->username,
    );
    if ($this->icon_url) {
      $d['icon_url'] = $this->icon_url;
    }
    $d['attachments'] = array();

    $i = $group[0];
    $b = $i['board'];
    $link_text = $b['project_name']." / ". $b["name"]." / ".$i["taskid"];
    $d['text'] = "<".$i['url']."|".$link_text.">";
    if (!empty($i['task'])) {
      $t = $i['task'];
      // $d['text'] .= "\n".$t['title'];

      // Add an attachement for details about the current state of the task.
      $a = array(
        "fallback" => $t['title'],
        "pretext" => $t['title'],
        "color" => (($t['color'])?$t['color']:'#FFFFFF'),
        "fields" => array(),
      );
      $a['fields'][] = array(
        "title" => "Type",
        "value" => $t['type'],
        "short" => true,
      );
      $a['fields'][] = array(
        "title" => "Assignee",
        "value" => $t['assignee'],
        "short" => true,
      );
      $a['fields'][] = array(
        "title" => "Priority",
        "value" => $t['priority'],
        "short" => true,
      );
      if ($t['tags']) {
        $a['fields'][] = array(
          "title" => "Tags",
          "value" => $t['tags'],
          "short" => true,
        );
      }
      $a['fields'][] = array(
        "title" => "Column",
        "value" => $t['columnname'],
        "short" => true,
      );
      if ($t['subtasks']) {
        $a['fields'][] = array(
          "title" => "Subtasks",
          "value" => $t['subtaskscomplete'].' of '.$t['subtasks'].' completed',
          "short" => true,
        );
      }
      if (!empty($t['blocked']) && $t['blocked'] != "0") {
        $a['fields'][] = array(
          "title" => "Blocked",
          "value" => ':no_entry_sign: Blocked'.(($t['blockedreason'])?': '.$t['blockedreason']:''),
          "short" => true,
        );
      }
      if (!is_null($t['size'])) {
        $a['fields'][] = array(
          "title" => "Size",
          "value" => $t['size'],
          "short" => true,
        );
      }
      if (!is_null($t['deadline'])) {
        $a['fields'][] = array(
          "title" => "Deadline",
          "value" => $t['deadline'],
          "short" => true,
        );
      }
      foreach ($t['customfields'] as $f) {
        $a['fields'][] = array(
          "title" => $f["name"],
          "value" => $f["value"],
          "short" => true,
        );
      }
      $d['attachments'][] = $a;
    }

    // Add Each item as its own entry.
    foreach (array_reverse($group) as $item) {
      $a = array(
        "fallback" => $item['event'].' by '.$item['author'].': '.$item['text'],
        "title" => $item['event'].' by '.$item['author'],
        "text" => $item['text'],
        "fields" => array(),
      );
      $d['attachments'][] = $a;
    }
    return $d;
  }

}
