<?php
// when we create page to create new section? we use code in js and in php
// if we want co have all needed templates and data in the one place - we should have this class
/**
 * @package onix-meta-box
 */

namespace Onixhelper\System\FieldsClasses;


class OhelperFieldsValidation
{

  public function __construct()
  {
    $this->validate_url_fields();
  }

  private function validate_url_fields()
  {
    global $errors;

    // Do some checking...
    if ($_POST['subhead'] != 'value i expect') {
      // Add an error here
      $errors->add('oops', 'There was an error.');
    }
    return $errors;
  }

}

add_action('save_post', new OhelperFieldsValidation(), 1, 2);
