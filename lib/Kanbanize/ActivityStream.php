<?php

class Kanbanize_ActivityStream {

  protected $tasks = array();
  protected $subdomain = null;
  protected $boards;

  function __construct($subdomain, $apikey) {
    $this->kanbanize = EtuDev_KanbanizePHP_API::getInstance();
    $this->kanbanize->setSubdomain($subdomain);
    $this->kanbanize->setApiKey($apikey);
    $this->subdomain = $subdomain;
    $this->data_dir = realpath(dirname(__FILE__).'/../../data');
  }

  function set_data_dir($dir) {
    $this->data_dir = $dir;
  }

  function get_new_activity_for_board($board_id) {
    $interval = new DateInterval("P2D");
    $from_date = new DateTime();
    $from_date->sub($interval);
    $to_date = new DateTime();
    $to_date->add($interval);
    $activities = new Kanbanize_ActivityList($this->kanbanize, $board_id, $from_date, $to_date);


    if (!file_exists($this->data_dir) || !is_writable($this->data_dir)) {
      throw new Exception($this->data_dir." must exist and be writable!");
    }
    $position_file = $this->data_dir.'/last_posted_in_board_'.$board_id;
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
          $item["board"] = $this->get_board($board_id);
          $item["url"] = "https://".$this->subdomain.".kanbanize.com/ctrl_board/".$board_id."/".$item["taskid"];

          // Remove the additional subtask activity entry. We'll see it in the parent change.
          if (is_null($item["task"]["columnname"])) {
            unset($item);
            continue;
          }
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
    $result["url"] = "https://".$this->subdomain.".kanbanize.com/ctrl_board/".$board_id."/".$id;
    return $result;
  }

  function get_board($board_id) {
    if (!isset($this->boards)) {
      $result = $this->kanbanize->getProjectsAndBoards();
      $this->boards = array();
      foreach ($result as $project) {
        foreach ($project["boards"] as $board) {
          $this->boards[$board["id"]] = array(
            "id" => $board["id"],
            "name" => $board["name"],
            "project_name" => $project["name"],
            "project_id" => $project["id"],
          );
        }
      }
    }
    return $this->boards[$board_id];
  }
}
