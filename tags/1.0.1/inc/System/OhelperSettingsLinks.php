<?php

/**
 * @package onix-meta-box
 */

namespace Onixhelper\System;

class OhelperSettingsLinks extends OhelperBaseController
{

  public function register()
  {
    add_filter("plugin_action_links_$this->omb_name", [$this, 'settings_link']);
  }

  function settings_link($links)
  {
    $settings_link = '<a href="admin.php?page=onix_meta_box">Settings</a>';
    array_push($links, $settings_link);

    return $links;
//  }
  }
}
