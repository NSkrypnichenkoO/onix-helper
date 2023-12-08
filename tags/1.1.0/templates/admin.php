<?php

use Onixhelper\System\OhelperBaseController;

$base_controller = new OhelperBaseController();

require_once $base_controller->omb_path . 'templates/template-parts/sections/header-main.php';
?>

<ul class="nav nav-tabs">
  <li class="active">
    <a href="#tab-1"> <?php esc_html_e('Manage stings', 'onix-helper') ?></a>
  </li>
  <li>
    <a href="#tab-2"> <?php esc_html_e('Plugin settings', 'onix-helper') ?></a>
  </li>
</ul>

<div class="tab-content">

  <div id="tab-1" class="tab-pane active">

    <form method="post" action="options.php">
      <?php
      settings_fields('omb_settings');

      //need pass the slug of the page where the setting section is apply to
      do_settings_sections('onix_meta_box');

      submit_button();
      ?>
    </form>

  </div>

  <div id="tab-2" class="tab-pane">
    <form method="post" action="options.php">
    <?php wp_nonce_field('form-admin_action', 'form-admin_nonce'); ?>
      <?php
      settings_fields('omb_plugin_work_settings');

      //need pass the slug of the page where the setting section is apply to
      do_settings_sections('onix_meta_box_work_settings');

      submit_button();
      ?>
    </form>
  </div>

</div>


<?php require_once $base_controller->omb_path . 'templates/template-parts/sections/footer-main.php'; ?>
