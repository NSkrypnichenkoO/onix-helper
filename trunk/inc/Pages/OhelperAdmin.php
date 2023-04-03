<?php
/**
 * @package onix-meta-box
 */

namespace Onixhelper\pages;

use Onixhelper\Interfaces\OhelperAdminPagesCreator;
use Onixhelper\Interfaces\Callbacks\OhelperAdminCallbacks;
use Onixhelper\System\OhelperBaseController;

/**
 * Class Admin need to manage pages in admin panel
 * @package Onixhelper\pages
 */
class OhelperAdmin extends OhelperBaseController
{

  public OhelperAdminPagesCreator $pages_creator;

  public OhelperAdminCallbacks $callbacks;


  public function __construct()
  {
    parent::__construct();

    $this->pages_creator = new OhelperAdminPagesCreator();

    $this->callbacks = new OhelperAdminCallbacks();
  }

  public function register()
  {
    $pages = $this->set_pages();
    $this->set_option_groups();
    $this->set_sections();
    $this->set_fields();

    $this->pages_creator->add_pages($pages)->with_subpage('Dashboard')->register();
  }

  public function set_pages(): array
  {
    return [
      [
        'page_title' => 'Onix Helper',
        'menu_title' => 'Onix Helper',
        'capability' => 'manage_options',
        'menu_slug' => 'onix_meta_box',
        'callback' => [$this->callbacks, 'admin_dashboard'],
        'icon_url' => $this->omb_url . '/assets/img/mono-logo.png',
        'position' => 110
      ]
    ];
  }


  public function set_option_groups()
  {
    // option name should be like page in the fields
    $args = [
      [
        'option_group' => 'omb_settings',
        'option_name' => 'onix_meta_box',
        'callback' => [$this->callbacks, 'checkbox_sanitise'],
      ],
      [
      'option_group' => 'omb_plugin_work_settings',
      'option_name' => 'onix_meta_box_work_settings',
      'callback' => [$this->callbacks, 'settings_sanitise'],
    ]
    ];

    $this->pages_creator->set_settings($args);
  }

  public function set_sections()
  {
    $args = [
      [
        'id' => 'onix_meta_group',
        'title' => 'Settings manager',
        'callback' => [$this->callbacks, 'admin_pages_section_manager'],
        'page' => 'onix_meta_box'
      ],

      [
        'id' => 'onix_meta_group_plugin_settings',
        'title' => 'Plugin settings',
        'callback' => [$this->callbacks, 'admin_pages_settings_section_manager'],
        'page' => 'onix_meta_box_work_settings'
      ],
    ];

    $this->pages_creator->set_sections($args);
  }

  public function set_fields()
  {
    $args = [];

    foreach ($this->option_groups as $key => $value) {
      //id need to be like settings option name
      // args > option name must be the same as page
      $args[] =
        [
        'id' => $key,
        'title' => $value,
        'callback' => [$this->callbacks, 'checkbox_field'],
        'page' => 'onix_meta_box',
        'section' => 'onix_meta_group',
        'args' => [
          'option_name' => 'onix_meta_box',
          'label_for' => $key,
          'class' => 'omb-switcher'
        ]
      ];
    }

    $args[] =
      [
      'id' => 'delete_plugin_inform',
      'title' => 'Remove all data after delete plugin',
      'callback' => [$this->callbacks, 'checkbox_field'],
      'page' => 'onix_meta_box_work_settings',
      'section' => 'onix_meta_group_plugin_settings',
      'args' => [
        'option_name' => 'onix_meta_box_work_settings',
        'label_for' => 'delete_plugin_inform',
        'class' => 'ui-toggle'
      ]
    ];

    $this->pages_creator->set_fields($args);
  }
}

