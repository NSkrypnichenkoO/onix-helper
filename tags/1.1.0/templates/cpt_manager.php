<?php

use Onixhelper\Interfaces\OhelperFunctionsOverrider;
use Onixhelper\System\OhelperBaseController;

$base_controller = new OhelperBaseController();

require_once $base_controller->omb_path . 'templates/template-parts/sections/header.php';

$edit_one = isset($_POST['edit_cpt']);
?>

<div class="add-new-entity-button">
  <a
    href="#tab-2"> <?php echo esc_html__('Add new', 'onix-helper') ?></a>
</div>

<ul class="nav nav-tabs">
  <li class="<?php echo $edit_one ? '' : esc_html(__('active', 'onix-helper')) ?>">
    <a href="#tab-1"><?php esc_html_e('Custom Post Types list', 'onix-helper') ?></a>
  </li>
<!--  <li class="--><?php //echo $edit_one ? esc_attr('active') : '' ?><!--">-->
<!--    <a href="#tab-2">-->
<!--      --><?php //echo $edit_one ? sprintf(__('Edit %s', 'onix-helper'), esc_html(sanitize_title($_POST['edit_cpt']))) : esc_html(__('Add new Custom Post Type', 'onix-helper')) ?><!--</a>-->
<!--  </li>-->
      <!--  <li>-->
      <!--    <a href="#tab-3"> <?php // echo esc_html_e('Export'. 'onix-helper') ?></a>-->
      <!--  </li>-->
</ul>


<div class="tab-content">
  <div id="tab-1" class="tab-pane <?php echo $edit_one ? '' : esc_attr('active') ?>">
    <h3><?php esc_html_e('Created Custom Post Types', 'onix-helper') ?></h3>

    <table class="wp-list-table widefat fixed striped table-view-list">
      <thead>
      <tr>
        <th><?php esc_html_e('Id', 'onix-helper') ?></th>
        <th><?php esc_html_e('Name', 'onix-helper') ?></th>
        <th><?php esc_html_e('Singular Name', 'onix-helper') ?></th>
        <th><?php esc_html_e('Public', 'onix-helper') ?></th>
        <th><?php esc_html_e('Description', 'onix-helper') ?></th>
        <th><?php esc_html_e('Actions', 'onix-helper') ?></th>
      </tr>
      </thead>

      <?php
      $cpt_list = get_option('onix_meta_box_cpt');

      if ($cpt_list) {
      ?>

      <tbody>
      <?php
      foreach ($cpt_list as $cpt) { ?>
        <tr>
          <td><?php esc_html_e($cpt['post_type'], 'onix-helper') ?></td>
          <td><?php esc_html_e($cpt['plural_name'], 'onix-helper') ?></td>
          <td><?php esc_html_e($cpt['singular_name'], 'onix-helper') ?></td>
          <td><?php echo isset($cpt['public']) ? $cpt['public'] ? esc_html(__('true', 'onix-helper')) : esc_html(__('false', 'onix-helper')) : esc_html(__('false', 'onix-helper')) ?></td>
          <td><?php echo isset($cpt["description"]) ? esc_html($cpt["description"]) : '' ?></td>
          <td class="button-wrapper">

            <form method="post" action="">
              <input type="hidden" name="edit_cpt" value="<?php echo esc_html($cpt['post_type']) ?>">
              <?php submit_button(__('Edit', 'onix-helper'), 'primary small', 'submit', false); ?>
            </form>

            <form method="post" action="options.php">
              <?php settings_fields('omb_cpt_settings'); ?>
              <input type="hidden" name="remove" value="<?php echo esc_html($cpt['post_type']) ?>">
              <?php submit_button(__('Remove', 'onix-helper'), 'delete small', 'submit', false,
                ['onclick' => 'return confirm("Are you sure you want to delete this post type? The data associated with it will be deleted")']); ?>
            </form>

          </td>
        </tr>
      <?php }
      ?>

      <?php }
      else {
        ?>
        <tr><?php esc_html_e('nothing found', 'onix-helper') ?></tr>
        <?php
      }
      ?>
      </tbody>
    </table>
  </div>

  <div id="tab-2" class="tab-pane <?php echo $edit_one ? esc_html('active') : '' ?>">
    <form method="post" action="options.php">
    <?php wp_nonce_field('form-cpt-manager_action', 'form-cpt-manager_nonce'); ?>

      <?php
      settings_fields('omb_cpt_settings');
      //need pass the slug of the page where the setting section is apply to
      OhelperFunctionsOverrider:: omb_do_settings_sections('onix_meta_box_cpt');
      submit_button();
      ?>
    </form>
  </div>
  <div id="tab-3" class="tab-pane"><h3> <?php esc_html_e('Export', 'onix-helper') ?></h3></div>


</div>


<?php require_once $base_controller->omb_path . 'templates/template-parts/sections/footer-main.php'; ?>

