<?php
if (isset($args)){
?>
<div class="oh-navigation-bar">
  <h1><?php esc_html( $args['title'])?></h1>
  <div class="oh-navigation-row">
    <label id="oh-description-switcher">
      <input type="checkbox" class="oh-simple-checkbox" checked>
      <?php esc_html_e('Show description', 'onix-helper') ?>
    </label>
  </div>
</div>
<?php }
