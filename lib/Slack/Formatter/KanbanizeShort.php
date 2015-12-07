<?php

class Slack_Formatter_KanbanizeShort extends Slack_Formatter_Kanbanize {

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
      $d['text'] .= "    ".$t['title'];

      $d['text'] .= "\n".$t['columnname'];
      $d['text'] .= "    :bust_in_silhouette: ".$t['assignee'];
      $d['text'] .= "    :label: ".$t['tags'];

      if ($t['blocked'] != "0") {
        $d['text'] .= "    :no_entry_sign: ".(($t['blockedreason'])?$t['blockedreason']:'Blocked');
      }
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
