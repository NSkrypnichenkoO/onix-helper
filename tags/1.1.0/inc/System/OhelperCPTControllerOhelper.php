<?php

/**
 * @package onix-meta-box
 */

namespace Onixhelper\System;

use Onixhelper\Interfaces\Callbacks\OhelperAdminCallbacks;
use Onixhelper\Interfaces\Callbacks\OhelperCPTCallbacksOhelper;
use Onixhelper\Interfaces\OhelperAdminPagesCreator;

class OhelperCPTControllerOhelper extends OhelperBaseController
{

  public OhelperCPTCallbacksOhelper $cpt_callbacks;

  public array $subpages = [];

  public OhelperAdminCallbacks $callbacks;

  private OhelperAdminPagesCreator $pages_creator;

  public array $cpt_list = [];


  public function register()
  {

    if (!$this->controller_activated('cpt_manager')) {
      return;
    }

    $this->pages_creator = new OhelperAdminPagesCreator();
    $this->callbacks = new OhelperAdminCallbacks();
    $this->cpt_callbacks = new OhelperCPTCallbacksOhelper();

    $subpages = $this->set_subpages();

    $this->set_option_groups();
    $this->set_sections();
    $this->set_fields();

    //add subpages to admin menu
    $this->pages_creator->add_subpages($subpages)->register();

    $this->store_custom_post_types();

    if (!empty($this->cpt_list)) {
      add_action('init', [$this, 'register_custom_post_types']);
    }
  }

  public function set_option_groups()
  {
    // option name should be like page in the fields
    $args = [
      [
        'option_group' => 'omb_cpt_settings',
        'option_name' => 'onix_meta_box_cpt',
        'callback' => [$this->cpt_callbacks, 'cpt_sanitise'],
      ]
    ];

    $this->pages_creator->set_settings($args);
  }

  public function set_sections()
  {
    $args = [
      [
        'id' => 'onix_meta_box_cpt_index',
        'title' => '',
        'callback' => [$this->cpt_callbacks, 'cpt_pages_section_manager'],
        'page' => 'onix_meta_box_cpt'
      ],
    ];

    $this->pages_creator->set_sections($args);
  }

  public function set_fields()
  {
    $args = [
      [
        // cpt slug must be required
        'id' => 'post_type',
        'title' => 'Custom post type slug **',
        'callback' => [$this->cpt_callbacks, 'text_field'],
        'page' => 'onix_meta_box_cpt',
        'section' => 'onix_meta_box_cpt_index',
        'args' => [
          'option_name' => 'onix_meta_box_cpt',
          'label_for' => 'post_type',
          'placeholder' => 'eg. book',
          'required' => true,
          'readonly' => true,
          'description' => 'Post type key. Must not exceed 20 characters and may only contain lowercase alphanumeric 
                            characters, dashes, and underscores'
        ]
      ],
      [
        'id' => 'plural_name',
        'title' => 'Custom post type name **',
        'callback' => [$this->cpt_callbacks, 'text_field'],
        'page' => 'onix_meta_box_cpt',
        'section' => 'onix_meta_box_cpt_index',
        'args' => [
          'option_name' => 'onix_meta_box_cpt',
          'label_for' => 'plural_name',
          'placeholder' => 'eg. book',
          'required' => true,
          'description' => 'Name of the post type shown in the menu. Usually plural.'
        ]
      ],
      [
        'id' => 'singular_name',
        'title' => 'Custom post type singular name',
        'callback' => [$this->cpt_callbacks, 'text_field'],
        'page' => 'onix_meta_box_cpt',
        'section' => 'onix_meta_box_cpt_index',
        'args' => [
          'option_name' => 'onix_meta_box_cpt',
          'label_for' => 'singular_name',
          'placeholder' => 'eg. book',
          'required' => true,
          'description' => 'Name for one object of this post type.'
        ]
      ],
      [
        // A short descriptive summary of what the post type is.
        'id' => 'description',
        'title' => 'A short descriptive summary of what the post type is.',
        'callback' => [$this->cpt_callbacks, 'text_field'],
        'page' => 'onix_meta_box_cpt',
        'section' => 'onix_meta_box_cpt_index',
        'args' => [
          'option_name' => 'onix_meta_box_cpt',
          'label_for' => 'description',
          'description' => 'A short descriptive summary of what the post type is.'
        ]
      ],
      [
        'id' => 'public',
        'title' => 'public',
        'callback' => [$this->cpt_callbacks, 'true_false_radio_buttons'],
        'page' => 'onix_meta_box_cpt',
        'section' => 'onix_meta_box_cpt_index',
        'args' => [
          'option_name' => 'onix_meta_box_cpt',
          'label_for' => 'public',
          'class' => 'ui-toggle',
          'has_default' => true,
          'description' => 'Whether a post type is intended for use publicly either via the admin interface or by
                            front-end users. While the default settings of $exclude_from_search, $publicly_queryable,
                            $show_ui, and $show_in_nav_menus are inherited from $public, each does not rely on this
                            relationship and controls a very specific intention.
                            Default false.'
        ]
      ],
      [
        'id' => 'publicly_queryable',
        'title' => 'publicly_queryable',
        'callback' => [$this->cpt_callbacks, 'true_false_radio_buttons'],
        'page' => 'onix_meta_box_cpt',
        'section' => 'onix_meta_box_cpt_index',
        'args' => [
          'option_name' => 'onix_meta_box_cpt',
          'label_for' => 'publicly_queryable',
          'class' => 'ui-toggle',
          'has_default' => true,
          'description' => 'Whether queries can be performed on the front end for the post type as part of
                            parse_request(). Endpoints would include: * ?post_type={post_type_key} * ?{post_type_key}={single_post_slug} * ?{post_type_query_var}={single_post_slug}
                            If not set, the default is inherited from $public.',
        ]
      ],
      [
        'id' => 'exclude_from_search',
        'title' => 'exclude_from_search',
        'callback' => [$this->cpt_callbacks, 'true_false_radio_buttons'],
        'page' => 'onix_meta_box_cpt',
        'section' => 'onix_meta_box_cpt_index',
        'args' => [
          'option_name' => 'onix_meta_box_cpt',
          'label_for' => 'exclude_from_search',
          'class' => 'ui-toggle',
          'has_default' => true,
          'description' => 'Whether to exclude posts with this post type from front end search results. Default is the opposite value of $public. '
        ]
      ],
      [
        'id' => 'show_ui',
        'title' => 'show_ui',
        'callback' => [$this->cpt_callbacks, 'true_false_radio_buttons'],
        'page' => 'onix_meta_box_cpt',
        'section' => 'onix_meta_box_cpt_index',
        'args' => [
          'option_name' => 'onix_meta_box_cpt',
          'label_for' => 'show_ui',
          'class' => 'ui-toggle',
          'has_default' => true,
          'description' => 'Whether to generate and allow a UI for managing this post type in the admin. Default is value of $public.'
        ]
      ],
      [
        'id' => 'show_in_menu',
        'title' => 'show_in_menu',
        'callback' => [$this->cpt_callbacks, 'true_false_radio_buttons'],
        'page' => 'onix_meta_box_cpt',
        'section' => 'onix_meta_box_cpt_index',
        'args' => [
          'option_name' => 'onix_meta_box_cpt',
          'label_for' => 'show_in_menu',
          'class' => 'ui-toggle',
          'has_default' => true,
          'description' => 'Where to show the post type in the admin menu. To work, $show_ui must be true. If true, the
                            post type is shown in its own top level menu. If false, no menu is shown. If a string of an
                            existing top level menu (tools.php or edit.php?post_type=page, for example), the post
                            type will be placed as a sub-menu of that.'
        ]
      ],
      [
        'id' => 'show_in_nav_menus',
        'title' => 'show_in_nav_menus',
        'callback' => [$this->cpt_callbacks, 'true_false_radio_buttons'],
        'page' => 'onix_meta_box_cpt',
        'section' => 'onix_meta_box_cpt_index',
        'args' => [
          'option_name' => 'onix_meta_box_cpt',
          'label_for' => 'show_in_nav_menus',
          'class' => 'ui-toggle',
          'has_default' => true,
          'description' => 'Makes this post type available for selection in navigation menus. Default is value of $public.'
        ]
      ],
      [
        'id' => 'capability_type',
        'title' => 'capability_type',
        'callback' => [$this->cpt_callbacks, 'select_field'],
        'page' => 'onix_meta_box_cpt',
        'section' => 'onix_meta_box_cpt_index',
        'args' => [
          'option_name' => 'onix_meta_box_cpt',
          'label_for' => 'capability_type',
          'class' => 'ui-toggle',
          'select_args' => ['post', 'page'], // parameter just for select
          'description' => ' this parameter was intended for Pages. Be careful when choosing it for your custom post type – if you are planning to have very many entries (say – over 2-3 thousand), you will run into load time issues. '
        ]
      ],
      [
        'id' => 'hierarchical',
        'title' => 'hierarchical',
        'callback' => [$this->cpt_callbacks, 'true_false_radio_buttons'],
        'page' => 'onix_meta_box_cpt',
        'section' => 'onix_meta_box_cpt_index',
        'args' => [
          'option_name' => 'onix_meta_box_cpt',
          'label_for' => 'hierarchical',
          'class' => 'ui-toggle',
          'has_default' => true,
          'description' => ' this parameter was intended for Pages. Be careful when choosing it for your custom post type – if you are planning to have very many entries (say – over 2-3 thousand), you will run into load time issues. Default false. '
        ]
      ],
      [
        'id' => 'menu_position',
        'title' => 'menu_position ',
        'callback' => [$this->cpt_callbacks, 'text_field'],
        'page' => 'onix_meta_box_cpt',
        'section' => 'onix_meta_box_cpt_index',
        'args' => [
          'option_name' => 'onix_meta_box_cpt',
          'label_for' => 'menu_position',
          'class' => 'ui-toggle',
          'description' => 'The position in the menu order the post type should appear. To work, $show_in_menu must be true. Default null (at the bottom).'
        ]
      ],
      [
        'id' => 'menu_icon',
        'title' => 'menu_icon',
        'callback' => [$this->cpt_callbacks, 'text_field'],
        'page' => 'onix_meta_box_cpt',
        'section' => 'onix_meta_box_cpt_index',
        'args' => [
          'option_name' => 'onix_meta_box_cpt',
          'label_for' => 'menu_icon',
          'class' => 'ui-toggle',
          'description' => "The URL to the icon to be used for this menu. Pass a base64-encoded SVG using a data URI, 
                            which will be colored to match the color scheme -- this should begin with 'data:image/svg+xml;base64,'. 
                            Pass the name of a Dashicons helper class to use a font icon, e.g.
                            'dashicons-chart-pie'. Leave empty to add icon via CSS Defaults to use the posts icon."
        ]
      ],
      [
        'id' => 'has_archive',
        'title' => 'has archive',
        'callback' => [$this->cpt_callbacks, 'true_false_radio_buttons'],
        'page' => 'onix_meta_box_cpt',
        'section' => 'onix_meta_box_cpt_index',
        'args' => [
          'option_name' => 'onix_meta_box_cpt',
          'label_for' => 'has_archive',
          'class' => 'ui-toggle',
          'has_default' => true,
          'description' => 'Whether there should be post type archives, or if a string, the archive slug to use.
                            Will generate the proper rewrite rules if $rewrite is enabled. Default false.'
        ]
      ],
      [
        'id' => 'show_in_rest',
        'title' => 'show_in_rest',
        'callback' => [$this->cpt_callbacks, 'true_false_radio_buttons'],
        'page' => 'onix_meta_box_cpt',
        'section' => 'onix_meta_box_cpt_index',
        'args' => [
          'option_name' => 'onix_meta_box_cpt',
          'label_for' => 'show_in_rest',
          'class' => 'ui-toggle',
          'has_default' => true,
          'description' => 'Whether to expose this post type in the REST API. Must be true to enable the Gutenberg editor.
                            Default: false'
        ]
      ],
      [
        'id' => 'supports',
        'title' => 'supports',
        'callback' => [$this->cpt_callbacks, 'multi_select_field'],
        'page' => 'onix_meta_box_cpt',
        'section' => 'onix_meta_box_cpt_index',
        'args' => [
          'option_name' => 'onix_meta_box_cpt',
          'label_for' => 'supports',
          'class' => 'ui-toggle',
          'select_args' => ['title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments', 'trackbacks', 'page-attributes', 'custom-fields', 'post-formats'],
          'description' => "Core feature(s) the post type supports. Serves as an alias for calling add_post_type_support() directly.
                            Core features include 'title', 'editor', 'comments', 'revisions', 'trackbacks', 'author',
                            'excerpt', 'page-attributes', 'thumbnail', 'custom-fields', and 'post-formats'.
                            Default is an array containing 'title' and 'editor'."
        ]
      ],
      [
        'id' => 'query_var',
        'title' => 'query var',
        'callback' => [$this->cpt_callbacks, 'text_field_if_radio_true'],
        'page' => 'onix_meta_box_cpt',
        'section' => 'onix_meta_box_cpt_index',
        'args' => [
          'option_name' => 'onix_meta_box_cpt',
          'label_for' => 'query_var',
          'class' => 'ui-toggle',
          'has_default' => true,
          'params' => [
            'false-title' => 'False',
            'true-title' => 'True',
            'input' => [
              'title' => 'query_var',
              'type' => 'text'
            ]
          ],
          'description' => 'Sets the query_var key for this post type. Defaults to $post_type key. If false, a post type cannot be loaded at ?{query_var}={post_slug}. If specified as a string, the query ?{query_var_string}={post_slug} will be valid.'
        ]
      ],
    ];

    //id need to be like settings option name
    // args > option name must be the same as page


    $this->pages_creator->set_fields($args);
  }

  /**
   *
   */
  public function store_custom_post_types()
  {

    $options = get_option('onix_meta_box_cpt');

    if (!is_array($options) || empty($options)) {
      return;
    }

    foreach ($options as $option) {

      if (!isset($option['post_type'])) {
        continue;
      }
      /*
       * required fields:
       * post_type,
       * name,
       * singular_name
       */

      $pt = $option['post_type'];
      $name = $option['plural_name'];
      $single_name = $option['singular_name'];

      $args = [
        'labels' => [
          'name' => $name,
          'singular_name' => $single_name,
          'menu_name' => $name,
          'name_admin_bar' => $single_name,
          'add_new' => sprintf(__('Add new %s ', 'onix-helper'), $single_name),
          'add_new_item' => sprintf(__('Add new %s ', 'onix-helper'), $single_name),
          'new_item' => sprintf(__('New %s ', 'onix-helper'), $single_name),
          'edit_item' => sprintf(__('Edit %s', 'onix-helper'), $single_name),
          'view_item' => sprintf(__('View %s', 'onix-helper'), $single_name),
          'all_items' => sprintf(__('All %s', 'onix-helper'), $name),
          'search_items' => sprintf(__('Search %s', 'onix-helper'), $single_name),
          'parent_item_colon' => $single_name,
          'not_found' => sprintf(__('%s not found', 'onix-helper'), $name),
          'not_found_in_trash' => sprintf(__('%s not found in trash', 'onix-helper'), $name),
          'featured_image' => _x('Featured Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'onix-helper'),
          'set_featured_image' => _x('Set featured image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'onix-helper'),
          'remove_featured_image' => _x('', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'onix-helper'),
          'use_featured_image' => _x('Remove featured image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'onix-helper'),
          'archives' => sprintf(_x('%s Archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'onix-helper'), $single_name),
          'insert_into_item' => sprintf(_x('Insert into %s', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'onix-helper'), $single_name),
          'uploaded_to_this_item' => sprintf(_x('Uploaded to this %s', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'onix-helper'), $single_name),
          'filter_items_list' => sprintf(_x('Filter list of %s', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'onix-helper'), $name),
          'items_list_navigation' => sprintf(_x('%s list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'onix-helper'), $name),
          'items_list' => sprintf(_x('%s list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'onix-helper'), $name),
        ],
        'post_type' => $pt,
      ];

      /*
       * the last parameters are optional. We should check if they isset in passed array before add to the cpt_list
      some of them need add validation
      */

      if (isset($option['supports'])) {
        //should check if supports is empty
        if (!empty($option['supports'])) {
          $args['supports'] = $option['supports'];
        }
      }


      if (isset($option['query_var'])) {
        $query_var = $option['query_var'];
        if ($query_var === '-1') {
          $args['query_var'] = false;
        } else {
          $args['query_var'] = strtr($query_var, [' ' => '']);
        }
      }

      $this->fill_text_parameter('capability_type', $option, $args);
      $this->fill_text_parameter('description', $option, $args);
      $this->fill_text_parameter('menu_position', $option, $args);
      $this->fill_text_parameter('menu_icon', $option, $args);
      $this->fill_checkbox_parameter('public', $option, $args);
      $this->fill_checkbox_parameter('publicly_queryable', $option, $args);
      $this->fill_checkbox_parameter('exclude_from_search', $option, $args);
      $this->fill_checkbox_parameter('show_ui', $option, $args);
      $this->fill_checkbox_parameter('show_in_menu', $option, $args);
      $this->fill_checkbox_parameter('show_in_nav_menus', $option, $args);

      $this->fill_checkbox_parameter('has_archive', $option, $args);
      $this->fill_checkbox_parameter('show_in_rest', $option, $args);
      $this->fill_checkbox_parameter('hierarchical', $option, $args);

      $this->cpt_list[] = $args;
    }
  }

  public function register_custom_post_types()
  {
    foreach ($this->cpt_list as $cpt) {
      register_post_type($cpt['post_type'], $cpt);
    }
  }

  public function set_subpages(): array
  {
    return [
      [
        'parent_slug' => 'onix_meta_box',
        'page_title' => 'CPT manager',
        'menu_title' => 'CPT manager',
        'capability' => 'manage_options',
        'menu_slug' => 'onix_meta_box_cpt',
        'callback' => [$this->callbacks, 'admin_cpt_manager'],
      ],
    ];
  }

  /**
   * class service method to fill array without duplicate if statement many times for checkboxes
   * @param string $key
   * @param array $from
   * @param array $to
   */
  private function fill_checkbox_parameter(string $key, array $from, array &$to)
  {
    if (isset($from[$key])) {
      $to[$key] = $from[$key] ? true : false;
    }
  }

  /**
   * class service method to fill array without duplicate if statement many times for text fields,
   * they should not take empty values
   *
   * @param string $key
   * @param array $from
   * @param array $to
   */
  private function fill_text_parameter(string $key, array $from, array &$to)
  {
    if (isset($from[$key])) {
      if ($from[$key]) {
        $to[$key] = $from[$key];
      }
    }
  }
}
