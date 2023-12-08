<?php
/**
 * @package onix-meta-box
 */


namespace Onixhelper\Interfaces\Callbacks;

use Onixhelper\System\OhelperBaseController;


/**
 * Class OhelperCPTCallbacksOhelper here we will declare all callbacks. for more cleaner code
 * @package Onixhelper\Interfaces\Callbacks
 */
class OhelperTaxCallbacksOhelper extends OhelperBaseCallbacks
{

  /**
   * method just to print section title
   */
  public function tax_pages_section_manager()
  {
    $base_controller = new OhelperBaseController();
    $args = [
      'title' => 'Manage Taxonomies',
    ];
    require_once $base_controller->omb_path . 'templates/template-parts/functional-parts/options-navigation-bar.php';
  }

  /**
   * @param $input
   * @return mixed
   */
  public function tax_sanitise($input): mixed
  {
    $output = get_option('onix_meta_box_tax');

    if (isset($_POST['remove'])) {
      //Sanitizes a string into a slug
      $slug = sanitize_title($_POST['remove']);
      $this->delete_all_terms($slug);

      // we need to unset array by the $_POST['remove'] as key
      unset($output[$slug]);
      return $output;
    }

    $option_type = 'taxonomy';

    /* at first time this function called twice, and on the second time as input passed already finished array
    * we need just to return it. it is not the best solution check if exist $input['post_type'], but i cant find better
    */
    if (!isset($input[$option_type])) {
      return $input;
    }

    $safe_data = [];
    $current_key = $safe_data[$option_type] = sanitize_title($input[$option_type]);

    if (isset($input['plural_name'])) {
      $safe_data['plural_name'] = sanitize_text_field($input['plural_name']);
    }
    if (isset($input['singular_name'])) {
      $safe_data['singular_name'] = sanitize_text_field($input['singular_name']);
    }
    if (isset($input['description'])) {
      $safe_data['description'] = sanitize_textarea_field($input['description']);
    }

    // may be should do the most hurd chek, with list of values
    if (isset($input['object_type'])) {
      $safe_data['object_type'] = array_map('sanitize_text_field', $input['object_type']);;
    }

    // мы же знаем все эти ключи... мы их можем хранить и ходить по ним циклом
    $this->fill_array_element_with_value($input, 'public', $safe_data);
    $this->fill_array_element_with_value($input, 'publicly_queryable', $safe_data);
    $this->fill_array_element_with_value($input, 'hierarchical', $safe_data);
    $this->fill_array_element_with_value($input, 'show_ui', $safe_data);
    $this->fill_array_element_with_value($input, 'show_in_menu', $safe_data);
    $this->fill_array_element_with_value($input, 'show_in_nav_menus', $safe_data);
    $this->fill_array_element_with_value($input, 'show_in_rest', $safe_data);
    $this->fill_array_element_with_value($input, 'show_tagcloud', $safe_data);
    $this->fill_array_element_with_value($input, 'show_in_quick_edit', $safe_data);
    $this->fill_array_element_with_value($input, 'show_admin_column', $safe_data);
    $this->fill_array_element_with_value($input, 'query_var', $safe_data);
    $this->fill_array_element_with_value($input, 'sort', $safe_data);


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
    $edit_tax = $this->tax_edit_mode();
    $base_content = $this->create_text_field($args, $edit_tax);;
    $this->omb_render_fields_box($args, $base_content, $edit_tax);
  }


  /**
   * @param array $args
   */
  public function true_false_radio_buttons(array $args)
  {
    $edit_tax = $this->tax_edit_mode();
    $base_content = $this->create_true_false_radio_buttons($args, $edit_tax);
    $this->omb_render_fields_box($args, $base_content, $edit_tax);
  }

  public function render_switcher_checkbox_tax(array $args)
  {
    $edit_tax = $this->tax_edit_mode();
    $this->create_default_checkbox($args, $edit_tax);
  }

  /**
   * @param array $args
   */
  public function multi_select_field(array $args)
  {
    $edit_tax = $this->tax_edit_mode();
    $options = get_post_types(['show_ui' => true]);
    $base_content = $this->create_multiple_select($args, $edit_tax, $options);
    $this->omb_render_fields_box($args, $base_content, $edit_tax);
  }

  /**
   * inner method to check if it is edit mode for cpt
   *
   * @return false|string false if it is not edit mode, cpt slug if it is its edit mode
   */
  private function tax_edit_mode(): false|string
  {
    return isset($_POST['edit_tax']) ? sanitize_title($_POST['edit_tax']) : false;
  }

  private function delete_all_terms(string $slug)
  {
    $terms = get_terms([
      'taxonomy' => $slug,
      'hide_empty' => false,
    ]);
    foreach ($terms as $term) {
      wp_delete_term($term->term_id, $slug);
    }
  }
}
