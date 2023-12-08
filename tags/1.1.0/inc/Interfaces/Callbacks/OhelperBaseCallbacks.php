<?php
/**
 * @package onix-meta-box
 */

namespace Onixhelper\Interfaces\Callbacks;

/**
 *
 * @package Onixhelper\Interfaces\Callbacks
 */
class OhelperBaseCallbacks extends OhelperHtmlManager
{

  protected function omb_render_fields_box(array $args, string $base_content, $object_edit_mode, string $additional_content = '')
  {
    $box = '<div class="row-right-part">';
    if (isset($args['has_default']) && $args['has_default']) {
      $box .= $this->create_default_checkbox($args, $object_edit_mode);
      $box .= '</div> </div> <div class="oh-field-content">';
      $box .= $base_content;
    } else {
      $box .= $base_content . '</div>';
    }
    $box .= '</div>';
    if ($additional_content) {
      $box .= '<div class="oh-field-additional-content">' . $additional_content . '</div>';
    }

    $tags = $this->get_ext_escaping_params();

    echo wp_kses($box, $tags);
  }

  public function prepare_additional_section(string $content, string $classes = ''): string
  {
    return '<section class="' . $classes . '">' . $content . '</section>';
  }


  /**
   * @param array|null $input
   * @param array|bool $output
   * @param string $option_type fields from array, the best way pass slug or id
   * @return mixed
   */
  public function sanitise(array|null $input, array|bool $output, string $option_type): mixed
  {

    /* at first time this function called twice, and on the second time as input passed already finished array
     * we need just to return it. it is not the best solution check if exist $input['post_type'], but i cant find better
     */
    if (!isset($input[$option_type])) {
      return $input;
    }
    $input = $this->fields_validation($input);

    $current_key = $input[$option_type];

    //if option not exist for now. Created first time
    if (!$output) {
      $output = [];
      $output[$current_key] = $input;
      return $output;
    }

    foreach ($output as $key => $type) {
      //if we already have array with this key - we should just update it
      if ($current_key === $key) {
        $output[$key] = $input;
      } else {
        //make associative array from input
        $output[$current_key] = $input;
      }
    }

    return $output;
  }

  /**
   * @param array $args
   * @param string $exc_name
   * @param $object_edit_mode
   */
  public function create_text_field(array $args, string|bool $object_edit_mode)
  {
    $name = isset($args['label_for']) ? sanitize_title($args['label_for']) : '';
    $option_name = $args['option_name'];
    $value = $read_only = '';
    $placeholder = isset($args['placeholder']) ? $args['placeholder'] : '';
    $required = isset($args['required']) ? $args['required'] : false;
    $validation_slug = 'oh-validation-slug';

    //if we get there from edit button all fields should contain inform
    if ($object_edit_mode) {
      $input = get_option($option_name);
      if (isset($input[$object_edit_mode][$name])) {
        $value = $input[$object_edit_mode][$name];
      }
      $read_only = isset($args['readonly']) ? $args['readonly'] : false;
    }

    return $this->render_text_field_html("$option_name" . "[$name]", $value, $placeholder, $required, $validation_slug, $read_only);
  }


  /**
   * @param array $args all fields parameters
   * @param string|bool $object_edit_mode slug if there are create new entety mode and false if there is page of create new ane
   */
  public function create_true_false_radio_buttons(array $args, string|bool $object_edit_mode)
  {
    $name = sanitize_title($args['label_for']);
    $option_name = $args['option_name'];

    $value = null;

    if ($object_edit_mode) {
      $checkbox = get_option($option_name);
      if (isset($checkbox[$object_edit_mode][$name])) {
        $value = $checkbox[$object_edit_mode][$name];
      }
    }

    return $this->render_true_false_radio_buttons_html("$option_name" . "[$name]", $value);
  }

  /**
   * @param string $name for radio inputs
   * @param int|null $value passed from db value. if option already filled with value - will be passed 1(true) ore 0(false)if option is empty - will be passed null
   */
  private function render_true_false_radio_buttons_html(string $name, int|null $value)
  {

    $box = '<div class="oh-hide-on-default manage-by-default-switcher" ';

    switch (true) {
      case $value === 0:
        $checkbox_false_params = ' checked ';
        $checkbox_true_params = '';
        break;
      case $value === 1:
        $checkbox_false_params = '';
        $checkbox_true_params = ' checked ';
        break;
      case $value === null:
        $box .= 'style="display:none"';
        $checkbox_true_params = ' disabled checked ';
        $checkbox_false_params = ' disabled ';
        break;
      default:
        $checkbox_true_params = '';
        $checkbox_false_params = '';
    }

    $checkbox_true = $this->render_radio_input($name, 1, $checkbox_true_params, esc_html__('True', 'onix-helper'));
    $checkbox_false = $this->render_radio_input($name, 0, $checkbox_false_params, esc_html__('False', 'onix-helper'));

    return $box . '>' . $checkbox_true . $checkbox_false . '</div>';
  }

  /**
   * @param array $args
   * @param $object_edit_mode
   */
  public function create_simple_select(array $args, $object_edit_mode): string
  {
    $name = sanitize_title($args['label_for']);
    $options = isset($args['select_args']) ? $args['select_args'] : [];
    $option_name = $args['option_name'];
    $classes = isset($args['class']) ? $args['class'] : '';
    $selected = '';

    if ($object_edit_mode) {
      $select = get_option($option_name);
      if (isset($select[$object_edit_mode][$name])) {
        $selected = $select[$object_edit_mode][$name];
      }
    }

    $select = $this->render_simple_select_html("$option_name" . "[$name]", $classes, $selected, $options);

    return $select;
  }


  /**
   * @param array $args
   * @param $object_edit_mode
   * @param $options
   * @return string
   */
  public function create_multiple_select(array $args, $object_edit_mode, $options): string
  {
    $name = sanitize_title($args['label_for']);
    $option_name = sanitize_key($args['option_name']);
    $selected = [];

    if ($object_edit_mode) {
      $select = get_option($option_name);
      if (isset($select[$object_edit_mode][$name])) {
        $selected = $select[$object_edit_mode][$name];
      }
    }


    $required = isset($args['required']) ? 'required' : false;
    $disabled = isset($args['disabled']) ? 'disabled' : false;

    $select = $this->render_multiple_select_html("$option_name" . "[$name][]",
      isset($args['class']) ? $args['class'] : '', $selected, $options,
      $required, $disabled);

    return $select;
  }

  /**
   * @param array $args
   * @param $object_edit_mode
   * @param $options
   * @return string
   */
  public function create_multiple_select_value_not_title(array $args, $object_edit_mode, $options): string
  {
    $name = sanitize_title($args['label_for']);
    $option_name = sanitize_key($args['option_name']);
    $selected = [];

    if ($object_edit_mode) {
      $select = get_option($option_name);
      if (isset($select[$object_edit_mode][$name])) {
        $selected = $select[$object_edit_mode][$name];
      }
    }


    $required = isset($args['required']) ? 'required' : false;
    $disabled = isset($args['disabled']) ? 'disabled' : false;

    $select = $this->render_multiple_select_html_value_not_title("$option_name" . "[$name][]",
      isset($args['class']) ? $args['class'] : '', $selected, $options,
      $required, $disabled);

    return $select;
  }


  /**
   * method that go for array? and find all string values. After that Convert special characters to HTML entities
   *
   * @param array $input all data from the form
   * @return array data from the form after validation
   */
  public function fields_validation(array $input): array
  {
    foreach ($input as $key => $item) {

      if (!is_numeric($key)) {
        $key = htmlspecialchars($key);
      }
      if (is_array($item)) {
        $this->fields_validation($item);
        continue;
      }
      if (!is_numeric($item)) {
        $input[$key] = htmlspecialchars($item);
      }
    }
    return $input;
  }


  public function create_switcher_checkbox_html(string $option_name, string $name, bool $checked)
  {
    $checkbox = $this->render_switcher_checkbox_html($option_name, $name, $checked);
    echo wp_kses($checkbox, $this->get_input_escaping_params());
  }

  /**
   *
   * @param array $args
   */
  public function create_default_checkbox(array $args, $object_edit_mode)
  {
    $name = isset($args['label_for']) ? sanitize_title($args['label_for']) : '';

    $checkbox = get_option($args['option_name']);// it is all metaboxes in the db/ need to find current

    if (!$checkbox && !$object_edit_mode) {
      $checked = true;
    } else {
      $item_slug = $object_edit_mode;
      $checked = !(isset($checkbox[$item_slug][$name]));
    }

    $checkbox = $this->render_switcher_checkbox_html('', '', $checked, esc_html(__('Use default', 'onix-helper')));
    return $checkbox;
  }


  function create_text_field_if_radio_true($args, $object_edit_mode)
  {
    $name = isset($args['label_for']) ? sanitize_title($args['label_for']) : false;
    $option_name = isset($args['option_name']) ? $args['option_name'] : false;
    $params = isset($args['params']) ? $args['params'] : [];

    if (empty($params) || (!$option_name) || (!$name)) {
      //nothing to do if have no params
      return '';
    }

    $value = null;

    if ($object_edit_mode) {
      $checkbox = get_option($option_name);
      if (isset($checkbox[$object_edit_mode][$name])) {
        $value = $checkbox[$object_edit_mode][$name];
      }
    }

    return $this->render_text_field_if_radio_true("$option_name" . "[$name]", $value, $params);
  }

  function create_select_pages_list()
  {

  }

  function render_text_field_if_radio_true($name, $value, array $params)
  {
    $box = '<div class="oh-hide-on-default"';
    $input_container = '<div data-depends-of="true_radio" class="oh-radio-block-to-show';
    $true_params = 'data-show-if-active="true_radio" ';
    $false_params = ' disabled checked ';
    $input_params = '';
    $numb = '';
    $numb_val = '';

    $input = $params['input'];
    $title = $input['title'];

    switch ($input['type']) {

      case 'number':
        $min = isset ($input['min-count']) ? $input['min-count'] : false;
        if ($min) {
          $input_params = 'min="' . $input['min-count'] . '" ';
        }

        if (!$value || $value < 2) {
          $box .= ' style="display:none" ';
          $true_params .= ' disabled ';
          $input_params .= ' disabled';
          $numb = $this->render_input('number', $name, $numb_val, $input_params, esc_html($title));
        } else {

          if ($value === '-1') {
            $false_params = ' checked ';
            $input_params .= 'disabled';
          }

          if ($value > 1) {
            $input_container .= ' oh-already-enabled';
            $true_params .= ' checked ';
            $numb_val = $value;
          }
          $numb = $this->render_input('number', $name, $numb_val, $input_params, esc_html($title));

        }
        break;

      case 'text':
        $numb_val = '';

        if (!$value) {
          $box .= ' style="display:none" ';
          $true_params .= ' disabled ';
          $input_params .= ' disabled ';
        } else {
          if ($value == '-1') {
            $false_params = ' checked ';
            $input_params .= 'disabled';

          } else {
            $input_container .= ' oh-already-enabled';
            $true_params .= ' checked ';
            $numb_val = $value;
            $false_params = '';
          }
        }

        $numb = $this->render_input('text', $name, $numb_val, $input_params, esc_html($title));

        break;
    }


    $false_radio = $this->render_radio_input($name, '-1', $false_params, esc_html($params['false-title']));
    $true_radio = $this->render_radio_input($name, '', $true_params, esc_html($params['true-title']));

    $box .= ' > <div class="oh-radio-with-options manage-by-default-switcher">';
    $input_container .= '">';

    return $box . $false_radio . $true_radio . '</div> ' . $input_container . $numb . '</div></div>';
  }

  /**
   * function to validate int with php.
   * @param string $string
   * @return bool|int
   */
  public function check_if_contains_numbers(string $string): bool
  {
    return (preg_match('~[0-9]+~', $string) === 1);
  }

  function fill_array_element_with_value(array $input, string $key, array &$safe_data)
  {
    if (isset($input[$key])) {
      $sanitise_value = $this->sanitise_true_false_radio($input[$key]);
      if ($sanitise_value !== null) {
        $safe_data[$key] = $sanitise_value;
      }
    }
  }

  private function sanitise_true_false_radio($value): bool|null
  {
    $value = (int)$value;

    if ($value === 1) {
      return true;
    }
    if ($value === 0) {
      return false;
    }

    return null;
  }

}
