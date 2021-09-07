<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/dijitaq/
 * @since             1.0.0
 * @package           Payfazz
 *
 * @wordpress-plugin
 * Plugin Name:       Payfazz plugin
 * Plugin URI:        https://github.com/dijitaq/payfazz-plugin
 * Description:       Wordpress plugin for Payfazz recruitment.
 * Version:           1.0.0
 * Author:            Firdaus Riyanto
 * Author URI:        https://github.com/dijitaq/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       payfazz
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PAYFAZZ_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-payfazz-activator.php
 */
function activate_payfazz() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-payfazz-activator.php';
	Payfazz_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-payfazz-deactivator.php
 */
function deactivate_payfazz() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-payfazz-deactivator.php';
	Payfazz_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_payfazz' );
register_deactivation_hook( __FILE__, 'deactivate_payfazz' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-payfazz.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_payfazz() {

	$plugin = new Payfazz();
	$plugin->run();

}
run_payfazz();
