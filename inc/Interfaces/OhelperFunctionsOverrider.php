<?php
/**
 * @package onix-meta-box
 */

namespace Onixhelper\Interfaces;

class OhelperFunctionsOverrider
{


  /**
   * Prints out the settings fields for a particular settings section.
   * Part of the Settings API. Use this in a settings page to output a specific section. Should normally be called by do_settings_sections() rather than directly.
   * @param string $page Slug title of the admin page whose settings fields you want to show.
   * @param string $section Slug title of the settings section whose fields you want to show.
   */
  public static function omb_do_settings_fields(string $page, string $section)
  {
    global $wp_settings_fields;

    if (!isset($wp_settings_fields[$page][$section])) {
      return;
    }
    $list = (array)$wp_settings_fields[$page][$section];

    foreach ($list as $field) {

      $class = ' class="onix-helper-field-block" id="' . $field['id'] . '"';

      if (!empty($field['args']['class'])) {
        $class = ' class="onix-helper-field-block" id="' . $field['id'] . '"';
      }

      echo "<div {$class}> ";

      if (!empty($field['args']['label_for'])) {
        echo '<div scope="row" class="row"><label for="' . esc_attr($field['args']['label_for']) . '">' . $field['title'] . '</label>';
      } else {
        echo '<div scope="row" class="row">' . $field['title'];
      }

      echo '<div class="row-right-part">';

      if (isset($field['args']['has_default']) && is_callable($field['args']['has_default'])) {
        // render switcher
        call_user_func($field['args']['has_default'], $field['args']);
        echo '</div></div><div class="oh-field-content">';
        call_user_func($field['callback'], $field['args']);
        echo '</div>';
      } else {
        call_user_func($field['callback'], $field['args']);
        echo '</div></div>';
      }

      $description = isset($field['args']['description']) ? $field['args']['description'] : '';
      echo '<div class="onix-helper-description"> 
        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
        <g clip-path="url(#clip0_37_3545)">
        <circle cx="9.99996" cy="5.83333" r="0.833333" fill="#D1D5DB"/>
        <path d="M9.16662 8.33333H9.99996V14.1667M18.3333 10C18.3333 14.6024 14.6023 18.3333 9.99996 18.3333C5.39759 18.3333 1.66663 14.6024 1.66663 10C1.66663 5.39762 5.39759 1.66666 9.99996 1.66666C14.6023 1.66666 18.3333 5.39762 18.3333 10Z" stroke="#D1D5DB" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </g><defs><clipPath id="clip0_37_3545"> <rect width="20" height="20" fill="white"/></clipPath></defs>
        </svg>' . $description . '</div>';

      echo '</div>';
    }
  }

  public static function omb_do_settings_sections($page)
  {

    global $wp_settings_sections, $wp_settings_fields;

    if (!isset($wp_settings_sections[$page])) {
      return;
    }

    foreach ((array)$wp_settings_sections[$page] as $key => $section) {

      if ('' !== $section['before_section']) {
        if ('' !== $section['section_class']) {
          echo wp_kses_post(sprintf($section['before_section'], esc_attr($section['section_class'])));
        } else {
          echo wp_kses_post($section['before_section']);
        }
      }

      if ($section['title']) {
        echo "<h2>{$section['title']}</h2>\n";
      }

      if ($section['callback']) {
        call_user_func($section['callback'], $section);
      }

      if (!isset($wp_settings_fields) || !isset($wp_settings_fields[$page]) || !isset($wp_settings_fields[$page][$section['id']])) {
        continue;
      }
      echo '<div class="form-table onix-helper-table" role="presentation">';
      self::omb_do_settings_fields($page, $section['id']);
      echo '</div>';

      if ('' !== $section['after_section']) {
        echo wp_kses_post($section['after_section']);
      }
    }
  }
}


