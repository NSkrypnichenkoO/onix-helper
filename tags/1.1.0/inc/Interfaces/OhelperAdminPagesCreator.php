<?php
/**
 * @package onix-meta-box
 */

namespace Onixhelper\Interfaces;

use Onixhelper\System\OhelperBaseController;

/**
 * Class OhelperAdminPagesCreator just interface to dinamikly create admin pages
 * @package Onixhelper\Interfaces
 */
class OhelperAdminPagesCreator extends OhelperBaseController
{
  public array $admin_pages = [];

  public array $admin_subpages = [];

  public array $settings = [];

  public array $sections = [];

  public array $fields = [];


  public function register()
  {
    if (!empty($this->admin_pages) || !empty($this->admin_subpages)) {
      add_action('admin_menu', [$this, 'add_admin_menu']);
    }

    if (!empty($this->settings)) {
      add_action('admin_init', [$this, 'register_custom_fields']);
    }
  }

  public function add_pages(array $pages): OhelperAdminPagesCreator
  {

    $this->admin_pages = $pages;

    return $this;

  }

  /**
   *
   * @param string|null $title
   * @return $this
   */
  public function with_subpage(string $title = null)
  {
    // we dont need to create subpages if no one page was declare
    if (empty($this->admin_pages)) {
      return $this;
    }

    //extract just first page. if will be more pages - should check the correct number
    $admin_page = $this->admin_pages[0];

    $subpages = [
      [
        'parent_slug' => $admin_page['menu_slug'],
        'page_title' => $admin_page['page_title'],
        'menu_title' => $title ?: $admin_page['menu_title'],
        'capability' => $admin_page['capability'],
        'menu_slug' => $admin_page['menu_slug'],
        'callback' => function () {
        },

      ]
    ];
    $this->admin_subpages = $subpages;

    return $this;

  }

  public function add_subpages($pages): OhelperAdminPagesCreator
  {
    //initialisation empty array of class with passed subpages
    $this->admin_subpages = array_merge($this->admin_subpages, $pages);

    return $this;
  }

  /**
   * go for each pages in the array and initial each of them as one
   */
  public function add_admin_menu()
  {
    foreach ($this->admin_pages as $page) {
      add_menu_page($page['page_title'], $page['menu_title'], $page['capability'], $page ['menu_slug'], $page ['callback'], $page ['icon_url'], $page['position']);
    }
    foreach ($this->admin_subpages as $subpage) {
      add_submenu_page($subpage['parent_slug'], $subpage['page_title'], $subpage['menu_title'], $subpage['capability'], $subpage ['menu_slug'], $subpage ['callback']);
    }
  }

  public function set_settings($settings): OhelperAdminPagesCreator
  {
    $this->settings = $settings;

    return $this;
  }

  public function set_sections($sections): OhelperAdminPagesCreator
  {
    $this->sections = $sections;

    return $this;
  }

  public function set_fields($fields): OhelperAdminPagesCreator
  {
    $this->fields = $fields;

    return $this;
  }

  public function register_custom_fields()
  {
    foreach ($this->settings as $setting) {
      register_setting($setting['option_group'], $setting['option_name'], isset($setting['callback']) ? $setting['callback'] : '');
    }

    foreach ($this->sections as $section) {
      add_settings_section($section ['id'], $section['title'], isset($section['callback']) ? $section['callback'] : '', $section['page']);
    }

    foreach ($this->fields as $field) {
      add_settings_field($field['id'], $field['title'], isset($field['callback']) ? $field['callback'] : '', $field['page'], $field['section'], isset($field['args']) ? $field['args'] : '');
    }
  }
}
