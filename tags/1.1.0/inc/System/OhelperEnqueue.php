<?php

/**
 * @package onix-meta-box
 */

namespace Onixhelper\System;

class OhelperEnqueue extends OhelperBaseController
{
  public function register()
  {
    add_action('admin_enqueue_scripts', [$this, 'enqueue']);
  }

  function enqueue()
  {

    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/css/all.min.css');

    wp_enqueue_style('omb-min-style', $this->omb_url . '/assets/css/style.min.css');
    wp_enqueue_script('omb-main', $this->omb_url . '/assets/js/main.js');
    wp_enqueue_script('omb-callbacks-js', $this->omb_url . '/assets/js/callbacks-js.js');

    wp_enqueue_script('omb-select-library-js', $this->omb_url . '/assets/js/selects-library.js');
    wp_enqueue_script('omb-admin-top-navigation-panel-js', $this->omb_url . '/assets/js/admin-top-navigation-panel.js');


    // back of meta boxes
    wp_register_script('omb-meta-fields', $this->omb_url . '/assets/js/meta-fields.js', array('jquery'));
    wp_enqueue_script('omb-meta-fields');

    // just for admin page of fields manager
    wp_enqueue_script('omb-admin-fields-manager-front-screen-options', $this->omb_url . '/assets/js/fields-manager/front/screen-options.js');
    wp_enqueue_script('omb-admin-fields-manager-box-fields-validation', $this->omb_url . '/assets/js/fields-manager/front/box-fields-validation.js');

    //fields manager page
    wp_enqueue_script('omb-admin-fields-manager-back-add-new-field-to-section', $this->omb_url . '/assets/js/fields-manager/back/add-new-field-to-section.js', array('jquery'));
    wp_localize_script('omb-admin-fields-manager-back-add-new-field-to-section', 'omb_ajax_object', array('ajax_url' => admin_url( 'admin-ajax.php' )));
  }

}
