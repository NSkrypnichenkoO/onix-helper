<?php

use Onixhelper\Interfaces\OhelperFunctionsOverrider;
use Onixhelper\System\OhelperBaseController;

$base_controller = new OhelperBaseController();

require_once $base_controller->omb_path . 'templates/template-parts/sections/header.php';

$edit_one = isset($_POST['edit_tax'])
?>
<div class="add-new-entity-button">
  <a
    href="#tab-2"> <?php echo esc_html__('Add new', 'onix-helper') ?></a>
</div>

<ul class="nav nav-tabs">
  <li class="<?php echo esc_attr($edit_one) ? '' : esc_attr('active') ?>">
    <a href="#tab-1"> <?php esc_html_e('Taxonomies list', 'onix-helper') ?></a>
  </li>
<!--  <li class="--><?php //echo $edit_one ? esc_attr('active', ) : '' ?><!--">-->
<!--    <a-->
<!--      href="#tab-2"> --><?php //echo $edit_one ? esc_html(__('Edit ', 'onix-helper')) . esc_html(sanitize_title($_POST['edit_tax'])) : esc_html(__('Add new Taxonomy', 'onix-helper')) ?><!--</a>-->
<!--  </li>-->
  <!--  <li>-->
  <!--    <a href="#tab-3"><?php // echo esc_html_e('Export'. 'onix-helper') ?></a>-->
  <!--  </li>-->
</ul>

<div class="tab-content">
  <div id="tab-1" class="tab-pane <?php echo $edit_one ? '' : esc_attr('active') ?>">
    <h3> <?php esc_html_e('Created Taxonomies', 'onix-helper') ?> </h3>

    <table class="wp-list-table widefat fixed striped table-view-list">
      <thead>
      <tr>
        <th><?php esc_html_e('Id', 'onix-helper') ?></th>
        <th><?php esc_html_e('Singular Name', 'onix-helper') ?></th>
        <th><?php esc_html_e('Description', 'onix-helper') ?></th>
        <th><?php esc_html_e('Actions', 'onix-helper') ?></th>
      </tr>
      </thead>

      <?php
      $tax_list = get_option('onix_meta_box_tax');

      if ($tax_list) {
      ?>

      <tbody>
        <?php
        foreach ($tax_list as $tax) {
          $taxonomy = $tax['taxonomy'];
          ?>
          <tr>
            <td><?php esc_html_e($taxonomy, 'onix-helper') ?></td>
            <td><?php esc_html_e($tax['singular_name']) ?></td>
            <td><?php echo isset($tax["description"]) ? esc_html($tax["description"]) : '' ?></td>
            <td class="button-wrapper">
              <form method="post" action="">
                <input type="hidden" name="edit_tax" value="<?php echo esc_html($taxonomy) ?>">
                <?php submit_button(__('Edit', 'onix-helper'), 'primary small', 'submit', false); ?>
              </form>

              <form method="post" action="options.php">
                <?php settings_fields('omb_tax_settings'); ?>
                <input type="hidden" name="remove" value="<?php esc_html_e($taxonomy, 'onix-helper') ?>">
                <?php submit_button(__('Remove', 'onix-helper'), 'delete small', 'submit', false,
                  ['onclick' => 'return confirm("Are you sure you want to delete this taxonomy? The data associated with it will be deleted")']); ?>
              </form>
            </td>
          </tr>
        <?php }
        }
        else {
          ?>
          <tr> <?php esc_html_e('nothing found', 'onix-helper') ?></tr>
        <?php } ?>
      </tbody>
    </table>
  </div>

  <div id="tab-2" class="tab-pane <?php echo $edit_one ? esc_attr('active') : '' ?>">
    <form method="post" action="options.php">
    <?php wp_nonce_field('form-tax-manager_action', 'form-tax-manager_nonce'); ?>

      <?php
      settings_fields('omb_tax_settings');
      //need pass the slug of the page where the setting section is apply to
      OhelperFunctionsOverrider:: omb_do_settings_sections('onix_meta_box_tax');
      submit_button();
      ?>
    </form>
  </div>
  <div id="tab-3" class="tab-pane"><h3> <?php esc_html_e('Export', 'onix-helper') ?></h3></div>

</div>
<?php require_once $base_controller->omb_path . 'templates/template-parts/sections/footer-main.php'; ?>
