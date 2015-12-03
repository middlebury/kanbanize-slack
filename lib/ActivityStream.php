<?php

require_once(dirname(__FILE__)."/KanbanizePHP/API.php");
require_once(dirname(__FILE__)."/KanbanizePHP/APICall.php");
require_once(dirname(__FILE__)."/ActivityList.php");

class ActivityStream {

  protected $tasks = array();

  function __construct($subdomain, $apikey) {
    $this->kanbanize = EtuDev_KanbanizePHP_API::getInstance();
    $this->kanbanize->setSubdomain($subdomain);
    $this->kanbanize->setApiKey($apikey);
  }

  function get_new_activity_for_board($board_id) {
    $interval = new DateInterval("P2D");
    $from_date = new DateTime();
    $from_date->sub($interval);
    $to_date = new DateTime();
    $to_date->add($interval);
    $activities = new ActivityList($this->kanbanize, $board_id, $from_date, $to_date);

    $position_dir = realpath(dirname(__FILE__).'/../data');
    if (!file_exists($position_dir) || !is_writable($position_dir)) {
      throw new Exception($position_dir." must exist and be writable!");
    }
    $position_file = $position_dir.'/last_posted_in_board_'.$board_id;
    if (!file_exists($position_file)) {
      touch($position_file);
    }
    if (!is_writable($position_file)) {
      throw new Exception($position_file." must be writable!");
    }

    // Post any new activity to Slack.
    $last_posted = trim(file_get_contents($position_file));
    $top_item = $activities->current();
    $new_activity = array();
    foreach ($activities as $item) {
      if ($item["hash"] == $last_posted) {
        break;
      } else {
        if (!empty($item["taskid"])) {
          $item["task"] = $this->get_task($board_id, $item["taskid"]);
        }
        $new_activity[] = $item;
      }
    }
    // Record the most recent item's hash
    file_put_contents($position_file, $top_item["hash"]);

    return $new_activity;
  }

  function get_task($board_id, $id) {
    if (!isset($this->tasks[$board_id.'.'.$id])) {
      $this->tasks[$board_id.'.'.$id] = $this->fetch_task($board_id, $id);
    }
    return $this->tasks[$board_id.'.'.$id];
  }

  function fetch_task($board_id, $id) {
    $result = $this->kanbanize->getTaskDetails($board_id, $id);
    return $result;
  }
}
