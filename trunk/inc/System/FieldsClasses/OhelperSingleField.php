<?php
// when we create page to create new section? we use code in js and in php
// if we want co have all needed templates and data in the one place - we should have this class
/**
 * @package onix-meta-box
 */

namespace Onixhelper\System\FieldsClasses;


class OhelperSingleField
{
  private array $available_fields_types = ['text', 'image', 'textarea', 'number', 'url', 'password', 'email'];
  private array $columns;

  public function __construct($section_columns)
  {
    $this->columns = $section_columns;
  }

  /**
   * @param $field
   * @param $index
   * @param $input_name
   *
   * @return string
   */
  public function render_full_row($field, $index, $input_name): string
  {

    $row = '<tr>';
    foreach ($this->columns as $slug => $title) {

      $value = isset($field[$slug]) ? $field[$slug] : false;

      if ($value) {
        $row .= '<th data-omb-short-name = "' . esc_attr__($slug) . '">
                 <span>' . esc_html($value) . '</span> 
                 <input type="hidden" name = "' . esc_attr($input_name . '[' . $index . '][' . $slug . ']') . '" value="' . esc_attr($value) . '">
               </th>';
      }
    }
    $row .= $this->render_remove_action_column() . '</tr>';
    return $row;
  }

  public function render_empty_row(): string
  {
    return '<tr class="first-empty-one"> <th> ' . esc_html(__('No data yet')) . '</th> <th></th> <th></th> <th></th></tr>';
  }

  public function render_remove_action_column(): string
  {
    return
      '<th>
         <span class="dashicons dashicons-trash" onclick="remove_single_row(this)"></span>
      </th>';
  }

  public function get_available_fields_types(): array
  {
    return $this->available_fields_types;
  }
}
