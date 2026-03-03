<?php
/**
 * Fired during plugin deactivation.
 *
 * @package By40Q\GlobalSettings
 */

declare( strict_types=1 );

namespace By40Q\GlobalSettings;

defined( 'ABSPATH' ) || exit;

/**
 * Plugin deactivator.
 */
class Global_Settings_Deactivator {

	/**
	 * Run on deactivation.
	 */
	public static function deactivate(): void {
		flush_rewrite_rules();
	}
}
