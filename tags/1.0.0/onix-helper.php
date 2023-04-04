<?php
/**
 * Plugin name: Onix Helper | CPT, CMB, Taxonomies
 *
 * Plugin URI:        https://onix-systems-onix-helper.staging.onix.ua/
 * Description:       Onix Helper is intended to create Custom Post Types and Custom Taxonomies in a way convenient to you. This plugin suits for developers, agencies and private users. It just works!
 * Version:           1.0.0
 * Requires at least: 6.1
 * Requires PHP:      8.0
 * Author:            Onix
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       onix-helper
 * Domain Path:       /
 */

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/


// If this file is called firectly, abort!!!
defined('ABSPATH') or die('Hey, what are you doing here? You silly human!');

//require composer
if (file_exists(plugin_dir_path(__FILE__) . 'vendor/autoload.php')) {
  require_once(plugin_dir_path(__FILE__) . 'vendor/autoload.php');
}

function ohelper_activate()
{
  if (class_exists('Onixhelper\System\OhelperActivate')) {
    Onixhelper\System\OhelperActivate::activate();
  }

}

register_activation_hook(__FILE__, 'ohelper_activate');

function ohelper_deactivate()
{
  Onixhelper\System\OhelperDeactivate::deactivate();
}

register_deactivation_hook(__FILE__, 'ohelper_deactivate');

/**
 * register all classes, that we use
 */
if (class_exists('Onixhelper\\OhelperInit')) {
  Onixhelper\OhelperInit::register_services();
} else {
  die('cant find Onixhelper\\OhelperInit');
}

