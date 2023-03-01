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
    wp_enqueue_style('omb-min-style', $this->omb_url . '/assets/css/style.min.css');
    wp_enqueue_script('omb-main', $this->omb_url . '/assets/js/main.js');
    wp_enqueue_script('omb-callbacks-js', $this->omb_url . '/assets/js/callbacks-js.js');


    wp_register_script( 'omb-meta-fields',$this->omb_url .'/assets/js/meta-fields.js', array('jquery'));
    wp_enqueue_script( 'omb-meta-fields' );
  }

}
