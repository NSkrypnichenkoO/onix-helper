<?php
/**
 * @package onix-meta-box
 */

namespace Onixhelper\Interfaces\Callbacks;


/**
 *
 * @package Onixhelper\Interfaces\Callbacks
 *
 * render element from already received data
 */
class OhelperHtmlManager
{
  private array $common_escaping_params = [
    'label' => [
      'class' => [],
      'style' => [],
      'for' => [],
    ],
    'input' => [
      'type' => [],
      'name' => [],
      'class' => [],
      'value' => [],
      'readonly' => [],
      'required' => [],
      'checked' => [],
      'min' => [],
      'disabled' => [],
      'style' => [],
      'data-show-if-active' => []
    ],
    'select' => [
      'name' => [],
      'class' => [],
      'multiple' => [],
      'required' => [],
      'disabled' => [],
    ],
    'option' => [
      'value' => [],
      'selected' => []
    ],
    'div' => [
      'class' => [],
      'style' => [],
      'data-depends-of' => [],
    ],
    'section' => [
      'class' => [],
      'style' => [],
    ]
  ];


  private array $input_escaping_params = [
    'input' => [
      'type' => [],
      'name' => [],
      'class' => [],
      'value' => [],
      'readonly' => [],
      'required' => [],
      'checked' => [],
      'min' => [],
      'disabled' => [],
      'style' => [],
      'data-show-if-active' => []
    ],
    'label' => [
      'class' => [],
      'style' => [],
      'for' => [],
    ],
    'div' => [
      'class' => [],
      'style' => [],
      'data-depends-of' => [],
    ],
  ];

  private array $select_escaping_params = [
    'select' => [
      'name' => [],
      'class' => [],
      'multiple' => [],
      'required' => [],
      'disabled' => [],
    ],
    'div' => [
      'class' => [],
      'style' => []
    ],
    'option' => [
      'value' => [],
      'selected' => []
    ],
  ];

  public function __construct()
  {
    /**
     *WordPress will check the styles against a list of allowed properties and it will still strip the style attribute
     * if none of the styles are safe. We should add display to list as follows to let your code work
     */
    add_filter('safe_style_css', function ($styles) {
      $styles[] = 'display';
      return $styles;
    });
  }

  /**
   * @param string $name
   * @param string $value
   * @param string $placeholder
   * @param bool $required
   * @param string $validation_slug actually class for js to validation on enter input value
   * @param bool $readonly
   */
  protected function render_text_field_html(string $name, string $value, string $placeholder, bool $required, string $validation_slug, bool $readonly): string
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

    return $input;
  }

  /**
   * @param string $name
   * @param string $classes
   * @param string $selected
   * @param array $options
   */
  protected function render_simple_select_html(string $name, string $classes, string $selected, array $options)
  {
    $select = '<select name="' . esc_attr($name) . '" class=" ' . esc_attr($classes) . ' ">';

    foreach ($options as $option) {

      $select .= '<option value="' . esc_attr($option) . '" ';

      if ($selected && ($option == $selected)) {
        $select .= 'selected';
      }

      $select .= '>' . esc_attr($option) . '</option>';
    }

    $select .= '</select>';

    return $select;
  }

  /**
   * @param string $name
   * @param string $classes
   * @param array $selected
   * @param array $options
   * @param bool|string $required
   * @param bool|string $disabled
   * @return string
   */
  protected function render_multiple_select_html(string $name, string $classes, array $selected, array $options, bool|string $required, bool|string $disabled = false): string
  {
    $select = '<div class="onix-beautiful-select"> <select multiple="multiple" name="' . esc_attr($name) . '" class=" ' . esc_attr($classes) . '" ';

    if ($required) {
      $select .= $required;
    }

    if ($disabled) {
      $select .= ' ' . $disabled;
    }

    $select .= '>';

    foreach ($options as $option) {
      $current = (!empty($selected) && in_array($option, $selected)) ? 'selected' : '';
      $select .= '<option value="' . esc_attr($option) . '" ' . $current . '>' . esc_attr($option) . '</option>';
    }

    return $select . '</select></div>';
  }

  protected function render_switcher_checkbox_html(string $option_name, string $name, bool $checked, string|bool $label = false)
  {
    $checkbox = ($label)? $label : '';

    $checkbox .= '<label for="' . esc_attr($option_name . '[' . $name . ']') . '" class="switch"><input type="checkbox" class="checkbox-switcher" value="1" ';
    if ($option_name && $name) {
      $checkbox .= 'name="' . esc_attr($option_name . '[' . $name . ']') . '" ';
    }

    if ($checked) {
      $checkbox .= "checked";
    }
    $checkbox .= ' > </label>';

    return $checkbox;
  }

  /**
   * @param string $name
   * @param string $value
   * @param string $params checked or disabled ore both
   * @param string $label
   * @return string
   */
  protected function render_radio_input(string $name, string $value, string $params, string $label): string
  {
    $radio = '<label class="oh-radio-input"> <input type="radio" value="';
    if ($value) {
      $radio .= $value;
    }

    $radio .= '" name="' . esc_attr($name) . '" ' . $params . ' >' . $label . ' </label> ';
    return $radio;
  }

  /**
   * @param string $name
   * @param string $value
   * @param string $params
   * @param string $label
   */
  protected function render_number_input(string $name, string $value, string $params, string $label): string
  {
    return '<label> ' . $label . ' <input type="number" name="' . esc_attr($name) . '" value="' . $value . '" ' . $params . ' > </label>';
  }

  /**
   * @param string $additional_content - section, that need to be shown
   */
  protected function render_block_for_additional_content(string $additional_content)
  {
    return '</div></div> <div> </div><div class="oh-field-additional-content">' . $additional_content;
  }


  protected function get_input_escaping_params(): array
  {
    return $this->input_escaping_params;
  }

  protected function get_select_escaping_params(): array
  {
    return $this->select_escaping_params;
  }

  protected function get_common_escaping_params(): array
  {
    return $this->common_escaping_params;
  }
}
