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


    $current_key = $input[$option_type] = sanitize_title($input[$option_type]);

    if (isset($input['plural_name'])) {
      $input['plural_name'] = sanitize_text_field($input['plural_name']);
    }
    if (isset($input['singular_name'])) {
      $input['singular_name'] = sanitize_text_field($input['singular_name']);
    }
    if (isset($input['description'])) {
      $input['description'] = sanitize_textarea_field($input['description']);
    }

    // may be should do the most hurd chek, with list of values
    if (isset($input['object_type'])) {
      $input['object_type'] = array_map('sanitize_text_field', $input['object_type']);;
    }

    // мы же знаем все эти ключи... мы их можем хранить и ходить по ним циклом
    $this->fill_array_element_with_value($input, 'public');
    $this->fill_array_element_with_value($input, 'publicly_queryable');
    $this->fill_array_element_with_value($input, 'hierarchical');
    $this->fill_array_element_with_value($input, 'show_ui');
    $this->fill_array_element_with_value($input, 'show_in_menu');
    $this->fill_array_element_with_value($input, 'show_in_nav_menus');
    $this->fill_array_element_with_value($input, 'show_in_rest');
    $this->fill_array_element_with_value($input, 'show_tagcloud');
    $this->fill_array_element_with_value($input, 'show_in_quick_edit');
    $this->fill_array_element_with_value($input, 'show_admin_column');
    $this->fill_array_element_with_value($input, 'query_var');
    $this->fill_array_element_with_value($input, 'sort');


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
   * @param $args
   */
  public function text_field($args)
  {
    $args['object_edit_mode'] = $this->tax_edit_mode();
    $this->create_text_field($args);
  }


  /**
   * @param array $args
   */
  public function checkbox_with_default_field(array $args)
  {
    $edit_tax = $this->tax_edit_mode();
    $this->create_checkbox_with_default_option($args, $edit_tax);
  }

  /**
   * @param array $args
   */
  public function true_false_radio_buttons(array $args)
  {
    $edit_tax = $this->tax_edit_mode();
    $this->create_true_false_radio_buttons($args, $edit_tax);
  }

  public function render_switcher_checkbox_tax(array $args)
  {
    $edit_tax = $this->tax_edit_mode();
    $this->render_switcher_checkbox($args,  $edit_tax);
  }

  /**
   * @param array $args
   */
  public function multi_select_field(array $args)
  {
    $edit_tax = $this->tax_edit_mode();
    $options = get_post_types(['show_ui' => true]);
    $this->create_multiple_select($args, $edit_tax, $options);
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
