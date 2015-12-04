<?php

interface SlackFormatter {

  public function can_handle(array $item);

  public function format(array $item);

}
