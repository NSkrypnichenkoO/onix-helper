<?php
/**
 * @package onix-meta-box
 */

namespace Onixhelper\Interfaces\Callbacks;

/**
 *
 * @package Onixhelper\Interfaces\Callbacks
 */
class OhelperBaseCallbacks
{

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
  public function create_text_field(array $args)
  {
    $name = isset($args['label_for']) ? $args['label_for'] : '';
    $option_name = $args['option_name'];
    $value = $read_only = '';
    $placeholder = isset($args['placeholder']) ? $args['placeholder'] : '';
    $required = isset($args['required']) ? $args['required'] : false;
    $validation_slug = 'oh-validation-slug';
    $object_edit_mode = isset($args['object_edit_mode']) ? $args['object_edit_mode'] : false;

    //if we get there from edit button all fields should contain inform
    if ($object_edit_mode) {
      $input = get_option($option_name);
      if (isset($input[$object_edit_mode][$name])) {
        $value = $input[$object_edit_mode][$name];
      }
      $read_only = isset($args['readonly']) ? $args['readonly'] : false;

    }

    $this->render_text_field_html("$option_name" . "[$name]", $value, $placeholder, $required, $validation_slug, $read_only);
  }


  /**
   * @param string $name
   * @param string $value
   * @param string $placeholder
   * @param bool $required
   * @param string $validation_slug actually class for js to validation on enter input value
   * @param bool $readonly
   */
  private function render_text_field_html(string $name, string $value, string $placeholder, bool $required, string $validation_slug, bool $readonly)
  {

    $input = '<label><input type="text" class="regular-text ' . esc_attr($validation_slug) . '" name="' . esc_attr($name) . '" 
          value="' . esc_html($value) . '" ';

    if ($placeholder) {
      $input .= ' placeholder="' . esc_attr($placeholder) . '"';
    }

    if ($required) {
      $input .= ' required';
    }

    if ($readonly) {
      $input .= ' readonly="disabled"';
    }

    $input .= ' ></label>';
    echo $input;
  }


//  /**
//   * @param array $args
//   * @param $object_edit_mode
//   */
//  public function create_checkbox_with_default_option(array $args, $object_edit_mode)
//  {
//    $name = $args['label_for'];
//    $option_name = $args['option_name'];
//    $label = isset($args['description']) ? $args['description'] : '';
//    $value = null;
//
//    if ($object_edit_mode) {
//      $checkbox = get_option($option_name);
//      $value = isset($checkbox[$object_edit_mode][$name]) ? $checkbox[$object_edit_mode][$name] : $value;
//    }
//    $classes = isset($args['class']) ? $args['class'] : '';
//
//    $this->render_checkbox_with_default_option_html("$option_name" . "[$name]", $classes, $value, $label);
//  }

  /**
   * @param array $args all fields parameters
   * @param string|bool $object_edit_mode slug if there are create new entety mode and false if there is page of create new ane
   */
  public function create_true_false_radio_buttons(array $args, string|bool $object_edit_mode)
  {
    $name = $args['label_for'];

    $option_name = $args['option_name'];

    $value = null;

    if ($object_edit_mode) {
      $checkbox = get_option($option_name);
      if (isset($checkbox[$object_edit_mode][$name])) {
        $value = $checkbox[$object_edit_mode][$name];
      }
    }

    $this->render_true_false_radio_buttons_html("$option_name" . "[$name]", $value);
  }

  /**
   * @param string $name for radio inputs
   * @param int|null $value passed from db value. if option already filled with value - will be passed 1(true) ore 0(false)if option is empty - will be passed null
   */
  private function render_true_false_radio_buttons_html(string $name, int|null $value)
  {

    $box = '<div class="oh-hide-on-default"';
    $checkbox_true_value = '<label class="oh-radio-input"> <input type="radio" value="1" name="' . esc_attr($name) . '"';
    $checkbox_false_value = '<label class="oh-radio-input"><input type="radio" value="0" name="' . esc_attr($name) . '"';

    switch (true) {
      case $value === 0:
        $checkbox_false_value .= ' checked ';
        break;
      case $value === 1:
        $checkbox_true_value .= ' checked ';
        break;
      case $value === null:
        $box .= ' style="display:none"';
        $checkbox_true_value .= ' disabled checked';
        $checkbox_false_value .= ' disabled ';
        break;
    }

    $checkbox_true_value .= '>True</label>';
    $checkbox_false_value .= '>False</label>';

    echo $box . '>' . $checkbox_true_value . $checkbox_false_value . '</div>';
  }

//  /**
//   * @param string $name
//   * @param string $classes
//   * @param $value
//   * @param string $label
//   */
//  private function render_checkbox_with_default_option_html(string $name, string $classes, $value, string $label)
//  {
//    /* if we use default value should disable field for custom check. Until user unpress default checkbox */
//
//    /* div bellow should be parentElement.parentElement for cpt-default-setter. should dont change inner structure
//     for this element, will broke js */
//
//    $checkbox_default = '<div class="oh-hide-on-default"> <label><input type="checkbox" class="cpt-default-setter"';
//    $checkbox_false_value = '<label class="cpt-checkbox-container"> <input type="hidden" value="0" name="' . $name . '"';
//    $checkbox_true_value = '<input type="checkbox" name="' . $name . '" class=" ' . $classes . ' "value="1" ';
//
//    if ($value == null) {  // if we have not value - we should check default checkbox
//      $checkbox_default .= ' checked ';
//      $checkbox_false_value .= ' disabled';
//      $checkbox_true_value .= ' disabled ';
//    }
//    if ($value) {
//      $checkbox_true_value .= ' checked ';
//    }
//
//    $checkbox_default .= '>Use default value</label><br>';
//    $checkbox_false_value .= ' >';
//    $checkbox_true_value .= '></label></div>';
//
//    $checkbox = $checkbox_default . $checkbox_false_value . $checkbox_true_value;
//
//    echo $checkbox;
//  }


  /**
   * @param array $args
   * @param $object_edit_mode
   */
  public function create_simple_select(array $args, $object_edit_mode)
  {
    $name = $args['label_for'];
    $option_name = $args['option_name'];
    $selected = '';

    if ($object_edit_mode) {
      $select = get_option($option_name);
      if (isset($select[$object_edit_mode][$name])) {
        $selected = $select[$object_edit_mode][$name];
      }
    }

    $options = isset($args['select_args']) ? $args['select_args'] : [];

    $this->render_simple_select_html("$option_name" . "[$name]",
      isset($args['class']) ? $args['class'] : '', $selected, $options);
  }

  /**
   * @param string $name
   * @param string $classes
   * @param string $selected
   * @param array $options
   */
  private function render_simple_select_html(string $name, string $classes, string $selected, array $options)
  {
    $select = '<select name="' . $name . '" class=" ' . $classes . ' ">';

    foreach ($options as $option) {

      $select .= '<option value="' . $option . '" ';

      if ($selected && ($option == $selected)) {
        $select .= 'selected';
      }

      $select .= '>' . $option . '</option>';
    }

    $select .= '</select>';

    echo $select;
  }

  /**
   * @param array $args
   * @param $object_edit_mode
   * @param $options
   */
  public function create_multiple_select(array $args, $object_edit_mode, $options)
  {
    $name = $args['label_for'];
    $option_name = $args['option_name'];
    $selected = [];

    if ($object_edit_mode) {
      $select = get_option($option_name);
      if (isset($select[$object_edit_mode][$name])) {
        $selected = $select[$object_edit_mode][$name];
      }
    }

    $this->render_multiple_select_html("$option_name" . "[$name][]",
      isset($args['class']) ? $args['class'] : '', $selected, $options,
      isset($args['required']));
  }


  /**
   * @param string $name
   * @param string $classes
   * @param array $selected
   * @param array $options
   * @param bool $required
   */
  private function render_multiple_select_html(string $name, string $classes, array $selected, array $options, bool $required = false)
  {
    $required = $required ? 'required' : "";
    $select = '<div class="onix-beautiful-select"> <select multiple="multiple" name="' . $name . '" class=" ' . $classes . '" ' . $required . '>';

    foreach ($options as $option) {
      $current = (!empty($selected) && in_array($option, $selected)) ? 'selected' : '';
      $select .= '<option value="' . $option . '" ' . $current . '>' . $option . '</option>';
    }

    $select .= '</select></div>';

    echo $select;
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


  public static function render_switcher_checkbox_html(string $option_name, string $name, bool $checked)
  {
    $checkbox = '<label for="' . esc_attr($option_name . '[' . $name . ']') . '" class="switch"> <input type="checkbox" name="' . esc_attr($option_name . '[' . $name . ']') . '" class="checkbox-switcher" value="1" ';
    if ($checked) {
      $checkbox .= "checked";
    }
    echo $checkbox .= ' > </label>';
  }

  /**
   *
   * @param array $args
   */
  public static function render_switcher_checkbox(array $args)
  {
    $name = isset($args['label_for']) ? $args['label_for'] : '';
    $checkbox = get_option($args['option_name']);

    $item_slug = array_key_first($checkbox);
    $checked = !(isset($checkbox[$item_slug][$name]));

    $checkbox_html = '<label class="switch"> <input type="checkbox" class="checkbox-switcher" value="1" ';

    if ($checked) {
      $checkbox_html .= "checked";
    }

    $checkbox_html .= ' ></label> <span>' . esc_html__('Use default', 'onix-helper') . '</span>';
    echo $checkbox_html;
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

  function fill_array_element_with_value(array &$input, string $key)
  {
    if (isset($input[$key])) {
      $sanitise_value = $this->sanitise_true_false_radio($input[$key]);
      if ($sanitise_value !== null) {
        $input[$key] = $sanitise_value;
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
