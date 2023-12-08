<?php
/**
 * @package onix-meta-box
 */

namespace Onixhelper\Interfaces\Callbacks;

use Onixhelper\System\FieldsClasses\OhelperSingleSection;
use Onixhelper\System\OhelperBaseController;

/**
 * Class OhelperCPTCallbacksOhelper here we will declare all callbacks. for more cleaner code
 * @package Onixhelper\Interfaces\Callbacks
 */
class OhelperFieldsCallbacksOhelper extends OhelperBaseCallbacks
{
  public OhelperSingleSection $section_manager;

  public function __construct()
  {
    $this->section_manager = new OhelperSingleSection();
  }

  /**
   * method just to print section title
   */
  public function fields_pages_section_manager()
  {
    $base_controller = new OhelperBaseController();
    $args = [
      'title' => 'Manage Custom Fields',
    ];
    require_once $base_controller->omb_path . 'templates/template-parts/functional-parts/options-navigation-bar.php';

  }

  /**
   * @param $input
   * @return mixed
   */
  public function fields_sanitise($input): mixed
  {

    $option_type = 'fields_section_slug';

    /* at first time this function called twice, and on the second time as input passed already finished array
    * we need just to return it. it is not the best solution check if exist $input['post_type'], but i cant find better
    */
    if (!isset($input[$option_type])) {
      return $input;
    }
    $safe_data = [];

    $output = get_option('onix_meta_box_fields');
    $current_key = $safe_data[$option_type] = sanitize_title($input[$option_type]);

    if (isset($_POST['remove'])) {
      //Sanitizes a string into a slug
      $slug = sanitize_title($_POST['remove']);

      delete_metadata('post', 0, $slug, false, true);

      unset($output[$slug]);
      return $output;
    }


    //need to do global sanitize for all fields in switch-case

    if (isset($input['fields_section_title'])) {
      $safe_data['fields_section_title'] = sanitize_text_field($input['fields_section_title']);
    }
    if (isset($input['fields_section_screen'])) {
      $safe_data['fields_section_screen'] = array_map('sanitize_text_field', $input['fields_section_screen']);
    }
    if (isset($input['fields_section_screen_pages'])) {
      $safe_data['fields_section_screen_pages'] = array_map('sanitize_text_field', $input['fields_section_screen_pages']);
    }
    if (isset($input['fields_repeater_section_count'])) {
      $safe_data['fields_repeater_section_count'] = sanitize_text_field($input['fields_repeater_section_count']);
    }
    if (isset($input['fields_section_context'])) {
      $safe_data['fields_section_context'] = sanitize_text_field($input['fields_section_context']);
    }
    if (isset($input['fields_section_priority'])) {
      $safe_data['fields_section_priority'] = sanitize_text_field($input['fields_section_priority']);
    }

    if (isset($input['fields_section_fields_list'])) {
      $fields = $input['fields_section_fields_list'];
      foreach ($fields as $index => $field) {
        $safe_data['fields_section_fields_list'][$index] = array_map('sanitize_text_field', $field);
      }
    }

    //if option not exist for now. Created first time
    if (!$output) {
      $output = [];
      $output[$current_key] = $safe_data;
      return $output;
    }

    foreach ($output as $key => $type) {
      //if we already have array with this key - we should just update it
      if ($current_key === $key) {
        $output[$key] = $safe_data;
      } else {
        //make associative array from input
        $output[$current_key] = $safe_data;
      }
    }

    return $output;
  }


  /**
   * @param $args
   */
  public function text_field($args)
  {
    $edit_field = $this->fields_edit_mode();
    $base_content = $this->create_text_field($args, $edit_field);
    $this->omb_render_fields_box($args, $base_content, $edit_field);
  }


  public function fields_section_screen(array $args)
  {

    $edit_field = $this->fields_edit_mode();

    $base_content = $this->multi_select_field($args);

    $additional_args = [
      'option_name' => 'onix_meta_box_fields',
      'label_for' => 'fields_section_screen_pages',
    ];

    /**
     * fo render additional sections we need to know, which options are already in use
     */
    $name = sanitize_title($args['label_for']);
    $option_name = sanitize_key($args['option_name']);
    $options_in_use = [];

    if ($edit_field) {
      $select = get_option($option_name);
      if (isset($select[$edit_field][$name])) {
        $options_in_use = $select[$edit_field][$name];
      }
    }

    /**
     * теперь, отталкиваясь от того, что активировано, мы можем передавать класс дополнительным секциям, что бы прятать
     * или показывать дополнительные секции.
     */
    $page_selector_classes = 'oh-pages-selector ';
    if (!in_array('page', $options_in_use)) {
      $page_selector_classes .= 'oh-hide-this-section';
      $additional_args['disabled'] = true;
    }

    $additional_content = $this->prepare_additional_section($this->multi_Select_page_list($additional_args), $page_selector_classes);
    $this->omb_render_fields_box($args, $base_content, $edit_field, $additional_content);
  }


  /**
   * @param $args
   */
  public function text_field_if_radio_true($args)
  {
    $edit_field = $this->fields_edit_mode();
    $base_content = $this->create_text_field_if_radio_true($args, $edit_field);
    $this->omb_render_fields_box($args, $base_content, $edit_field);

  }

  /**
   * @param array $args
   */
  public function multi_select_field(array $args)
  {
    $edit_field = $this->fields_edit_mode();
    $options = get_post_types(['_builtin' => false,]);
    array_push($options, 'link', 'comment', 'post', 'page', 'attachment');
    return $this->create_multiple_select($args, $edit_field, $options);
  }

  public function select_field(array $args)
  {
    $edit_field = $this->fields_edit_mode();
    $base_content = $this->create_simple_select($args, $edit_field);
    $this->omb_render_fields_box($args, $base_content, $edit_field);
  }

  /**
   *  get all templates in site and make multi select with this list
   *
   * @param array $args
   * @return string
   */
  public function multi_Select_page_list(array $args)
  {
    $edit_field = $this->fields_edit_mode();

    $options = ['all' => 'All'];
    $pages = get_pages();

    foreach ($pages as $page) {
      $options[$page->ID] = $page->post_title;
    }

    return $this->create_multiple_select_value_not_title($args, $edit_field, $options);
  }

  /**
   * inner method to check if it is edit mode for cpt
   *
   * @return false|string false if it is not edit mode, cpt slug if it is its edit mode
   */
  private function fields_edit_mode(): false|string
  {
    return isset($_POST['edit_fields_section']) ? sanitize_title($_POST['edit_fields_section']) : false;
  }


  public function button_to_add_fields($args)
  {
    $name = isset($args['label_for']) ? sanitize_title($args['label_for']) : ''; // field id
    $option_name = $args['option_name']; // section id
    $edit_field = $this->fields_edit_mode();

    $base_content = $this->section_manager->get_add_field_button();

    $additional_content = $this->section_manager->get_section_table_template($name, $option_name, $edit_field);

    $this->omb_render_fields_box($args, $base_content, $edit_field, $additional_content);
  }

}
