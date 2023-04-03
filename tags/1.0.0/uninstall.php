<?php


/**
 * Trigger this file on Plugin uninstall
 *
 * @package onix-meta-box
 */

if (!defined('WP_UNINSTALL_PLUGIN')) {
  die;
}

$plugin_settings = get_option('onix_meta_box_work_settings');
$need_delete_all = false;

if ($plugin_settings) {
  if (in_array('delete_plugin_inform', $plugin_settings)) {
    $need_delete_all = true;
  }
}

if ($need_delete_all) {

  $cpt_list = get_option('onix_meta_box_cpt') ? array_keys(get_option('onix_meta_box_cpt')) : [];

  foreach ($cpt_list as $cpt) {
    $posts = get_posts([
      'post_type' => $cpt,
      'post_status' => ['publish', 'draft', 'trash', 'future', 'pending', 'private', 'auto-draft', 'inherit']
    ]);

    foreach ($posts as $post) {
      wp_delete_post($post->ID, true);
    }
  }

  $tax_list = get_option('onix_meta_box_tax') ? array_keys(get_option('onix_meta_box_tax')) : [];

  foreach ($tax_list as $tax) {
    $terms = get_terms([
      'taxonomy' => $tax,
      'hide_empty' => false,
    ]);
    foreach ($terms as $term) {
      wp_delete_term($term->term_id, $term);
    }
  }

  $fields_list = get_option('onix_meta_box_fields') ? array_keys(get_option('onix_meta_box_fields')) : [];

  foreach ($fields_list as $section) {
    delete_metadata('post', 0, $section, false, true);
  }

  delete_option('onix_meta_box_tax');
  delete_option('onix_meta_box_cpt');
  delete_option('onix_meta_box_fields');
  delete_option('onix_meta_box_work_settings');
}
