<?php
/**
 * @link              http://lucianotonet.com
 * @since             1.0.0
 * @package           Rdplugin
 *
 * @wordpress-plugin
 * Plugin Name:       RD Plugin
 * Plugin URI:        https://github.com/tonetlds/rdplugin
 * Description:       Plugin exclusivo para o desafio RD.
 * Version:           1.0.0
 * Author:            Luciano
 * Author URI:        http://lucianotonet.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       rdplugin
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-rdplugin-activator.php
 */
function activate_rdplugin() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-rdplugin-activator.php';
	Rdplugin_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-rdplugin-deactivator.php
 */
function deactivate_rdplugin() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-rdplugin-deactivator.php';
	Rdplugin_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_rdplugin' );
register_deactivation_hook( __FILE__, 'deactivate_rdplugin' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-rdplugin.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_rdplugin() {

	$plugin = new Rdplugin();
	$plugin->run();

}
run_rdplugin();
