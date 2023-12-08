<?php

/**
 * @package onix-meta-box
 */

namespace Onixhelper\System;

use Onixhelper\Interfaces\Callbacks\OhelperAdminCallbacks;
use Onixhelper\Interfaces\Callbacks\OhelperFieldsCallbacksOhelper;
use Onixhelper\Interfaces\OhelperAdminPagesCreator;
use Onixhelper\System\FieldsClasses\OhelperConfigurableMetabox;

class OhelperFieldsControllerOhelper extends OhelperBaseController
{
  public OhelperFieldsCallbacksOhelper $fields_callbacks;

  public array $subpages = [];

  public OhelperAdminCallbacks $callbacks;

  private OhelperAdminPagesCreator $pages_creator;

  private array $fields;


  public function register()
  {

    if (!$this->controller_activated('fields_manager')) {
      return;
    }

    $this->pages_creator = new OhelperAdminPagesCreator();
    $this->callbacks = new OhelperAdminCallbacks();
    $this->fields_callbacks = new OhelperFieldsCallbacksOhelper();

    $this->set_option_groups();
    $this->set_sections();
    $this->set_fields();

    $subpages = $this->set_subpages();

    $this->pages_creator->add_subpages($subpages)->register();

    $this->store_field_sections();
  }

  public function set_subpages(): array
  {
    return [
      [
        'parent_slug' => 'onix_meta_box',
        'page_title' => 'Fields manager',
        'menu_title' => 'Fields manager',
        'capability' => 'manage_options',
        'menu_slug' => 'onix_meta_box_fields',
        'callback' => [$this->callbacks, 'admin_fields_manager'],
      ]
    ];
  }

  public function set_option_groups()
  {
    // option name should be like page in the fields
    $args = [
      [
        'option_group' => 'omb_fields_settings',
        'option_name' => 'onix_meta_box_fields',
        'callback' => [$this->fields_callbacks, 'fields_sanitise'],
      ]
    ];

    $this->pages_creator->set_settings($args);
  }

  public function set_sections()
  {
    $args = [
      [
        'id' => 'onix_meta_box_fields_index',
        'title' => '',
        'callback' => [$this->fields_callbacks, 'fields_pages_section_manager'],
        'page' => 'onix_meta_box_fields'
      ],
    ];

    $this->pages_creator->set_sections($args);
  }


  public function set_fields()
  {
    $args = [
      [
        'id' => 'fields_section_title',
        'title' => 'fields_section_title **',
        'callback' => [$this->fields_callbacks, 'text_field'],
        'page' => 'onix_meta_box_fields',
        'section' => 'onix_meta_box_fields_index',
        'args' => [
          'option_name' => 'onix_meta_box_fields',
          'label_for' => 'fields_section_title',
          'placeholder' => '',
          'required' => true,
          'array' => 'taxonomy',
          'description' => 'Title of the meta box.'
        ]
      ],
      [
        'id' => 'fields_section_slug',
        'title' => 'fields_section_slug **',
        'callback' => [$this->fields_callbacks, 'text_field'],
        'page' => 'onix_meta_box_fields',
        'section' => 'onix_meta_box_fields_index',
        'args' => [
          'option_name' => 'onix_meta_box_fields',
          'label_for' => 'fields_section_slug',
          'placeholder' => '',
          'required' => true,
          'readonly' => true,
          'array' => 'taxonomy',
          'description' => 'Meta box ID (used in the "id" attribute for the meta box).'
        ]
      ],
      [
        'id' => 'fields_section_screen',
        'title' => 'fields_section_screen **',
        'callback' => [$this->fields_callbacks, 'fields_section_screen'],
        'page' => 'onix_meta_box_fields',
        'section' => 'onix_meta_box_fields_index',
        'args' => [
          'option_name' => 'onix_meta_box_fields',
          'label_for' => 'fields_section_screen',
          'placeholder' => '',
          'required' => true,
          'array' => 'taxonomy',
          'description' => "The screen or screens on which to show the box (such as a post type, 'link', or 'comment'). 
                            Accepts a single screen ID, WP_Screen object, or array of screen IDs. Default is the current 
                            screen. If you have used add_menu_page() or add_submenu_page() to create a new screen 
                            (and hence screen_id), make sure your menu slug conforms to the limits of sanitize_key() 
                            otherwise the 'screen' menu may not correctly render on your page.
                            Default: null"
        ]
      ],
      [
        'id' => 'fields_repeater_section_count',
        'title' => 'need repeater?',
        'callback' => [$this->fields_callbacks, 'text_field_if_radio_true'],
        'page' => 'onix_meta_box_fields',
        'section' => 'onix_meta_box_fields_index',
        'args' => [
          'option_name' => 'onix_meta_box_fields',
          'label_for' => 'fields_repeater_section_count',
          'class' => 'ui-toggle',
          'has_default' => true,
          'params' => [
            'false-title' => 'Unlimited',
            'true-title' => 'Limited',
            'input' => [
              'title' => 'Max section count',
              'type' => 'number',
              'min-count' => '2'
            ]
          ],
          'description' => 'Should the section be a repeater, default - false'
        ]
      ],
      [
        'id' => 'fields_section_context',
        'title' => 'fields_section_context',
        'callback' => [$this->fields_callbacks, 'select_field'],
        'page' => 'onix_meta_box_fields',
        'section' => 'onix_meta_box_fields_index',
        'args' => [
          'option_name' => 'onix_meta_box_fields',
          'label_for' => 'fields_section_context',
          'select_args' => ['advanced', 'normal', 'side'],
          'description' => 'The context within the screen where the box should display. Available contexts vary from 
                            screen to screen. Post edit screen contexts include normal, side, and advanced. Comments 
                            screen contexts include normal and side. Menus meta boxes (accordion sections) all use the 
                            side context. Global default is advanced. Default: advanced'
        ]
      ],
      [
        'id' => 'fields_section_priority',
        'title' => 'fields_section_priority',
        'callback' => [$this->fields_callbacks, 'select_field'],
        'page' => 'onix_meta_box_fields',
        'section' => 'onix_meta_box_fields_index',
        'args' => [
          'option_name' => 'onix_meta_box_fields',
          'label_for' => 'fields_section_priority',
          'select_args' => ['high', 'core', 'default', 'low'],
          'description' => 'The priority within the context where the box should show.Accepts high, core, default, 
                            or low .Default default Default: default',
        ]
      ],
      [
        'id' => 'fields_section_fields_list',
        'title' => 'Add fields to meta box',
        'callback' => [$this->fields_callbacks, 'button_to_add_fields'],
        'page' => 'onix_meta_box_fields',
        'section' => 'onix_meta_box_fields_index',
        'args' => [
          'option_name' => 'onix_meta_box_fields',
          'label_for' => 'fields_section_fields_list',
          'array' => 'taxonomy',
          'description' => 'Press to add fields in your custom meta box.'
        ]
      ],

    ];

    $this->pages_creator->set_fields($args);

  }

  public function store_field_sections()
  {

    $options = get_option('onix_meta_box_fields');

    if (!is_array($options) || empty($options)) {
      return;
    }

    foreach ($options as $option) {
      $fields_list = isset($option['fields_section_fields_list']) ? $option['fields_section_fields_list'] : [];
      $args = array(
        'screen_to_show' => isset ($option['fields_section_screen']) ? $option['fields_section_screen'] : ['post'],
        'section_slug' => isset ($option['fields_section_slug']) ? $option['fields_section_slug'] : false,
        'section_title' => isset ($option['fields_section_title']) ? $option['fields_section_title'] : 'You can set title on edit section screen',
        'context' => isset($option['fields_section_context']) ? $option['fields_section_context'] : 'advanced',
        'priority' => isset($option['fields_section_priority']) ? $option['fields_section_priority'] : 'high',
        'max_section_count' => isset($option['fields_repeater_section_count']) ? $option['fields_repeater_section_count'] : 1,
        'fields_list' => $fields_list,
      );

      /**
       * parameter to specify page for show meta box
       */
      if (isset($option['fields_section_screen_pages'])) {
        $args['pages'] = $option['fields_section_screen_pages'];
      }
      new OhelperConfigurableMetabox($args);
    }
  }
}
