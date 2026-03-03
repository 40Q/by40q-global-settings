<?php
/**
 * Plugin Name:       40Q Global Settings
 * Plugin URI:        https://40q.agency
 * Description:       Global Settings panel for 40Q sites. Replaces ACF options pages — devs register tabs and fields in PHP, editors fill values in a React admin UI. Requires 40Q Core Plugin.
 * Version:           1.0.0
 * Requires at least: 6.4
 * Requires PHP:      8.2
 * Author:            40Q Agency
 * Author URI:        https://40q.agency
 * License:           GPL-2.0-or-later
 * Text Domain:       by40q
 * Domain Path:       /languages
 *
 * @package By40Q\GlobalSettings
 */

declare( strict_types=1 );

namespace By40Q\GlobalSettings;

defined( 'ABSPATH' ) || exit;

define( 'BY40Q_GLOBAL_SETTINGS_VERSION', '1.0.0' );
define( 'BY40Q_GLOBAL_SETTINGS_FILE', __FILE__ );
define( 'BY40Q_GLOBAL_SETTINGS_PATH', plugin_dir_path( __FILE__ ) );
define( 'BY40Q_GLOBAL_SETTINGS_URL', plugin_dir_url( __FILE__ ) );

// Require 40Q Core Plugin.
if ( ! defined( 'BY40Q_CORE_VERSION' ) ) {
	add_action(
		'admin_notices',
		function () {
			echo '<div class="notice notice-error"><p>';
			echo '<strong>40Q Global Settings</strong> requires the <strong>40Q Core Plugin</strong> to be active.';
			echo '</p></div>';
		}
	);
	return;
}

// Composer autoloader.
if ( file_exists( BY40Q_GLOBAL_SETTINGS_PATH . 'vendor/autoload.php' ) ) {
	require_once BY40Q_GLOBAL_SETTINGS_PATH . 'vendor/autoload.php';
}

require_once BY40Q_GLOBAL_SETTINGS_PATH . 'includes/class-global-settings-activator.php';
require_once BY40Q_GLOBAL_SETTINGS_PATH . 'includes/class-global-settings-deactivator.php';

register_activation_hook( __FILE__, array( 'By40Q\GlobalSettings\Global_Settings_Activator', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'By40Q\GlobalSettings\Global_Settings_Deactivator', 'deactivate' ) );

require_once BY40Q_GLOBAL_SETTINGS_PATH . 'includes/functions.php';
require_once BY40Q_GLOBAL_SETTINGS_PATH . 'includes/class-field-registry.php';
require_once BY40Q_GLOBAL_SETTINGS_PATH . 'includes/class-rest-controller.php';
require_once BY40Q_GLOBAL_SETTINGS_PATH . 'includes/class-admin-page.php';
require_once BY40Q_GLOBAL_SETTINGS_PATH . 'includes/class-global-settings.php';
require_once BY40Q_GLOBAL_SETTINGS_PATH . 'includes/settings.php';

Global_Settings::instance();
