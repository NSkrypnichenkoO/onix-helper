<?php

/**
 * @package onix-meta-box
 */

namespace Onixhelper\System\FieldsClasses;


class OhelperConfigurableMetabox
{

  private $post_type;

  private bool|array $pages = false;

  private $meta_key;
  /*
   * id , name, context, priority
   */
  private $add_metabox_args = [];

  /*
   *  'should_repeat', 'fields_count'
   */
  private $fields_list = [];

  /*
   *  number of section fields
   */
  private $number_of_fields;

  // max count if it is repeater
  private $max_section_count;


  public function __construct(array $parameter)
  {
    $this->post_type = $parameter['post_type'];

    if (isset($parameter['pages']) && !in_array('All', $parameter['pages'])) {
      $this->pages = $this->prepare_page_id_list($parameter['pages']);
    }

    $this->meta_key = $parameter['meta_key'] ? $parameter['meta_key'] : get_the_ID();

    $this->add_metabox_args = $parameter['meta_box_parameters'];
    $this->fields_list = $parameter['fields']['fields_list'];
    $this->number_of_fields = count($this->fields_list);
    $this->max_section_count = $parameter['fields']['max_section_count'];

    add_action('add_meta_boxes', array($this, 'add_metabox'));

    $this->omb_save_posts_prepare($this->post_type);

    add_action('admin_print_footer_scripts', array($this, 'show_assets'), 10, 999);
  }


  public function add_metabox()
  {
    global $post;

    if ($this->pages) {
      if (in_array($post->ID, $this->pages)) {
        add_meta_box($this->add_metabox_args['id'], $this->add_metabox_args['name'],
          array($this, 'render_metabox'), $this->post_type, $this->add_metabox_args['context'], $this->add_metabox_args['priority']);
      }
    } else {
      add_meta_box($this->add_metabox_args['id'], $this->add_metabox_args['name'],
        array($this, 'render_metabox'), $this->post_type, $this->add_metabox_args['context'], $this->add_metabox_args['priority']);
    }
  }


  /**
   * render all field section on the page
   * @param $post - current page
   */
  public function render_metabox($post)
  {
    ?>
    <table class="form-table omb-section-fields <?php echo esc_attr($this->meta_key) ?>-info"
           max-section-count="<?php echo esc_attr($this->max_section_count) ?>"
           id="<?php echo esc_attr($this->meta_key) ?>">
      <tr>
        <th>
          <?php echo esc_html($this->add_metabox_args['name']) ?>
          <span
            class="dashicons dashicons-plus-alt add-field-block add-new-<?php echo esc_attr($this->meta_key) ?>"></span>
        </th>
        <td class="<?php echo esc_attr($this->meta_key) ?>-list">
          <?php

          $list_of_values = get_post_meta($post->ID, $this->meta_key, true);
          $number_iteration = 0;

          //if we doesnt have fields yet
          if (is_array($list_of_values)) {
            $array_key = array_keys($list_of_values[$this->meta_key]);
            $number_iteration = count($list_of_values[$this->meta_key][$array_key[0]]);
          }

          if ($number_iteration > 0) {
            $section = [];
            for ($i = 0; $i < $number_iteration; $i++) {
              foreach ($array_key as $key) {
                $section [$key] = $list_of_values [$this->meta_key][$key][$i];
              }
              self::show_metabox($section);
            }

          } else {

            self::show_metabox([]);
          }
          ?>
        </td>
      </tr>
    </table>
    <?php
  }


  /**
   * function to show fields of different types. Can feel fields with inform ore leave empty
   * @param $section_item
   */
  private function show_metabox($section_item)
  {

    $number_of_fields = count($this->fields_list);
    ?>
    <span class="item-<?php echo esc_attr($this->meta_key) ?>" id="item-<?php echo esc_attr($this->meta_key) ?>">
		<?php
    for ($i = 0; $i < $number_of_fields; $i++) {

      $name_of_field = $this->fields_list[$i]['name'];
      $input_label = $this->fields_list[$i]['title'];
      $type = $this->fields_list[$i]['type'];

      $value = empty($section_item) ? '' : $section_item[$name_of_field];
      self::show_metabox_field($name_of_field, $value, $input_label, $type);
    } ?>
            <span
              class="dashicons dashicons-trash remove-fields-block remove-new-<?php echo esc_attr($this->meta_key) ?>"></span>
            </span>
    <?php
  }


  /**
   * render html of fields
   *
   * @param $name_of_field
   * @param $field_value
   * @param $input_label
   * @param $type_field
   */
  private function show_metabox_field($name_of_field, $field_value, $input_label, $type_field)
  {

    switch ($type_field) {
      case 'text':
        $text = '
        <label for="' . esc_attr($name_of_field) . '"> ' . esc_html($input_label) . ' 
		    <p><input type="text" 
		    name="' . esc_attr($this->meta_key) . '[' . esc_attr($name_of_field) . ']' . '[]" 
		    value="' . esc_html($field_value) . '"></p></label>';

        $tags = [
          'p' => [],
          'label' => [
            'for' => [],
          ],
          'input' => [
            'type' => [],
            'name' => [],
            'value' => [],
          ],
        ];
        echo wp_kses($text, $tags);
        break;
      case 'image':
        $default = get_stylesheet_directory_uri() . '/images/no-image.jpg';
        esc_html_e('image', 'onix-helper');
        if ($field_value) {
          $image_attributes = wp_get_attachment_image_src($field_value, array(100, 100));
          $src = $image_attributes[0];
        } else {
          $src = $default;
        }
        $img = '
				<div>
				<img data-src="' . esc_attr($default) . '" src="' . esc_url($src) . '" width="' . 90 . 'px" height="' . 90 . 'px" />
					<div>
						<input type="hidden" name="' . esc_attr($this->meta_key) . '[' . esc_attr($name_of_field) . ']' . '[]" id="' . esc_attr($name_of_field) . '[]" value="' . esc_html($field_value) . '" />
						<button type="submit" class="upload_image_button button">' . __('Upload', 'onix-helper') . '</button>
					</div>
				</div>
				';
        $tags = [
          'div' => [],
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
          'button' => [
            'type' => [],
            'class' => [],
          ],
        ];
        echo wp_kses($img, $tags);
        break;
    }
  }

  /**
   * save and sanitise data
   * @param $post_id
   */
  public function save_metabox($post_id)
  {
    if (!self::check_metadata_exist()) {
      error_log('check_metadata_exist');
      return;
    }

    $list_of_fields_name = self::create_array_from_fields_name();


    $result_array = [];

    foreach ($list_of_fields_name as $key) {

      $result_array[$this->meta_key][$key] = array_map('sanitize_text_field', $_POST[$this->meta_key][$key]);
    }

    update_post_meta($post_id, $this->meta_key, $result_array);
  }

  private function check_metadata_exist()
  {

    $list_of_fields_name = self::create_array_from_fields_name();

    foreach ($list_of_fields_name as $key) {
      if (!isset($_POST[$this->meta_key][$key])) {
        return false;
      }
    }

    return true;
  }


  private function create_array_from_fields_name()
  {
    $name_of_fields = [];
    for ($i = 0; $i < $this->number_of_fields; $i++) {
      array_push($name_of_fields, $this->fields_list[$i]['name']);
    }

    return $name_of_fields;
  }

  /**
   * write in a foreach loop save_post_ action for each needed post type
   *
   * @param array $post_types_list list of post type for which must be register new metha box
   */
  public function omb_save_posts_prepare(array $post_types_list)
  {
    foreach ($post_types_list as $item) {
      add_action('save_post_' . $item, [$this, 'save_metabox']);
    }
  }

  public function show_assets()
  {
    if (is_admin() && get_current_screen()->id == $this->post_type) {
      $this->show_styles();
    }
  }

  public function show_styles()
  {
    ?>
    <style>
      .add-new-<?php echo esc_attr($this->meta_key) ?> {
        color: #00a0d2;
        cursor: pointer;
      }

      .<?php echo esc_attr($this->meta_key) ?>-list .item-<?php echo esc_attr($this->meta_key) ?> {
        display: flex;
        align-items: center;
      }

      .remove-new-<?php echo esc_attr($this->meta_key) ?> {
        color: brown;
        cursor: pointer;
      }

      .item-<?php echo esc_attr($this->meta_key) ?> img {
        margin-top: 15px;
      }

    </style>
    <?php
  }

  private function prepare_page_id_list(array $pages): array
  {
    $result = [];
    foreach ($pages as $title) {
      $args = [
        'post_type'              => 'page',
        'title'                  => $title,
      ];

      $pages_wit_title = get_posts( $args ); //it is array
      foreach ($pages_wit_title as $page_wit_title){
        $result[] = $page_wit_title->ID;
      }
    }

    return $result;
  }
}
