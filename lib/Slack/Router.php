<?php

class Slack_Router {

  protected $slack_url;
  protected $formatters = array();
  protected $destinations = array();

  public function __construct($slack_url) {
    if (!strlen($slack_url)) {
      throw new InvalidArgumentException("You must specify a slack url.");
    }
    $this->slack_url = $slack_url;
  }

  public function add_destination($channel, Slack_Filter $filter = null) {
    $this->destinations[] = new Slack_Destination($channel, $filter);
  }

  public function add_formatter(Slack_Formatter $formatter) {
    $this->formatters[] = $formatter;
  }

  public function route_activities(array $activities) {
    if (!count($this->destinations)) {
      throw new Exception("You must configure at least one Slack destination.");
    }
    if (!count($this->formatters)) {
      throw new Exception("You must configure at least one Slack formatter.");
    }
    foreach ($activities as $group) {
      foreach ($this->destinations as $destination) {
        $filtered_group = array();
        foreach ($group as $item) {
          if ($destination->item_matches($item)) {
            $filtered_group[] = $item;
          }
        }
        if (count($filtered_group)) {
          $this->post($destination->get_channel(), $filtered_group);
        }
      }
    }
  }

  protected function post($channel, array $item) {
    if (!strlen($channel)) {
      throw new InvalidArgumentException("You must specify a channel.");
    }

    foreach ($this->formatters as $formatter) {
      if ($formatter->can_handle($item)) {
        $data = $formatter->format($item);
        break;
      }
    }
    if (empty($data)) {
      throw new Exception("No formatter handled our item.");
    }
    $data['channel'] = $channel;
    $json = json_encode($data);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $this->slack_url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, array('payload' => $json));
    // receive server response ...
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $slack_response = curl_exec ($ch);
    curl_close ($ch);

  }
}
