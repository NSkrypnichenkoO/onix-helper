<?php

/**
 * @package onix-meta-box
 */

namespace Onixhelper\System;

class OhelperDeactivate
{
  public static function deactivate()
  {
    flush_rewrite_rules();
  }
}
