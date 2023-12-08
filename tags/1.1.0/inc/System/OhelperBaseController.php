<?php

/**
 * @package onix-meta-box
 */

namespace Onixhelper\System;

/**
 * Class OhelperBaseController need to define plugins variable
 * глобальные переменные - плохая идея, так как другие плагины могут ненароком определить такие же
 * лучше использовать класс с полями, и екстендить его при необходимости
 * we dont need put this class to init because we will just extend this class, newer create instance
 * @package Onixhelper\System
 */
class OhelperBaseController
{
  public string $omb_name;
  public string $omb_path;
  public string $omb_url;

  //variables to declare list of sections and fields in the admin panel, used in the Admin.php
  public array $option_groups;

  public array $settings_fields;


  public function __construct()
  {
    $this->omb_name = plugin_basename(dirname(__FILE__, 3)) . '/onix-meta-box.php';
    $this->omb_path = plugin_dir_path(dirname(__FILE__, 2));
    $this->omb_url = plugin_dir_url(dirname(__FILE__, 2));

    $this->option_groups = [
      'cpt_manager' => 'CPT manager',
      'fields_manager' => 'Fields manager',
      'tax_manager' => 'Taxonomy manager',
    ];

    $this->settings_fields = [
      'delete_plugin_inform' => 'Remove all data after delete plugin',
    ];
  }

  /**
   * method just for controllers. Check if current option checked on the plugin settings page
   *
   * @param string $key slug of admin page with feature on the admin panel. All list are in OhelperBaseController __construct
   *
   * @return mixed value of option if option active and false if option not active
   */
  public function controller_activated(string $key): bool
  {
    $option = get_option('onix_meta_box');
    if (!$option) {
      return false;
    }
    return in_array($key, $option);
  }

}
