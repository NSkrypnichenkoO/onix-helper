<?php
if (isset($args)){
?>
<div class="oh-navigation-bar">
  <h1><?php esc_html_e( $args['title'], 'onix-helper')?></h1>
  <div class="oh-navigation-row">
<!--    <label>-->
<!--      <input type="checkbox" class="oh-simple-checkbox">-->
<!--      --><?php //echo esc_html_e('Show description', 'onix-helper') ?>
<!--    </label>-->
  </div>
</div>
<?php }
