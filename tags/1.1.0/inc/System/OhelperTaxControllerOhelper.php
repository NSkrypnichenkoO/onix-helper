<?php
/**
 * @package onix-meta-box
 */

namespace Onixhelper\System;

use Onixhelper\Interfaces\Callbacks\OhelperAdminCallbacks;
use Onixhelper\Interfaces\Callbacks\OhelperTaxCallbacksOhelper;
use Onixhelper\Interfaces\OhelperAdminPagesCreator;

class OhelperTaxControllerOhelper extends OhelperBaseController
{
  public OhelperTaxCallbacksOhelper $tax_callbacks;

  public array $subpages = [];

  public OhelperAdminCallbacks $callbacks;

  private OhelperAdminPagesCreator $pages_creator;

  private array $taxonomies;


  public function register()
  {

    if (!$this->controller_activated('tax_manager')) {
      return;
    }

    $this->pages_creator = new OhelperAdminPagesCreator();
    $this->callbacks = new OhelperAdminCallbacks();
    $this->tax_callbacks = new OhelperTaxCallbacksOhelper();

    $subpages = $this->set_subpages();

    $this->set_option_groups();
    $this->set_sections();
    $this->set_fields();

//  add subpages to admin menu
    $this->pages_creator->add_subpages($subpages)->register();

    $this->store_custom_taxonomies();

    if (!empty($this->taxonomies)) {
      add_action('init', [$this, 'register_custom_taxonomies']);
    }
  }

  public function set_subpages(): array
  {
    return [
      [
        'parent_slug' => 'onix_meta_box',
        'page_title' => 'Taxonomy Manager',
        'menu_title' => 'Taxonomy manager',
        'capability' => 'manage_options',
        'menu_slug' => 'onix_meta_box_tax',
        'callback' => [$this->callbacks, 'admin_tax_manager'],
      ],
    ];
  }

  public function set_option_groups()
  {
    // option name should be like page in the fields
    $args = [
      [
        'option_group' => 'omb_tax_settings',
        'option_name' => 'onix_meta_box_tax',
        'callback' => [$this->tax_callbacks, 'tax_sanitise'],
      ]
    ];

    $this->pages_creator->set_settings($args);
  }

  public function set_sections()
  {
    $args = [
      [
        'id' => 'onix_meta_box_tax_index',
        'title' => '',
        'callback' => [$this->tax_callbacks, 'tax_pages_section_manager'],
        'page' => 'onix_meta_box_tax'
      ],
    ];

    $this->pages_creator->set_sections($args);
  }

  public function set_fields()
  {
    $args = [
      [
        'id' => 'taxonomy',
        'title' => '$taxonomy **',
        'callback' => [$this->tax_callbacks, 'text_field'],
        'page' => 'onix_meta_box_tax',
        'section' => 'onix_meta_box_tax_index',
        'args' => [
          'option_name' => 'onix_meta_box_tax',
          'label_for' => 'taxonomy',
          'placeholder' => 'eg. genre',
          'required' => true,
          'readonly' => true,
          'validation_class' => 'oh-validation-slug',
          'description' => 'Taxonomy key, must not exceed 32 characters and may only contain lowercase alphanumeric
                            characters, dashes, and underscores.'
        ]
      ],
      [
        'id' => 'plural_name',
        'title' => 'Custom Tax name (plural)**',
        'callback' => [$this->tax_callbacks, 'text_field'],
        'page' => 'onix_meta_box_tax',
        'section' => 'onix_meta_box_tax_index',
        'args' => [
          'option_name' => 'onix_meta_box_tax',
          'label_for' => 'plural_name',
          'placeholder' => 'eg. Genres',
          'required' => true,
          'description' => 'general name for the taxonomy, usually plural'
        ]
      ],
      [
        'id' => 'singular_name',
        'title' => 'Custom Tax singular name **',
        'callback' => [$this->tax_callbacks, 'text_field'],
        'page' => 'onix_meta_box_tax',
        'section' => 'onix_meta_box_tax_index',
        'args' => [
          'option_name' => 'onix_meta_box_tax',
          'label_for' => 'singular_name',
          'placeholder' => 'eg. Genre',
          'required' => true,
          'array' => 'taxonomy',
          'description' => 'name for one object of this taxonomy.'
        ]
      ],
      [
        'id' => 'description',
        'title' => 'Description',
        'callback' => [$this->tax_callbacks, 'text_field'],
        'page' => 'onix_meta_box_tax',
        'section' => 'onix_meta_box_tax_index',
        'args' => [
          'option_name' => 'onix_meta_box_tax',
          'label_for' => 'description',
          'placeholder' => 'write something',
          'array' => 'taxonomy',
          'description' => 'A short descriptive summary of what the taxonomy is for.',
        ]
      ],
      [
        'id' => 'object_type',
        'title' => 'For which object',
        'callback' => [$this->tax_callbacks, 'multi_select_field'],
        'page' => 'onix_meta_box_tax',
        'section' => 'onix_meta_box_tax_index',
        'args' => [
          'option_name' => 'onix_meta_box_tax',
          'label_for' => 'object_type',
          'class' => 'ui-toggle',
          'required' => true,
          'description' => 'Object type or array of object types with which the taxonomy should be associated.'
        ]
      ],
      [
        'id' => 'public',
        'title' => 'public',
        'callback' => [$this->tax_callbacks, 'true_false_radio_buttons'],
        'page' => 'onix_meta_box_tax',
        'section' => 'onix_meta_box_tax_index',
        'args' => [
          'option_name' => 'onix_meta_box_tax',
          'label_for' => 'public',
          'class' => 'ui-toggle, oh-option-controller',
          'has_default' => [$this->tax_callbacks, 'render_switcher_checkbox_tax'],
          'description' => 'Whether a taxonomy is intended for use publicly either via the admin interface or
                            by front-end users. The default settings of $publicly_queryable, $show_ui, and
                            $show_in_nav_menus are inherited from $public. Default: true.'
        ]
      ],
      [
        'id' => 'publicly_queryable',
        'title' => 'publicly_queryable',
        'callback' => [$this->tax_callbacks, 'true_false_radio_buttons'],
        'page' => 'onix_meta_box_tax',
        'section' => 'onix_meta_box_tax_index',
        'args' => [
          'option_name' => 'onix_meta_box_tax',
          'label_for' => 'publicly_queryable',
          'class' => 'ui-toggle',
          'has_default' => [$this->tax_callbacks, 'render_switcher_checkbox_tax'],
          'description' => 'Whether the taxonomy is publicly queryable. If not set, the default is inherited from $public'
        ]
      ],
      [
        'id' => 'hierarchical',
        'title' => 'hierarchical',
        'callback' => [$this->tax_callbacks, 'true_false_radio_buttons'],
        'page' => 'onix_meta_box_tax',
        'section' => 'onix_meta_box_tax_index',
        'args' => [
          'option_name' => 'onix_meta_box_tax',
          'label_for' => 'hierarchical',
          'class' => 'ui-toggle',
          'has_default' => [$this->tax_callbacks, 'render_switcher_checkbox_tax'],
          'description' => 'Whether the taxonomy is hierarchical. Default false'
        ]
      ],
      [
        'id' => 'show_ui',
        'title' => 'show_ui',
        'callback' => [$this->tax_callbacks, 'true_false_radio_buttons'],
        'page' => 'onix_meta_box_tax',
        'section' => 'onix_meta_box_tax_index',
        'args' => [
          'option_name' => 'onix_meta_box_tax',
          'label_for' => 'show_ui',
          'class' => 'ui-toggle',
          'has_default' => [$this->tax_callbacks, 'render_switcher_checkbox_tax'],
          'description' => 'Whether to generate and allow a UI for managing terms in this taxonomy in the admin.
                            If not set, the default is inherited from $public (default true).'
        ]
      ],
      [
        'id' => 'show_in_menu',
        'title' => 'show_in_menu',
        'callback' => [$this->tax_callbacks, 'true_false_radio_buttons'],
        'page' => 'onix_meta_box_tax',
        'section' => 'onix_meta_box_tax_index',
        'args' => [
          'option_name' => 'onix_meta_box_tax',
          'label_for' => 'show_in_menu',
          'class' => 'ui-toggle',
          'has_default' => [$this->tax_callbacks, 'render_switcher_checkbox_tax'],
          'description' => 'Whether to show the taxonomy in the admin menu. If true, the taxonomy is shown as a submenu
                            of the object type menu. If false, no menu is shown. $show_ui must be true. If not set,
                            default is inherited from $show_ui (default true)'
        ]
      ],
      [
        'id' => 'show_in_nav_menus',
        'title' => 'show_in_nav_menus',
        'callback' => [$this->tax_callbacks, 'true_false_radio_buttons'],
        'page' => 'onix_meta_box_tax',
        'section' => 'onix_meta_box_tax_index',
        'args' => [
          'option_name' => 'onix_meta_box_tax',
          'label_for' => 'show_in_nav_menus',
          'class' => 'ui-toggle',
          'has_default' => [$this->tax_callbacks, 'render_switcher_checkbox_tax'],
          'description' => 'Makes this taxonomy available for selection in navigation menus. If not set, the default
                            is inherited from $public (default true)'
        ]
      ],
      [
        'id' => 'show_in_rest',
        'title' => 'show_in_rest',
        'callback' => [$this->tax_callbacks, 'true_false_radio_buttons'],
        'page' => 'onix_meta_box_tax',
        'section' => 'onix_meta_box_tax_index',
        'args' => [
          'option_name' => 'onix_meta_box_tax',
          'label_for' => 'show_in_rest',
          'class' => 'ui-toggle',
          'has_default' => [$this->tax_callbacks, 'render_switcher_checkbox_tax'],
          'description' => 'Whether to include the taxonomy in the REST API. Set this to true for the taxonomy to be
                            available in the block editor.'
        ]
      ],
      [
        'id' => 'show_tagcloud',
        'title' => 'show_tagcloud',
        'callback' => [$this->tax_callbacks, 'true_false_radio_buttons'],
        'page' => 'onix_meta_box_tax',
        'section' => 'onix_meta_box_tax_index',
        'args' => [
          'option_name' => 'onix_meta_box_tax',
          'label_for' => 'show_tagcloud',
          'class' => 'ui-toggle',
          'has_default' => [$this->tax_callbacks, 'render_switcher_checkbox_tax'],
          'description' => 'Whether to list the taxonomy in the Tag Cloud Widget controls. If not set, the default is
                            inherited from $show_ui (default true)'
        ]
      ],
      [
        'id' => 'show_in_quick_edit',
        'title' => 'show_in_quick_edit',
        'callback' => [$this->tax_callbacks, 'true_false_radio_buttons'],
        'page' => 'onix_meta_box_tax',
        'section' => 'onix_meta_box_tax_index',
        'args' => [
          'option_name' => 'onix_meta_box_tax',
          'label_for' => 'show_in_quick_edit',
          'class' => 'ui-toggle',
          'has_default' => [$this->tax_callbacks, 'render_switcher_checkbox_tax'],
          'description' => 'Whether to show the taxonomy in the quick/bulk edit panel. It not set, the default is
                            inherited from $show_ui (default true).'
        ]
      ],
      [
        'id' => 'show_admin_column',
        'title' => 'show_admin_column',
        'callback' => [$this->tax_callbacks, 'true_false_radio_buttons'],
        'page' => 'onix_meta_box_tax',
        'section' => 'onix_meta_box_tax_index',
        'args' => [
          'option_name' => 'onix_meta_box_tax',
          'label_for' => 'show_admin_column',
          'class' => 'ui-toggle',
          'has_default' => [$this->tax_callbacks, 'render_switcher_checkbox_tax'],
          'description' => 'Whether to display a column for the taxonomy on its post type listing screens. Default false.'
        ]
      ],
      [
        'id' => 'query_var',
        'title' => 'query_var',
        'callback' => [$this->tax_callbacks, 'true_false_radio_buttons'],
        'page' => 'onix_meta_box_tax',
        'section' => 'onix_meta_box_tax_index',
        'args' => [
          'option_name' => 'onix_meta_box_tax',
          'label_for' => 'query_var',
          'class' => 'ui-toggle',
          'has_default' => [$this->tax_callbacks, 'render_switcher_checkbox_tax'],
          'description' => 'Sets the query var key for this taxonomy. Default $taxonomy key. If false, a taxonomy
                            cannot be loaded at ?{query_var}={term_slug}. If a string, the query
                            ?{query_var}={term_slug} will be valid'
        ]
      ],
      [
        'id' => 'sort',
        'title' => 'sort',
        'callback' => [$this->tax_callbacks, 'true_false_radio_buttons'],
        'page' => 'onix_meta_box_tax',
        'section' => 'onix_meta_box_tax_index',
        'args' => [
          'option_name' => 'onix_meta_box_tax',
          'label_for' => 'sort',
          'class' => 'ui-toggle',
          'has_default' => [$this->tax_callbacks, 'render_switcher_checkbox_tax'],
          'description' => 'Whether terms in this taxonomy should be sorted in the order they are provided to
                            wp_set_object_terms(). Default null which equates to false'
        ]
      ],
    ];


    $this->pages_creator->set_fields($args);
  }

  public function store_custom_taxonomies()
  {

    $options = get_option('onix_meta_box_tax');

    if (!is_array($options) || empty($options)) {
      return;
    }

    foreach ($options as $option) {

      /*
       * required fields:
       * taxonomy,
       * plural_name,
       * singular_name
       * objects - but we should feel it with null if user dont pass anything
       */

      $pl_name = isset($option['plural_name']) ? $option['plural_name'] : $option['singular_name'];
      $single_name = $option['singular_name'];
      $objects = !empty ($option['object_type']) ? $option['object_type'] : null;

      $labels = array(
        'name' => _x($pl_name, 'taxonomy general name', 'onix-helper'),
        'singular_name' => _x($single_name, 'taxonomy singular name', 'onix-helper'),
        'search_items' => sprintf(__('Search %s', 'onix-helper'), $pl_name),
        'all_items' => sprintf(__('All %s', 'onix-helper'),  $pl_name),
        'parent_item' => sprintf(__('Parent %s', 'onix-helper'), $single_name),
        'parent_item_colon' => sprintf(__('Parent %s :', 'onix-helper'), $single_name),
        'edit_item' => sprintf(__('Edit %s' , 'onix-helper'), $single_name),
        'update_item' => sprintf(__('Update  %s', 'onix-helper') , $single_name),
        'add_new_item' => sprintf(__('Add New %s' , 'onix-helper'), $single_name),
        'new_item_name' => sprintf(__('New %s'  . ' Name', 'onix-helper'), $single_name),
        'menu_name' => $single_name,
      );

//      sprintf()
      /*
       * for me it is bad practise pass to the method register_taxonomy() array with some parameters, that it not need.
       * so we should separate args array from system information
      */

      $args = [
        'service' => [
          'objects' => $objects,
          'slug' => isset($option['taxonomy']) ? $option['taxonomy'] : 'test'
        ],
        'parameters' => [
          'labels' => $labels,
        ]
      ];

      $this->fill_text_parameter('description', $option, $args);
      $this->fill_checkbox_parameter('public', $option, $args);
      $this->fill_checkbox_parameter('publicly_queryable', $option, $args);
      $this->fill_checkbox_parameter('hierarchical', $option, $args);
      $this->fill_checkbox_parameter('show_ui', $option, $args);
      $this->fill_checkbox_parameter('show_in_menu', $option, $args);
      $this->fill_checkbox_parameter('show_in_nav_menus', $option, $args);
      $this->fill_checkbox_parameter('show_in_rest', $option, $args);
      $this->fill_checkbox_parameter('show_tagcloud', $option, $args);
      $this->fill_checkbox_parameter('show_in_quick_edit', $option, $args);
      $this->fill_checkbox_parameter('show_admin_column', $option, $args);
      $this->fill_checkbox_parameter('query_var', $option, $args);
      $this->fill_checkbox_parameter('sort', $option, $args);

      $this->taxonomies[] = $args;
    }
  }

  public function register_custom_taxonomies()
  {
    foreach ($this->taxonomies as $taxonomy) {
      register_taxonomy($taxonomy['service']['slug'], $taxonomy['service']['objects'], $taxonomy['parameters']);
    }
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
      $to['parameters'][$key] = $from[$key];
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
      if ($a = $from[$key]) {
        $to['parameters'][$key] = $a;
      }
    }
  }

}
