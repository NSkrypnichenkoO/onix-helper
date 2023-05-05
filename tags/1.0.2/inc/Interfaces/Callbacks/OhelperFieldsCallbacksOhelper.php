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
//    var_dump($input);
//    die('before save');

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
    if (isset($input['fields_section_screen_pages'])) {
      $input['fields_section_screen_pages'] = array_map('sanitize_text_field', $input['fields_section_screen_pages']);
    }

    if (isset($input['fields_repeater_section_count'])) {
      $input['fields_repeater_section_count'] = sanitize_text_field($input['fields_repeater_section_count']);
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
    $edit_field = $this->fields_edit_mode();
    $base_content = $this->create_text_field($args, $edit_field);
    $this->omb_render_fields_box($args, $base_content, $edit_field);
  }


  public function fields_section_screen(array $args)
  {
    $edit_field = $this->fields_edit_mode();
    $base_content = $this->multi_select_field($args);

    $additional_args = [
      'option_name' =>'onix_meta_box_fields',
      'label_for'=> 'fields_section_screen_pages',
    ];

    /**
     * fo render additional sections we need to know, which options are already in use
     */
    $name = sanitize_title($args['label_for']);
    $option_name = sanitize_key($args['option_name']);
    $options_in_use = [];

    if ($edit_field) {
      $select = get_option($option_name);
      if (isset($select[$edit_field][$name])) {
        $options_in_use  = $select[$edit_field][$name];
      }
    }

    /**
     * теперь, отталкиваясь от того, что активировано, мы можем передавать класс дополнительным секциям, что бы прятать
     * или показывать дополнительные секции.
     */
    $page_selector_classes = 'oh-pages-selector ';
    if(!in_array('page', $options_in_use)){
      $page_selector_classes .= 'oh-hide-this-section';
      $additional_args['disabled'] = true;
    }

    $additional_content = $this->prepare_additional_section($this->multi_Select_page_list($additional_args), $page_selector_classes);
    $this->omb_render_fields_box($args, $base_content, $edit_field, $additional_content);
  }


  /**
   * @param $args
   */
  public function text_field_if_radio_true($args)
  {
    $edit_field = $this->fields_edit_mode();
    $base_content = $this->create_text_field_if_radio_true($args, $edit_field);
    $this->omb_render_fields_box($args, $base_content, $edit_field);

  }

  /**
   * @param array $args
   */
  public function multi_select_field(array $args)
  {
    $edit_field = $this->fields_edit_mode();
    $options = get_post_types(['_builtin' => false,]);
    array_push($options, 'link', 'comment', 'post', 'page', 'attachment');
    return $this->create_multiple_select($args, $edit_field, $options);
  }

  public function multi_Select_page_list(array $args)
  {

    $edit_field = $this->fields_edit_mode();

    $options = ['All'];

    $pages = get_pages();
    foreach ($pages as $page) {

      array_push($options, $page->post_title);

      /**
       * TODO add ability ta chose page by template
       */
//      $slug = get_page_template_slug($page->ID);
//      if ($slug) {
//        var_dump(get_page_template_slug($page->ID));
//      }
    }

    return $this->create_multiple_select($args, $edit_field, $options);
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
