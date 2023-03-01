<div class="omb-wrap">
  <?php

  use Onixhelper\System\OhelperBaseController;

  $base_controller = new OhelperBaseController();

  settings_errors();

  //require_once $base_controller->omb_path . 'templates/template-parts/holidays-decor/cat.php';
  ?>
  <div class="first-screen">
    <?php require_once $base_controller->omb_path . 'templates/template-parts/functional-parts/plugin-title.php'; ?>
    <div class="header-image">
      <img src="<?php echo $base_controller->omb_url . 'assets/img/mascot.png' ?>" alt="Italian Trulli">
    </div>
  </div>
