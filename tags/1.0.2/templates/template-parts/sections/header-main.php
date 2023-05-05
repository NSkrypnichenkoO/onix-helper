<?php

use Onixhelper\System\OhelperBaseController;

$base_controller = new OhelperBaseController();
?>
<div class="first-screen">
  <?php require_once $base_controller->omb_path . 'templates/template-parts/functional-parts/plugin-title.php'; ?>
  <div class="header-image">
    <div class="main-boot">
      <img src="<?php echo esc_url($base_controller->omb_url) . 'assets/img/onix-bot.png' ?>" alt="Onix Helper">
    </div>
    <span class="boot-eyes"></span>
  </div>
</div>
<?php settings_errors(); ?>
<div class="omb-wrap">
