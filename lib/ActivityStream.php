<?php

require_once(dirname(__FILE__)."/KanbanizePHP/API.php");
require_once(dirname(__FILE__)."/KanbanizePHP/APICall.php");
require_once(dirname(__FILE__)."/ActivityList.php");

class ActivityStream {

  function __construct($subdomain, $apikey) {
    $this->kanbanize = EtuDev_KanbanizePHP_API::getInstance();
    $this->kanbanize->setSubdomain($subdomain);
    $this->kanbanize->setApiKey($apikey);
  }

  function load_new_activity_for_board($board_id) {
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
    $position_file = $position_dir.'/last_posted';
    if (!file_exists($position_file)) {
      touch($position_file);
    }
    if (!is_writable($position_file)) {
      throw new Exception($position_file." must be writable!");
    }

    // Post any new activity to Slack.
    $last_posted = trim(file_get_contents($position_file));
    $top_item = $activities->current();
    foreach ($activities as $item) {
      if ($item["hash"] == $last_posted) {
        break;
      } else {
        $this->post_activity($item);
      }
    }
    // Record the most recent item's hash
    file_put_contents($position_file, $top_item["hash"]);
  }

  function post_activity(array $item) {
    print "\nposting "; print_r($item);
  }
}
