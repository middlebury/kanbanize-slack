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

      if (!empty($t['blocked']) && $t['blocked'] != "0") {
        $d['text'] .= "    :no_entry_sign: ".(($t['blockedreason'])?$t['blockedreason']:'Blocked');
      }
    }

    // Add Each item as its own entry.
    $text = "";
    foreach (array_reverse($group) as $item) {
      // Strip the 'None' assignee from subtask messsages.
      if (preg_match('/^Subtask: None/', $item['text'])) {
        $item['text'] = preg_replace('/^Subtask: None/', 'Subtask: ', $item['text']);
      }

      // Put Comments in their own attachment so they don't get hidden.
      if ($item['event'] == 'Comment added') {
        // Add any previous items as an attachment.
        $text = trim($text);
        if (strlen($text)) {
          $d['attachments'][] = array('text' => $text, 'mrkdwn_in' => array('text'));
        }
        // Add the comment
        $d['attachments'][] = array('text' => "*".$item['event'].' by '.$item['author'].':* '.$item['text'], 'mrkdwn_in' => array('text'), 'color' => '#0000CC');
        // Start a new attachment.
        $text = "";
      } else {
        $text .= "*".$item['event'].' by '.$item['author'].':* '.$item['text']."\n";
      }
    }
    $text = trim($text);
    if (strlen($text)) {
      $d['attachments'][] = array('text' => $text, 'mrkdwn_in' => array('text'));
    }
    return $d;
  }

}
