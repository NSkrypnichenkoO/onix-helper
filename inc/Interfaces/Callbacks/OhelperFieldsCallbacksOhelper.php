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
class OhelperFieldsCallbacksOhelper extends OhelperBaseCallbacks
{

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

    $output = get_option('onix_meta_box_fields');

    if (isset($_POST['remove'])) {
      //Sanitizes a string into a slug
      $slug = sanitize_title($_POST['remove']);

      delete_metadata('post', 0, $slug, false, true);

      unset($output[$slug]);
      return $output;
    }
    $option_type = 'fields_section_slug';



    /* at first time this function called twice, and on the second time as input passed already finished array
   * we need just to return it. it is not the best solution check if exist $input['post_type'], but i cant find better
   */
    if (!isset($input[$option_type])) {
      return $input;
    }

    $current_key = $input[$option_type] = sanitize_title($input[$option_type]);

    if (isset($input['fields_section_title'])) {
      $input['fields_section_title'] = sanitize_text_field($input['fields_section_title']);
    }
    if (isset($input['fields_section_screen'])) {
      $input['fields_section_screen'] = array_map('sanitize_text_field', $input['fields_section_screen']);
    }

    if (isset($input['fields_section_repeater_max_count'])) {
      $position = sanitize_text_field($input['fields_section_repeater_max_count']);
      $input['fields_section_repeater_max_count'] = (int)$input['fields_section_repeater_max_count'];

      // for now it can be number with white-spaces...
//      if ($this->check_if_contains_numbers($position)) {
//        $input['fields_section_repeater_max_count'] = (int)(preg_replace("/[^0-9]/", "", $position));
//      } else {
//        unset($input['fields_section_repeater_max_count']);
//      }
    }

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
    $args['object_edit_mode'] = $this->fields_edit_mode();
    $this->create_text_field($args);
  }

  /**
   * @param array $args
   */
  public function multi_select_field(array $args)
  {
    $edit_field = $this->fields_edit_mode();
    $options = get_post_types(['_builtin' => false,]);
    array_push($options, 'link', 'comment', 'post', 'page', 'attachment');
    $this->create_multiple_select($args, $edit_field, $options);
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
}
