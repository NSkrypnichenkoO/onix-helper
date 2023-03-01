<?php

/**
 * @package onix-meta-box
 */

namespace Onixhelper\System;

class OhelperActivate
{

  public static function activate()
  {
    flush_rewrite_rules();
  }

}
