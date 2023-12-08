<?php
/**
 * @package onix-meta-box
 */

namespace Onixhelper\Interfaces\Callbacks;

use Onixhelper\System\OhelperBaseController;

/**
 * Class OhelperAdminCallbacks here we will declare all callbacks. for more cleaner code
 * @package Onixhelper\Interfaces\Callbacks
 */
class OhelperAdminCallbacks extends OhelperBaseController
{

  public function admin_dashboard()
  {
    return require_once("$this->omb_path/templates/admin.php");
  }

  public function admin_cpt_manager()
  {
    return require_once("$this->omb_path/templates/cpt_manager.php");
  }

  public function admin_tax_manager()
  {
    return require_once("$this->omb_path/templates/tax_manager.php");
  }

  public function admin_fields_manager()
  {
    return require_once("$this->omb_path/templates/fields_manager.php");
  }


  /**
   * input care about just checked our checkbox ore not
   * is it in the form request or not
   *
   * @param $input array or null from checkbox on admin settings code
   *
   * @return array  passed input after validation
   */
  public function checkbox_sanitise($input): array
  {
    $result = [];

    foreach ($this->option_groups as $key => $value) {
      if (isset($input[$key]) && (int)$input[$key] === 1) {
        array_push($result, $key);
      }
    }

    return $result;
  }

  public function settings_sanitise($input)
  {
    // will work just for true\false values for now and should be changed in the feature
    $result = [];
    foreach ($this->settings_fields as $key => $value) {
      if (isset($input[$key]) && (int)$input[$key] === 1) {
        array_push($result, $key);
      }
    }
    return $result;
  }

  public function admin_pages_section_manager()
  {
      esc_html_e('Manage the features of this plugin by activating the checkboxes', 'onix-helper');
  }

  public function admin_pages_settings_section_manager()
  {
//    echo '';
  }

  /**
   * @param $args array of arguments that we pass when create the field
   *
   * echo html of checkbox or empty string if error
   */
  public function checkbox_field(array $args)
  {

    $name = isset($args['label_for']) ? $args['label_for'] : '';

    if (!$name) {
      error_log('field in Admin.php set_fields() was create with error in code, please check it ', );
    }

    $option_name = $args['option_name'];
    $checkbox = get_option($option_name);

    $checked = $checkbox ? in_array($name, $checkbox) : false;

    $a = new OhelperBaseCallbacks;
    $a->create_switcher_checkbox_html($option_name, $name, $checked);
  }
}
