<?php

use Onixhelper\Interfaces\OhelperFunctionsOverrider;
use Onixhelper\System\OhelperBaseController;

$base_controller = new OhelperBaseController();

require_once $base_controller->omb_path . 'templates/template-parts/sections/header.php';

$edit_one = isset($_POST['edit_fields_section']);
?>
<ul class="nav nav-tabs">
  <li class="<?php echo $edit_one ? '' : 'active' ?>">
    <a href="#tab-1"><?php echo esc_html_e('Created Fields Sections', 'onix-helper') ?></a>
  </li>
  <li class="<?php echo $edit_one ? 'active' : '' ?>">
    <a
      href="#tab-2"> <?php echo $edit_one ? esc_html_e('Edit ', 'onix-helper') . esc_html(sanitize_title($_POST['edit_fields_section'])) : esc_html_e('Add new Fields Section', 'onix-helper') ?></a>
  </li>
</ul>

<div class="tab-content">
  <div id="tab-1" class="tab-pane <?php echo $edit_one ? '' : 'active' ?>">
    <h3> Created Taxonomies </h3>
    <table class="wp-list-table widefat fixed striped table-view-list">
      <thead>
      <tr>
        <th><?php echo esc_html_e('Slug', 'onix-helper') ?></th>
        <th><?php echo esc_html_e('Singular Name', 'onix-helper') ?></th>
        <th><?php echo esc_html_e('Description', 'onix-helper') ?></th>
        <th><?php echo esc_html_e('Actions', 'onix-helper') ?></th>
      </tr>
      </thead>

      <?php
      $omb_list = get_option('onix_meta_box_fields');

      if ($omb_list) {
      ?>

      <tbody>
        <?php
        foreach ($omb_list as $section) {
          $section_slug = $section['fields_section_slug'];
          ?>
          <tr>
            <td><?php esc_html_e($section_slug, 'onix-helper') ?></td>
            <td><?php esc_html_e($section['fields_section_title'], 'onix-helper') ?></td>
            <td><?php echo isset($section["description"]) ? esc_html(__($section["description"], 'onix-helper')) : '' ?></td>
            <td class="button-wrapper">
              <form method="post" action="">
                <input type="hidden" name="edit_fields_section" value="<?php echo esc_attr($section_slug) ?>">
                <?php submit_button(__('Edit', 'onix-helper'), 'primary small', 'submit', false); ?>
              </form>
              <!-- this form will submit data to the cpt_sanitise() method, as $_POST array-->
              <form method="post" action="options.php">
                <?php settings_fields('omb_fields_settings'); ?>
                <!-- We need this input to pass to the callback function post_type slug, which we want do delete-->
                <input type="hidden" name="remove" value="<?php echo esc_attr($section_slug) ?>">
                <?php submit_button(__('Remove', 'onix-helper'), 'delete small', 'submit', false,
                  ['onclick' => 'return confirm("Are you sure you want to delete this taxonomy? The data associated with it will be deleted")']); ?>
              </form>
            </td>
          </tr>
        <?php }
        }
        else {
          ?>
          <tr><?php echo esc_html_e('nothing found', 'onix-helper') ?></tr>
        <?php } ?>
      </tbody>
    </table>
  </div>

  <div id="tab-2" class="tab-pane <?php echo $edit_one ? 'active' : '' ?>">

    <form method="post" action="options.php">
      <?php
      settings_fields('omb_fields_settings');
      //need pass the slug of the page where the setting section is apply to
      OhelperFunctionsOverrider:: omb_do_settings_sections('onix_meta_box_fields');
      submit_button();
      ?>
    </form>
  </div>


</div>

<?php require_once $base_controller->omb_path . 'templates/template-parts/sections/footer-main.php';
?>
