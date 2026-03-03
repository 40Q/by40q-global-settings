<?php
/**
 * Main plugin class — bootstraps all components.
 *
 * @package By40Q\GlobalSettings
 */

declare( strict_types=1 );

namespace By40Q\GlobalSettings;

defined( 'ABSPATH' ) || exit;

/**
 * Main singleton class for the Global Settings plugin.
 */
final class Global_Settings {

	private static ?Global_Settings $instance = null;

	private function __construct() {
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
		add_action( 'admin_menu', array( $this, 'register_admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
	}

	/**
	 * Get the singleton instance.
	 */
	public static function instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Fire the registration action so other plugins can register their tabs and fields.
	 */
	public function init(): void {
		/**
		 * Hook into this action to register tabs and fields.
		 *
		 * Example:
		 *   add_action( 'by40q_register_global_settings', function() {
		 *       \By40Q\GlobalSettings\Field_Registry::register_tab( [...] );
		 *       \By40Q\GlobalSettings\Field_Registry::register_field( [...] );
		 *   } );
		 */
		do_action( 'by40q_register_global_settings' );
		$this->register_shortcodes();
	}

	/**
	 * Register WP shortcodes for text, textarea, richtext, and url fields that have a shortcode slug enabled.
	 * Richtext fields are output via wp_kses_post(); all others via esc_html().
	 */
	private function register_shortcodes(): void {
		$shortcode_settings = Field_Registry::get_shortcode_settings();
		$fields             = Field_Registry::get_fields();
		foreach ( $shortcode_settings as $key => $setting ) {
			if ( empty( $setting['enabled'] ) || empty( $setting['slug'] ) ) {
				continue;
			}
			$shortcode_types = array( 'text', 'textarea', 'richtext', 'url' );
			if ( ! isset( $fields[ $key ] ) || ! in_array( $fields[ $key ]['type'], $shortcode_types, true ) ) {
				continue;
			}
			// Skip if field has shortcodes disabled.
			if ( $fields[ $key ]['disable_shortcode'] ?? false ) {
				continue;
			}
			$field_type = $fields[ $key ]['type'];
			$slug       = $setting['slug'];
			add_shortcode(
				$slug,
				static function () use ( $key, $field_type ): string {
					$values = Field_Registry::get_saved_values();
					$value  = (string) ( $values[ $key ] ?? '' );
					return 'richtext' === $field_type ? wp_kses_post( $value ) : esc_html( $value );
				}
			);
		}
	}

	/**
	 * Register REST API routes.
	 */
	public function register_rest_routes(): void {
		( new Rest_Controller() )->register_routes();
	}

	/**
	 * Register the admin menu page.
	 */
	public function register_admin_menu(): void {
		( new Admin_Page() )->register_menu();
	}

	/**
	 * Enqueue admin assets on the correct page.
	 *
	 * @param string $hook_suffix Current admin page hook.
	 */
	public function enqueue_admin_assets( string $hook_suffix ): void {
		( new Admin_Page() )->enqueue_assets( $hook_suffix );
	}
}
