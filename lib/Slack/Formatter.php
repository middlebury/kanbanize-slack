<?php

interface Slack_Formatter {

  public function can_handle(array $item);

  public function format(array $item);

}
