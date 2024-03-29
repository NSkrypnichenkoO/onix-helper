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

//    add_action( 'init', [ $this, 'activate' ] );
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
          'description' => 'Should the section be a repeater, default - false'
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
      $args = array(
        'post_type' => isset ($option['fields_section_screen']) ? $option['fields_section_screen'] : ['post'],
        'meta_key' => $option['fields_section_slug'],
        'meta_box_parameters' => array(
          'id' => $option['fields_section_slug'],
          'name' => $option['fields_section_title'],
          'context' => 'advanced',
          'priority' => 'high',
        ),
        'fields' => array(
          'max_section_count' => isset($option['fields_repeater_section_count']) ? $option['fields_repeater_section_count'] : 1,

          'fields_list' => array(
            array(
              'type' => 'text',
              'title' => 'Text field',
              'name' => 'link'
            ),
            array(
              'type' => 'image',
              'title' => 'Image',
              'name' => 'image'
            ),
          )

        )

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
