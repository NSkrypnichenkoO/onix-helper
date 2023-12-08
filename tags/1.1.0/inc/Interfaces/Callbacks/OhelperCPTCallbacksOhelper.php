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
class OhelperCPTCallbacksOhelper extends OhelperBaseCallbacks
{

  /**
   * method just to print section title
   */
  public function cpt_pages_section_manager()
  {
    $base_controller = new OhelperBaseController();
    $args = [
      'title' => 'Manage CPT',
    ];
    require_once $base_controller->omb_path . 'templates/template-parts/functional-parts/options-navigation-bar.php';
  }


  /**
   * we haw multiple array with all parameters for cpt. single cpt saved as associative array, list of cpt-s
   * saved as associative array too, where the kee is cpt slug and the value - array of all current cpt parameters
   *
   * @param $input
   *
   * @return false|mixed|void
   */
  public function cpt_sanitise($input): mixed
  {

    $output = get_option('onix_meta_box_cpt');

    if (isset($_POST['remove'])) {
      //Sanitizes a string into a slug
      $slug = sanitize_title($_POST['remove']);

      // we need to unset array by the $_POST['remove'] as key
      unset($output[$slug]);;
      $this->remove_all_posts($slug);
      return $output;
    }
    $option_type = 'post_type';

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
    if (isset($input['query_var'])) {
      $safe_data['query_var'] = sanitize_text_field($input['query_var']);
    }

    if (isset($input['menu_icon'])) {
      $safe_data['menu_icon'] = sanitize_title($input['menu_icon']);
    }

    if (isset($input['description'])) {
      $safe_data['description'] = sanitize_textarea_field($input['description']);
    }

    if (isset($input['menu_position'])) {
      $position = sanitize_text_field($input['menu_position']);
      // for now it can be number with white-spaces...
      if ($this->check_if_contains_numbers($position)) {
        $safe_data['menu_position'] = (int)(preg_replace("/[^0-9]/", "", $position));
      }
    }

    if (isset($input['capability_type'])) {
      $write_values = ['post', 'page'];
      $value = sanitize_text_field($input['capability_type']);
      if (in_array($value, $write_values)) {
        $safe_data['capability_type'] = $value;
      }
    }

    // may be should do the most hurd chek, with list of values
    if (isset($input['supports'])) {
      $safe_data['supports'] = array_map('sanitize_text_field', $input['supports']);
    }

    $this->fill_array_element_with_value($input, 'public', $safe_data);
    $this->fill_array_element_with_value($input, 'publicly_queryable',$safe_data);
    $this->fill_array_element_with_value($input, 'exclude_from_search',$safe_data);
    $this->fill_array_element_with_value($input, 'show_ui',$safe_data);
    $this->fill_array_element_with_value($input, 'show_ui',$safe_data);
    $this->fill_array_element_with_value($input, 'show_in_menu',$safe_data);
    $this->fill_array_element_with_value($input, 'show_in_nav_menus',$safe_data);
    $this->fill_array_element_with_value($input, 'hierarchical',$safe_data);
    $this->fill_array_element_with_value($input, 'show_in_rest',$safe_data);
    $this->fill_array_element_with_value($input, 'has_archive',$safe_data);

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


  public function text_field($args)
  {
    $edit_cpt = $this->cpt_edit_mode();
    $base_content = $this->create_text_field($args, $edit_cpt);
    $this->omb_render_fields_box($args, $base_content, $edit_cpt);
  }

  /**
   * @param array $args
   */
  public function true_false_radio_buttons(array $args)
  {
    $edit_cpt = $this->cpt_edit_mode();
    $base_content = $this->create_true_false_radio_buttons($args, $edit_cpt);
    $this->omb_render_fields_box($args, $base_content, $edit_cpt);
  }

  public function select_field(array $args)
  {
    $edit_cpt = $this->cpt_edit_mode();
    $base_content = $this->create_simple_select($args, $edit_cpt);
    $this->omb_render_fields_box($args, $base_content, $edit_cpt);
  }


  public function multi_select_field(array $args)
  {
    $edit_cpt = $this->cpt_edit_mode();
    $options = isset($args['select_args']) ? $args['select_args'] : [];
    $base_content = $this->create_multiple_select($args, $edit_cpt, $options);
    $this->omb_render_fields_box($args, $base_content, $edit_cpt);
  }

  public function text_field_if_radio_true(array $args){
    $edit_cpt = $this->cpt_edit_mode();
    $base_content = $this->create_text_field_if_radio_true($args, $edit_cpt);
    $this->omb_render_fields_box($args, $base_content, $edit_cpt);
  }

  /**
   * inner method to check if it is edit mode for cpt
   *
   * @return false|string false if it is not edit mode, cpt slug if it is its edit mode
   */
  private function cpt_edit_mode()
  {
    return isset($_POST['edit_cpt']) ? sanitize_title($_POST['edit_cpt']) : false;
  }

  private function remove_all_posts($post_type)
  {
    $posts = get_posts([
      'post_type' => $post_type,
      'post_status' => ['publish', 'draft', 'trash', 'future', 'pending', 'private', 'auto-draft', 'inherit']
    ]);

    if (is_array($posts)) {
      foreach ($posts as $post) {
        wp_delete_post($post->ID, true);
      }
    }
  }
}
