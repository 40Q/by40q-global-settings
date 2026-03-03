<?php
/**
 * Public helper functions for the Global Settings plugin.
 *
 * @package By40Q\GlobalSettings
 */

declare( strict_types=1 );

// phpcs:disable -- Bracketed namespace syntax required when a file declares multiple namespaces.

namespace By40Q\GlobalSettings {

	defined( 'ABSPATH' ) || exit;

	/**
	 * Retrieve a single global setting value by field key.
	 *
	 * Mirrors ACF's get_field() ergonomics — call this anywhere in PHP templates,
	 * blocks, or other plugins to read a setting value.
	 *
	 * Example:
	 *   $slogan = by40q_global_setting( 'site_slogan' );
	 *   $show_banner = by40q_global_setting( 'show_top_banner', false );
	 *
	 * @param  string $key     The field key as registered via Field_Registry::register_field().
	 * @param  mixed  $default Fallback value if the key is not found or has no saved value.
	 * @return mixed           The stored value, the field's registered default, or $default.
	 */
	function by40q_global_setting( string $key, mixed $default = null ): mixed {
		static $cache = null;

		if ( null === $cache ) {
			$cache = Field_Registry::get_saved_values();
		}

		if ( array_key_exists( $key, $cache ) ) {
			return $cache[ $key ];
		}

		// Fall back to field-registered default before the caller default.
		$fields = Field_Registry::get_fields();
		if ( isset( $fields[ $key ]['default'] ) ) {
			return $fields[ $key ]['default'];
		}

		return $default;
	}
}

// ── Global alias ──────────────────────────────────────────────────────────────
// Expose get_setting() in the global namespace so themes and plugins can call
// it without knowing the By40Q\GlobalSettings namespace.

namespace {

	/**
	 * Retrieve a global setting value by key.
	 *
	 * Convenience alias for \By40Q\GlobalSettings\by40q_global_setting().
	 * Use this in themes and plugins instead of the namespaced function.
	 *
	 * Example:
	 *   echo esc_html( get_setting( 'contact_email' ) );
	 *   echo wp_get_attachment_image( (int) get_setting( 'sample_image' ), 'full' );
	 *
	 * @param  string $key     Field key registered via Field_Registry::register_field().
	 * @param  mixed  $default Fallback if the key has no saved or registered default value.
	 * @return mixed
	 */
	if ( ! function_exists( 'get_setting' ) ) {
		function get_setting( string $key, mixed $default = null ): mixed {
			return \By40Q\GlobalSettings\by40q_global_setting( $key, $default );
		}
	}
}

