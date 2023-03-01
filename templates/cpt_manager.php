<?php

use Onixhelper\Interfaces\OhelperFunctionsOverrider;
use Onixhelper\System\OhelperBaseController;

$base_controller = new OhelperBaseController();

require_once $base_controller->omb_path . 'templates/template-parts/sections/header.php';

$edit_one = isset($_POST['edit_cpt'])
?>


<ul class="nav nav-tabs">
  <li class="<?php echo $edit_one ? '' : 'active' ?>">
    <a href="#tab-1"><?php echo __('Created Custom Post Types', 'onix-helper') ?></a>
  </li>
  <li class="<?php echo $edit_one ? 'active' : '' ?>">
    <a
      href="#tab-2"> <?php echo $edit_one ? 'Edit ' . sanitize_title($_POST['edit_cpt']) : __('Add new Custom Post Type') ?></a>
  </li>
  <!--  <li>-->
  <!--    <a href="#tab-3"> <?php // echo __('Export'. 'onix-helper') ?></a>-->
  <!--  </li>-->
</ul>


<div class="tab-content">
  <div id="tab-1" class="tab-pane <?php echo $edit_one ? '' : 'active' ?>">
    <h3><?php echo __('Created Custom Post Types', 'onix-helper') ?></h3>

    <table class="wp-list-table widefat fixed striped table-view-list">
      <thead>
      <tr>
        <th><?php echo __('Id', 'onix-helper') ?></th>
        <th><?php echo __('Name', 'onix-helper') ?></th>
        <th><?php echo __('Singular Name', 'onix-helper') ?></th>
        <th><?php echo __('Public', 'onix-helper') ?></th>
        <th><?php echo __('Description', 'onix-helper') ?></th>
        <th><?php echo __('Actions', 'onix-helper') ?></th>
      </tr>
      </thead>

      <?php
      $cpt_list = get_option('onix_meta_box_cpt');

      if ($cpt_list) {
      ?>

      <thebody>
        <?php
        foreach ($cpt_list as $cpt) { ?>
          <tr>
            <td><?php esc_html_e($cpt['post_type'], 'onix-helper') ?></td>
            <td><?php esc_html_e($cpt['plural_name'], 'onix-helper') ?></td>
            <td><?php esc_html_e($cpt['singular_name'], 'onix-helper') ?></td>
            <td><?php echo isset($cpt['public']) ? ($cpt['public'] ? 'true' : 'false') : 'false' ?></td>
            <td><?php echo isset($cpt["description"]) ? esc_html(__($cpt["description"], 'onix-helper')) : '' ?></td>
            <td class="button-wrapper">

              <form method="post" action="">
                <input type="hidden" name="edit_cpt" value="<?php echo esc_html($cpt['post_type']) ?>">
                <?php submit_button(__('Edit', 'onix-helper'), 'primary small', 'submit', false); ?>
              </form>

              <form method="post" action="options.php">
                <?php settings_fields('omb_cpt_settings'); ?>
                <input type="hidden" name="remove" value="<?php echo $cpt['post_type'] ?>">
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
          <tr><?php echo __('nothing found', 'onix-helper') ?></tr>
          <?php
        }
        ?>
      </thebody>
    </table>
  </div>

  <div id="tab-2" class="tab-pane <?php echo $edit_one ? 'active' : '' ?>">
    <form method="post" action="options.php">
      <?php
      settings_fields('omb_cpt_settings');
      //need pass the slug of the page where the setting section is apply to
      OhelperFunctionsOverrider:: omb_do_settings_sections('onix_meta_box_cpt');
      submit_button();
      ?>
    </form>
  </div>
  <div id="tab-3" class="tab-pane"><h3> <?php echo __('Export', 'onix-helper') ?></h3></div>


</div>


<?php require_once $base_controller->omb_path . 'templates/template-parts/sections/footer-main.php'; ?>

