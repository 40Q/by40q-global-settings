<?php
/**
 * Field Registry — stores tab and field definitions registered by plugins.
 *
 * Usage (from any plugin, hooked on `by40q_register_global_settings`):
 *
 *   add_action( 'by40q_register_global_settings', function() {
 *       Field_Registry::register_tab( [ 'key' => 'general', 'label' => 'General', 'order' => 10 ] );
 *       Field_Registry::register_field( [
 *           'key'     => 'site_slogan',
 *           'label'   => 'Site Slogan',
 *           'type'    => 'text',
 *           'tab'     => 'general',
 *           'default' => '',
 *       ] );
 *   } );
 *
 * @package By40Q\GlobalSettings
 */

declare( strict_types=1 );

namespace By40Q\GlobalSettings;

defined( 'ABSPATH' ) || exit;

/**
 * Stores and retrieves registered tabs and fields.
 */
class Field_Registry {

	/** @var array<string, array<string, mixed>> Tabs keyed by tab key. */
	private static array $tabs = array();

	/** @var array<string, array<string, mixed>> Fields keyed by field key. */
	private static array $fields = array();

	/** @var array<string, array<string, mixed>> Submenus keyed by submenu key. */
	private static array $submenus = array();

	/**
	 * Register a submenu page under Global Settings in the WP admin sidebar.
	 *
	 * @param array{key: string, label: string, order?: int} $submenu Submenu definition.
	 *   Required: key (string), label (string).
	 *   Optional: order (int, default 10).
	 */
	public static function register_submenu( array $submenu ): void {
		$key = sanitize_key( $submenu['key'] ?? '' );
		if ( empty( $key ) ) {
			_doing_it_wrong( __METHOD__, 'Submenu must have a non-empty "key".', '1.0.0' );
			return;
		}

		self::$submenus[ $key ] = array(
			'key'   => $key,
			'label' => sanitize_text_field( $submenu['label'] ?? $key ),
			'order' => (int) ( $submenu['order'] ?? 10 ),
		);
	}

	/**
	 * Return registered submenus sorted by order.
	 *
	 * @return array<string, array<string, mixed>>
	 */
	public static function get_submenus(): array {
		$submenus = self::$submenus;
		uasort( $submenus, fn( $a, $b ) => $a['order'] <=> $b['order'] );
		return $submenus;
	}

	/**
	 * Register a tab.
	 *
	 * @param array{key: string, label: string, order?: int, page?: string} $tab Tab definition.
	 *   Optional: page (string) — key of the submenu page this tab belongs to.
	 *   Defaults to 'main' (the primary Global Settings page).
	 */
	public static function register_tab( array $tab ): void {
		$key = sanitize_key( $tab['key'] ?? '' );
		if ( empty( $key ) ) {
			_doing_it_wrong( __METHOD__, 'Tab must have a non-empty "key".', '1.0.0' );
			return;
		}

		self::$tabs[ $key ] = array(
			'key'   => $key,
			'label' => sanitize_text_field( $tab['label'] ?? $key ),
			'order' => (int) ( $tab['order'] ?? 10 ),
			'page'  => sanitize_key( $tab['page'] ?? 'main' ),
		);
	}

	/**
	 * Register a field.
	 *
	 * @param array<string, mixed> $field Field definition.
	 *   Required keys: key (string), label (string), type (string), tab (string).
	 *   Optional: default (mixed), choices (array, for "select"), description (string).
	 */
	public static function register_field( array $field ): void {
		$key = sanitize_key( $field['key'] ?? '' );
		if ( empty( $key ) ) {
			_doing_it_wrong( __METHOD__, 'Field must have a non-empty "key".', '1.0.0' );
			return;
		}

		$valid_types = array( 'text', 'textarea', 'richtext', 'toggle', 'image', 'url', 'select', 'repeater' );
		$type        = $field['type'] ?? 'text';
		if ( ! in_array( $type, $valid_types, true ) ) {
			_doing_it_wrong(
				__METHOD__,
				sprintf( 'Field type "%s" is not valid. Allowed: %s.', esc_html( $type ), esc_html( implode( ', ', $valid_types ) ) ),
				'1.0.0'
			);
			return;
		}

		// Repeater sub-field type — any non-repeater type, defaults to 'text'.
		$repeater_sub_types = array( 'text', 'textarea', 'richtext', 'toggle', 'image', 'url', 'select' );
		$repeater_type      = in_array( $field['repeater_type'] ?? 'text', $repeater_sub_types, true )
			? ( $field['repeater_type'] ?? 'text' )
			: 'text';

		self::$fields[ $key ] = array(
			'key'               => $key,
			'label'             => sanitize_text_field( $field['label'] ?? $key ),
			'type'              => $type,
			'tab'               => sanitize_key( $field['tab'] ?? 'general' ),
			'default'           => $field['default'] ?? null,
			'description'       => sanitize_text_field( $field['description'] ?? '' ),
			'choices'           => is_array( $field['choices'] ?? null ) ? $field['choices'] : array(),
			// HTML input type hint for text fields (e.g. 'email', 'tel', 'number').
			'input_type'        => sanitize_key( $field['input_type'] ?? '' ),
			// Repeater-specific.
			'repeater_type'     => $repeater_type,
			'sub_label'         => sanitize_text_field( $field['sub_label'] ?? '' ),
			// Disable shortcode registration for this field.
			'disable_shortcode' => (bool) ( $field['disable_shortcode'] ?? false ),
		);
	}

	/**
	 * Return the schema for a given page: tabs (sorted) each containing their fields,
	 * with saved values merged in.
	 *
	 * @param string $page Page context key. Defaults to 'main' (the primary page).
	 * @return array<string, mixed>
	 */
	public static function get_schema( string $page = 'main' ): array {
		$saved_values = self::get_saved_values();

		// Sort tabs by order, filtered to the requested page.
		$tabs = array_filter( self::$tabs, fn( $t ) => ( $t['page'] ?? 'main' ) === $page );
		uasort( $tabs, fn( $a, $b ) => $a['order'] <=> $b['order'] );

		$schema = array();
		foreach ( $tabs as $tab_key => $tab ) {
			$tab_fields = array();
			foreach ( self::$fields as $field_key => $field ) {
				if ( $field['tab'] !== $tab_key ) {
					continue;
				}
				$field['value'] = array_key_exists( $field_key, $saved_values )
					? $saved_values[ $field_key ]
					: $field['default'];
				$tab_fields[]   = $field;
			}
			$tab['fields'] = $tab_fields;
			$schema[]      = $tab;
		}

		// Append fields with no matching tab under a virtual "General" tab.
		$orphan_fields = array();
		foreach ( self::$fields as $field_key => $field ) {
			if ( ! isset( self::$tabs[ $field['tab'] ] ) ) {
				$field['value']  = array_key_exists( $field_key, $saved_values )
					? $saved_values[ $field_key ]
					: $field['default'];
				$orphan_fields[] = $field;
			}
		}
		if ( ! empty( $orphan_fields ) ) {
			array_unshift(
				$schema,
				array(
					'key'    => 'general',
					'label'  => 'General',
					'order'  => 0,
					'fields' => $orphan_fields,
				)
			);
		}

		return $schema;
	}

	/**
	 * Return registered fields as a flat associative array keyed by field key (no values).
	 *
	 * @return array<string, array<string, mixed>>
	 */
	public static function get_fields(): array {
		return self::$fields;
	}

	/**
	 * Get raw saved values from wp_options.
	 *
	 * @return array<string, mixed>
	 */
	public static function get_saved_values(): array {
		$option = get_option( 'by40q_global_settings', array() );
		return is_array( $option ) ? $option : array();
	}

	/**
	 * Persist values to wp_options after sanitizing each against its registered type.
	 *
	 * @param array<string, mixed> $values Raw values from REST or form submission.
	 * @return array<string, mixed> Sanitized and saved values.
	 */
	public static function save_values( array $values ): array {
		// Start from existing saved values so saving one page doesn't wipe another page's fields.
		$sanitized = self::get_saved_values();
		foreach ( self::$fields as $field_key => $field ) {
			if ( ! array_key_exists( $field_key, $values ) ) {
				continue;
			}
			$sanitized[ $field_key ] = self::sanitize_value( $values[ $field_key ], $field['type'], $field['repeater_type'] );
		}
		update_option( 'by40q_global_settings', $sanitized );
		return $sanitized;
	}

	/**
	 * Get stored shortcode settings (which text fields have shortcodes enabled and their slugs).
	 *
	 * @return array<string, array{enabled: bool, slug: string}>
	 */
	public static function get_shortcode_settings(): array {
		$option = get_option( 'by40q_shortcode_settings', array() );
		return is_array( $option ) ? $option : array();
	}

	/**
	 * Persist shortcode settings (enabled/slug per text field key).
	 *
	 * @param array<string, mixed> $settings Raw settings from REST request.
	 */
	public static function save_shortcode_settings( array $settings ): void {
		$sanitized = array();
		foreach ( $settings as $key => $setting ) {
			if ( ! is_array( $setting ) ) {
				continue;
			}
			$sanitized[ sanitize_key( $key ) ] = array(
				'enabled' => (bool) ( $setting['enabled'] ?? false ),
				'slug'    => sanitize_key( $setting['slug'] ?? '' ),
			);
		}
		update_option( 'by40q_shortcode_settings', $sanitized );
	}

	/**
	 * Sanitize a single value according to field type.
	 *
	 * @param mixed  $value Raw value.
	 * @param string $type  Field type.
	 * @return mixed Sanitized value.
	 */
	private static function sanitize_value( mixed $value, string $type, string $repeater_type = 'text' ): mixed {
		return match ( $type ) {
			'text'      => sanitize_text_field( (string) $value ),
			'textarea'  => sanitize_textarea_field( (string) $value ),
			'richtext'  => wp_kses_post( (string) $value ),
			'toggle'    => (bool) $value,
			'image'     => (int) $value, // attachment ID.
			'url'       => esc_url_raw( (string) $value ),
			'select'    => sanitize_text_field( (string) $value ),
			'repeater'  => is_array( $value )
				? array_values( array_map( fn( $item ) => self::sanitize_value( $item, $repeater_type ), $value ) )
				: array(),
			default     => sanitize_text_field( (string) $value ),
		};
	}
}
