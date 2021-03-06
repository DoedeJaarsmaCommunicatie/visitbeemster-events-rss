<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://doedejaarsma.nl/
 * @since             1.0.0
 * @package           Vb_E
 *
 * @wordpress-plugin
 * Plugin Name:       Visit Eveentser
 * Plugin URI:        https://visitbeemster.nl/
 * Description:       Deze plugin voegt een RSS feed toe met upcoming events
 * Version:           1.1.0
 * Author:            Doede Jaarsma communicatie
 * Author URI:        https://doedejaarsma.nl/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       vb-e
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
define( 'VB_E_VERSION', '1.1.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-vb-e-activator.php
 */
function activate_vb_e() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-vb-e-activator.php';
	Vb_E_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-vb-e-deactivator.php
 */
function deactivate_vb_e() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-vb-e-deactivator.php';
	Vb_E_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_vb_e' );
register_deactivation_hook( __FILE__, 'deactivate_vb_e' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-vb-e.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_vb_e() {
    require_once __DIR__ . '/vendor/autoload.php';

	$plugin = new Vb_E();
	$plugin->run();

}
run_vb_e();
