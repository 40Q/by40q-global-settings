<?php
/**
 * Registers the Global Settings admin menu page and enqueues assets.
 *
 * @package By40Q\GlobalSettings
 */

declare( strict_types=1 );

namespace By40Q\GlobalSettings;

defined( 'ABSPATH' ) || exit;

/**
 * Admin Page.
 */
class Admin_Page {

	/**
	 * Register the top-level admin menu item and all submenu pages.
	 */
	public function register_menu(): void {
		add_menu_page(
			__( 'Global Settings', 'by40q' ),
			__( 'Global Settings', 'by40q' ),
			'manage_options',
			'by40q-global-settings',
			array( $this, 'render_page' ),
			'dashicons-admin-site-alt3',
			80
		);

		// Re-register the main page as the first submenu item so it gets a distinct label.
		add_submenu_page(
			'by40q-global-settings',
			__( 'Global Settings', 'by40q' ),
			__( 'Settings', 'by40q' ),
			'manage_options',
			'by40q-global-settings',
			array( $this, 'render_page' )
		);

		// Register each developer-defined submenu page.
		foreach ( Field_Registry::get_submenus() as $submenu ) {
			$slug = 'by40q-' . $submenu['key'];
			add_submenu_page(
				'by40q-global-settings',
				$submenu['label'],
				$submenu['label'],
				'manage_options',
				$slug,
				array( $this, 'render_page' )
			);
		}
	}

	/**
	 * Render the admin page — just the React mount point.
	 */
	public function render_page(): void {
		?>
		<div class="wrap">
			<div id="by40q-global-settings-root"></div>
		</div>
		<?php
	}

	/**
	 * Enqueue the React settings bundle on any Global Settings admin page.
	 *
	 * @param string $hook_suffix Current admin page hook suffix.
	 */
	public function enqueue_assets( string $hook_suffix ): void {
		// Build the full list of allowed page slugs from the registry.
		$allowed_slugs = array( 'by40q-global-settings' );
		foreach ( Field_Registry::get_submenus() as $submenu ) {
			$allowed_slugs[] = 'by40q-' . $submenu['key'];
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$current_slug = sanitize_key( wp_unslash( $_GET['page'] ?? '' ) );
		if ( ! in_array( $current_slug, $allowed_slugs, true ) ) {
			return;
		}

		// Derive the page context passed to the React app and REST API.
		$page_context = ( 'by40q-global-settings' === $current_slug )
			? 'main'
			: substr( $current_slug, strlen( 'by40q-' ) );

		$asset_file = BY40Q_GLOBAL_SETTINGS_PATH . 'build/scripts/settings.asset.php';
		if ( ! file_exists( $asset_file ) ) {
			return;
		}

		$asset = require $asset_file;

		wp_enqueue_script(
			'by40q-global-settings',
			BY40Q_GLOBAL_SETTINGS_URL . 'build/scripts/settings.js',
			$asset['dependencies'],
			$asset['version'],
			true
		);

		// Pass REST URL, nonce, and current page context to the React app.
		wp_localize_script(
			'by40q-global-settings',
			'by40qGlobalSettings',
			array(
				'restUrl' => rest_url( 'by40q/v1/global-settings' ),
				'nonce'   => wp_create_nonce( 'wp_rest' ),
				'page'    => $page_context,
			)
		);

		// Load the WP media library so ImageField can open the media modal.
		wp_enqueue_media();

		wp_enqueue_style( 'wp-components' );

		// Custom page styles — built from src/js/settings/settings.scss.
		$style_path = BY40Q_GLOBAL_SETTINGS_PATH . 'build/scripts/settings.css';
		if ( file_exists( $style_path ) ) {
			wp_enqueue_style(
				'by40q-global-settings',
				BY40Q_GLOBAL_SETTINGS_URL . 'build/scripts/settings.css',
				array( 'wp-components' ),
				$asset['version']
			);
		}
	}
}
