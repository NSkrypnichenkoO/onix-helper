<?php

/**
 * @package onix-meta-box
 */

namespace Onixhelper\System\FieldsClasses;


use Onixhelper\System\OhelperBaseController;

class OhelperConfigurableMetabox extends OhelperBaseController
{
  // for which screen should show this box
  private array $screens_to_show = [];

  // slug of this section
  private string $section_slug = '';

  // title of this section
  private string $section_title = '';

  // if user choose some pages from the list of all pages
  private array $current_pages = [];

  // list of fields, that should be created
  private $fields_list = [];

  //context
  private string $section_context = 'advanced';

  //priority
  private string $section_priority = 'high';

  // number of section fields
  private int $number_of_fields;

  // max count if it is repeater
  private int $max_section_count;


  public function __construct(array $parameters)
  {
    parent::__construct();
    $this->screens_to_show = $parameters['screen_to_show'];
    $this->section_slug = isset($parameters['section_slug']) ? $parameters['section_slug'] : 'onix_helper_' . get_the_ID();
    $this->section_title = $parameters['section_title'];
    $this->max_section_count = (isset($parameters['max_section_count']) && $parameters['max_section_count']) ? $parameters['max_section_count'] : 1;
    $this->section_context = (isset($parameters['context']) && $parameters['context']) ? $parameters['context'] : $this->section_context;
    $this->section_priority = (isset($parameters['priority']) && $parameters['priority']) ? $parameters['priority'] : $this->section_priority;

    if (isset($parameters['pages']) && !array_key_exists('all', $parameters['pages'])) {
      $this->current_pages = $parameters['pages'];
    }

    $this->fields_list = $parameters['fields_list'];
    $this->number_of_fields = count($this->fields_list);

    add_action('add_meta_boxes', array($this, 'add_metabox'));

    // to save data in the box
    $this->omb_save_posts_prepare();
  }


  public function add_metabox()
  {
    global $post;

    if (!empty($this->current_pages)) { // if we need to add this box just for current pages
      if (in_array($post->ID, $this->current_pages)) {
        add_meta_box($this->section_slug, $this->section_title,
          array($this, 'render_metabox'), $this->screens_to_show, $this->section_context, $this->section_priority);
      }
      /*
      need to check if we have also else post types to add this box to do this we must check length of $this->screen_to_show.
      it must be more then one
      */
      if (count($this->screens_to_show) > 1) {
        // need to remove 'page' and also create meta box
        if (($key = array_search('page', $this->screens_to_show)) !== false) {
          unset($this->screens_to_show[$key]);
          add_meta_box($this->section_slug, $this->section_title,
            array($this, 'render_metabox'), $this->screens_to_show, $this->section_context, $this->section_priority);
        }
      }

    } else {
      add_meta_box($this->section_slug, $this->section_title,
        array($this, 'render_metabox'), $this->screens_to_show, $this->section_context, $this->section_priority);
    }
  }


  /**
   * render all field section on the page
   * @param $post - current page
   */
  public function render_metabox($post)
  {
    $max_count = $this->max_section_count;
    $slug = $this->section_slug;
    // Add an nonce field so we can check for it later.
    wp_nonce_field('ohelper_inner_custom_box', 'ohelper_inner_custom_box_nonce');
    ?>
    <div class="form-table omb-section-fields <?php echo esc_attr($this->section_slug) ?>-info"
         max-section-count="<?php echo esc_attr($max_count) ?>"
         id="<?php echo esc_attr($slug) ?>">

      <div class="<?php echo esc_attr($slug) ?>-list oh-list-of-section-fields">
        <?php
        $list_of_values = get_post_meta($post->ID, $slug, true);

        //if we already has data
        if (is_array($list_of_values)) {
          foreach ($list_of_values as $index => $block) {
            self::show_metabox($block, $index);
          }
        } else {
          self::show_metabox([]);
        }
        ?>
      </div>

      <?php if ($max_count > 1 || $max_count < 0) { ?>
        <div class="oh-bottom-actions">
          <span
            class="add-field-block add-new-<?php echo esc_attr($slug) ?>"> <?php echo __('Add row', 'onix-helper') ?></span>
        </div>
      <?php } ?>
    </div>
    <?php
  }


  /**
   * function to show fields of different types. Can feel fields with inform ore leave empty
   * @param $block
   * @param int $index
   */
  private function show_metabox($block, $index = 0)
  {
    echo '<div class="oh-field-item item-' . esc_attr($this->section_slug) . '" id="item-' . esc_attr($this->section_slug) . '"> <div class="oh-fields-block">';
    foreach ($this->fields_list as $field) {
      $field_slug = $field['slug'];
      $value = isset($block[$field_slug]) ? $block[$field_slug] : '';
      self::show_metabox_field($field_slug, $value, $field['title'], $field['type'], $index);
    }
    echo '</div><div class="oh-actions-container"><span class="dashicons dashicons-trash remove-fields-block remove-new-' . esc_attr($this->section_slug) . '"></span></div></div>';
  }


  /**
   * render html of fields
   *
   * @param $name_of_field
   * @param $field_value
   * @param $input_label
   * @param $type_field
   */
  private function show_metabox_field($name_of_field, $field_value, $input_label, $type_field, $index)
  {

    switch ($type_field) {
      case 'text':
        $text = '
        <label for="' . esc_attr($name_of_field) . '"> ' . esc_html($input_label) . ' 
		    <p><input type="text" 
		    name="' . esc_attr($this->section_slug) . '[' . esc_attr($index) . '][' . esc_attr($name_of_field) . ']' . '" 
		    value="' . esc_html($field_value) . '"></p></label>';
        echo wp_kses($text, $this->ksess_input_params());
        break;

      case 'number':
        $text = '
        <label for="' . esc_attr($name_of_field) . '"> ' . esc_html($input_label) . ' 
		    <p><input type="number" 
		    name="' . esc_attr($this->section_slug) . '[' . esc_attr($index) . '][' . esc_attr($name_of_field) . ']' . '" 
		    value="' . esc_html($field_value) . '"></p></label>';
        echo wp_kses($text, $this->ksess_input_params());
        break;

      case 'password':
        $text = '
        <label for="' . esc_attr($name_of_field) . '"> ' . esc_html($input_label) . ' 
		    <p><input type="password" 
		    name="' . esc_attr($this->section_slug) . '[' . esc_attr($index) . '][' . esc_attr($name_of_field) . ']' . '" 
		    value="' . esc_html($field_value) . '"></p></label>';
        echo wp_kses($text, $this->ksess_input_params());
        break;

      case 'url':
        $text = '
        <label for="' . esc_attr($name_of_field) . '"> ' . esc_html($input_label) . '
		    <p><input type="url" pattern="https://.*"
		    name="' . esc_attr($this->section_slug) . '[' . esc_attr($index) . '][' . esc_attr($name_of_field) . ']' . '"
		    value="' . esc_html($field_value) . '"> <span class="validity"></span></p></label>';
        echo wp_kses($text, $this->ksess_input_params());
        break;

      case 'email':
        $text = '
        <label for="' . esc_attr($name_of_field) . '"> ' . esc_html($input_label) . ' 
		    <p><input type="email" 
		    name="' . esc_attr($this->section_slug) . '[' . esc_attr($index) . '][' . esc_attr($name_of_field) . ']' . '" 
		    value="' . esc_html($field_value) . '"></p></label>';
        echo wp_kses($text, $this->ksess_input_params());
        break;

      case 'image':
        $default = $this->omb_url . 'assets/img/no-image.png';
        esc_html_e('image', 'onix-helper');
        if ($field_value) {
          $image_attributes = wp_get_attachment_image_src($field_value, array(100, 100));
          $src = $image_attributes[0];
        } else {
          $src = $default;
        }
        $img = '
				<div class="oh-image-block">
				<img data-src="' . esc_attr($default) . '" src="' . esc_url($src) . '" />
					<span>
						<input type="hidden" name="' . esc_attr($this->section_slug) . '[' . esc_attr($index) . '][' . esc_attr($name_of_field) . ']' . '" id="' . esc_attr($name_of_field) . '[]" value="' . esc_html($field_value) . '" />
						<button type="submit" class="upload_image_button button">' . __('Upload', 'onix-helper') . '</button>
					</span>
				</div>';
        echo wp_kses($img, $this->ksess_image_params());
        break;

      case 'textarea':
        $text = '
        <label for="' . esc_attr($name_of_field) . '"> ' . esc_html($input_label) . ' 
		    <p><textarea name="' . esc_attr($this->section_slug) . '[' . esc_attr($index) . '][' . esc_attr($name_of_field) . ']' . '" 
		    >' . esc_html($field_value) . '</textarea></p></label>
		    ';
        echo wp_kses($text, $this->ksess_textarea_params());
        break;
    }
  }

  /**
   * save and sanitise data
   * @param $post_id
   */
  public function save_metabox($post_id)
  {
    // Check if our nonce is set.
    if (!isset($_POST['ohelper_inner_custom_box_nonce'])) {
      return $post_id;
    }

    $nonce = $_POST['ohelper_inner_custom_box_nonce'];

    // Verify that the nonce is valid.
    if (!wp_verify_nonce($nonce, 'ohelper_inner_custom_box')) {
      return $post_id;
    }

    if (isset($_POST['form-admin_nonce']) && wp_verify_nonce($_POST['form-admin_nonce'], 'form-admin_action')) {
      return $post_id;
    }
    if (isset($_POST['form-cpt-manager_nonce']) && wp_verify_nonce($_POST['form-cpt-manager_nonce'], 'form-cpt-manager_action')) {
      return $post_id;
    }
    if (isset($_POST['form-tax-manager_nonce']) && wp_verify_nonce($_POST['form-tax-manager_nonce'], 'form-tax-manager_action')) {
      return $post_id;
    }
    if (isset($_POST['form-fields-manager_nonce']) && wp_verify_nonce($_POST['form-fields-manager_nonce'], 'form-fields-manager_action')) {
      return $post_id;
    }


    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
      return $post_id;
    }

    if (!isset($_POST[$this->section_slug])) {
      error_log('no data present in post array');
      return $post_id;
    }
    // Check the user's permissions.
    if (isset($_POST['post_type']) && $_POST['post_type'] == 'page') {
      if (!current_user_can('edit_page', $post_id)) {
        return $post_id;
      }
    } else {
      if (!current_user_can('edit_post', $post_id)) {
        return $post_id;
      }
    }

    // it's safe to save the data now.
    $field_groups_from_form = $_POST[$this->section_slug];
    $result = [];

    foreach ($field_groups_from_form as &$field_group_from_form) {

      foreach ($field_group_from_form as $key => $single_field_value) {
        if (is_array($single_field_value)) {// ned if we create multiselect, for example
        } else {
          $field_group_from_form[sanitize_title($key)] = sanitize_textarea_field($single_field_value);
        }
      }
      $result[] = $field_group_from_form;
    }

    // Update the meta field.
    update_post_meta($post_id, $this->section_slug, $result);
  }

  /**
   * write in a foreach loop save_post_ action for each needed post type
   */
  public function omb_save_posts_prepare()
  {
    foreach ($this->screens_to_show as $item) {
      add_action('save_post_' . $item, [$this, 'save_metabox']);
    }
  }

  private function ksess_input_params()
  {
    return [
      'p' => [],
      'label' => [
        'for' => [],
      ],
      'span' => [
        'class' => []
      ],
      'input' => [
        'pattern' => [],
        'type' => [],
        'name' => [],
        'value' => [],
      ],
    ];
  }

  private function ksess_image_params()
  {
    return [
      'div' => [
        'class' => []
      ],
      'img' => [
        'data-src' => [],
        'src' => [],
        'width' => [],
        'height' => [],
      ],
      'input' => [
        'type' => [],
        'name' => [],
        'id' => [],
        'value' => [],
      ],
      'span' => [],
      'button' => [
        'type' => [],
        'class' => [],
      ],
    ];
  }

  private function ksess_textarea_params()
  {
    return [
      'p' => [],
      'label' => [
        'for' => [],
      ],
      'textarea' => [
        'type' => [],
        'name' => [],
        'value' => [],
      ],
    ];
  }
}
