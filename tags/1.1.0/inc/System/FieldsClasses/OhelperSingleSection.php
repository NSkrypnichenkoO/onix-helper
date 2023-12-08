<?php
/**
 * @package onix-meta-box
 */

namespace Onixhelper\System\FieldsClasses;

use JetBrains\PhpStorm\NoReturn;

class OhelperSingleSection
{

  private array $thead_columns;
  private OhelperSingleField $field_manager;

  public function __construct()
  {
    $this->set_thead_columns();

    $this->field_manager = new OhelperSingleField($this->thead_columns);

    add_action('wp_ajax_get_new_field_form_template', [$this, 'get_new_field_form_template']);
    add_action('wp_ajax_nopriv_get_new_field_form_template', [$this, 'get_new_field_form_template']);

    add_action('wp_ajax_get_full_row_template', [$this, 'get_full_row_template']);
    add_action('wp_ajax_nopriv_get_full_row_template', [$this, 'get_full_row_template']);

    add_action('wp_ajax_get_available_fields_types', [$this, 'get_available_fields_types']);
    add_action('wp_ajax_nopriv_get_available_fields_types', [$this, 'get_available_fields_types']);
  }

  /**
   * method to return template of create single field for section
   */
  #[NoReturn] public function get_new_field_form_template()
  {
    $field_types = $this->field_manager->get_available_fields_types();
    $popup = '<div class="onix-beautiful-popup">
                <div class="popup-content">
                    <div id="close-modal-button" class="close-modal-button" onclick="close_modal_popup(this)"><i class="fa-solid fa-xmark"></i></div>
                        <div class="form" id="oh-add-new-field-form">
                            <h2>' . esc_html(__('New field parameters', 'onix-helper')) . '</h2>
                            <div class="oh-single-field-params">
                                <div class="oh-single-field-param"><p>' . esc_html(__('Field type', 'onix-helper')) . '</p>
                                <div class="onix-beautiful-select"> <select name="type">';

    foreach ($field_types as $field_type) {
      $popup .= '<option value="' . esc_attr($field_type) . '">' . esc_html($field_type) . '</option>';
    }
    $popup .= '</select> </div>
                                </div>
                                <div class="oh-single-field-param"> <p>lable </p> <input name="title"></div>
                                <div class="oh-single-field-param" onkeydown="oh_onkeydown_validation(event)">  <p> slug </p> <input name="slug"></div>
                            </div>
                            <span id="submit-onix-helper-add-field-form" onclick="add_field_to_form(this)">' . esc_html(__('Add', 'onix - helper')) . '</span>
                        </div>
                    </div>
                </div>
              </div>';
    echo $popup;
    die();
  }

  /**
   * render html of all table
   *
   * @param string $id field slug
   * @param string $option_name section slug
   * @param bool|string $edit_mode we edit existing section ore create new one
   *
   * @return string html of all table
   */
  public function get_section_table_template(string $id, string $option_name, bool|string $edit_mode): string
  {
    return '
<div>
  <table class="oh-beautiful-table" id="oh-fields-list"> '
      . $this->get_table_thead()
      . $this->get_table_tbody($id, $option_name, $edit_mode)
      . '
  </table>
</div>';
  }

  /**
   * render string with html of button for add new field in section
   *
   * @return string html of button
   */
  public function get_add_field_button(): string
  {
    return '
<div>
  <button id="omb_add_new_field_to_section">' . esc_html(__('Add field', 'onix-helper')) . '</button>
</div>';
  }

  /**
   * fill table
   * <thead> with columns
   *
   * @return string html of table
   * <thead>
   */
  public function get_table_thead(): string
  {
    $columns = '';
    foreach ($this->thead_columns as $column_title) {
      $columns .= '
<th>' . esc_html($column_title) . '</th>
';
    }
    return '
<thead>
<tr>' . $columns . '</tr>
</thead>';
  }

  /**
   * render
   * <tbody>
   *
   * @param string $id field id
   * @param string $option_name section id
   * @param bool|string $edit_field we edit existing section ore create new one
   * @return string html of
   * <tbody>
   */
  private function get_table_tbody(string $id, string $option_name, bool|string $edit_field): string
  {
    $input_name = "$option_name" . "[$id]";
    $body = '';
    if ($edit_field) {
      $input = get_option($option_name);
      $fields_list = isset($input[$edit_field][$id]) ? $input[$edit_field][$id] : false;
      if ($fields_list) {
        foreach ($fields_list as $index => $field) {
          $body .= $this->field_manager->render_full_row($field, $index, $input_name);
        }
      } else {
// if we save section without any field
        $body = $this->field_manager->render_empty_row();
      }
    } else {
// if section just created at the moment
      $body = $this->field_manager->render_empty_row();
    }

// this (data-input-name) attribute need to js to create new rows with right names for inputs
    return '
<tbody data-input-name="' . esc_attr($input_name) . '"> ' . $body . '</tbody>';
  }

  /**
   * here we can set titles for our columns. If you dont need title - edit just $slugs array
   * if you want to ed ore edit title - make changes in mach
   */
  private function set_thead_columns()
  {
// we must have this function to translate our plugin. in array cant be expression, like __('Slug', 'onix-helper'). Just string
    $slugs = ['slug', 'type', 'title', 'options'];

    foreach ($slugs as $slug) {
      $title = match ($slug) {
        'slug' => __('Slug', 'onix-helper'),
        'type' => __('Type', 'onix-helper'),
        'title' => __('Description', 'onix-helper'),
        default => '',
      };
      $this->thead_columns[$slug] = $title;
    }
  }

  /**
   * function is answer on ajax request, return template of single tr for table
   */
  #[NoReturn] public function get_full_row_template()
  {
    $data = json_decode(file_get_contents('php://input'), true);

    $field = isset($data['fields']) ? $data['fields'] : false;
    $index = isset($data['index']) ? $data['index'] : false;
    $input_name = isset($data['input_name']) ? $data['input_name'] : false;

    if ($field && $index !== false && $input_name) {
      echo json_encode(['status' => '200', 'template' => $this->field_manager->render_full_row($field, $index, $input_name)]);
      die();
    } else {
      echo json_encode(['status' => '400']);
      die();
    }
  }
}
