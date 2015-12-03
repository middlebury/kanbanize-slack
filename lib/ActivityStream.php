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
    foreach ($activities as $k => $v) {
      var_dump($k, $v);
    }
  }
}
