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

    /*initialization of the option for the application to work. While none of the modules is activated -
     it should just be an empty array.*/
    add_option( 'onix_meta_box', [] );
  }

}
