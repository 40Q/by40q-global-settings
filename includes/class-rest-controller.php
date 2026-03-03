<?php
/**
 * REST API controller for Global Settings.
 *
 * Exposes:
 *   GET  /wp-json/by40q/v1/global-settings  — returns full schema with current values.
 *   POST /wp-json/by40q/v1/global-settings  — validates and saves new values.
 *
 * Both endpoints require the `manage_options` capability (administrators only).
 *
 * @package By40Q\GlobalSettings
 */

declare( strict_types=1 );

namespace By40Q\GlobalSettings;

defined( 'ABSPATH' ) || exit;

/**
 * REST Controller.
 */
class Rest_Controller {

	private const NAMESPACE = 'by40q/v1';
	private const ROUTE     = '/global-settings';

	/**
	 * Register REST routes.
	 */
	public function register_routes(): void {
		register_rest_route(
			self::NAMESPACE,
			self::ROUTE,
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_settings' ),
					'permission_callback' => array( $this, 'check_permission' ),
					'args'                => array(
						'page' => array(
							'required'          => false,
							'type'              => 'string',
							'default'           => 'main',
							'sanitize_callback' => 'sanitize_key',
							'description'       => 'Page context key — which submenu page to return schema for.',
						),
					),
				),
				array(
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'save_settings' ),
					'permission_callback' => array( $this, 'check_permission' ),
					'args'                => array(
						'values' => array(
							'required'    => true,
							'description' => 'Flat key→value map of all field values to save.',
						),
					),
				),
			)
		);
	}

	/**
	 * GET handler — returns the schema for the requested page with current values.
	 *
	 * @param \WP_REST_Request $request Incoming request.
	 * @return \WP_REST_Response
	 */
	public function get_settings( \WP_REST_Request $request ): \WP_REST_Response {
		$page              = sanitize_key( $request->get_param( 'page' ) ?? 'main' );
		$stored_shortcodes = Field_Registry::get_shortcode_settings();
		$shortcodes        = array();
		foreach ( Field_Registry::get_fields() as $key => $field ) {
			if ( ! in_array( $field['type'], array( 'text', 'textarea', 'richtext', 'url' ), true ) ) {
				continue;
			}
			// Skip fields that have shortcodes disabled.
			if ( $field['disable_shortcode'] ?? false ) {
				continue;
			}
			$shortcodes[ $key ] = array(
				'enabled' => (bool) ( $stored_shortcodes[ $key ]['enabled'] ?? false ),
				'slug'    => (string) ( $stored_shortcodes[ $key ]['slug'] ?? '' ),
			);
		}
		return rest_ensure_response(
			array(
				'schema'     => Field_Registry::get_schema( $page ),
				'shortcodes' => $shortcodes,
			)
		);
	}

	/**
	 * POST handler — saves field values.
	 *
	 * @param \WP_REST_Request $request Incoming request.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function save_settings( \WP_REST_Request $request ): \WP_REST_Response|\WP_Error {
		$values = $request->get_param( 'values' );

		if ( ! is_array( $values ) ) {
			return new \WP_Error(
				'invalid_values',
				__( 'The "values" parameter must be an object.', 'by40q' ),
				array( 'status' => 400 )
			);
		}

		$saved = Field_Registry::save_values( $values );

		$shortcodes = $request->get_param( 'shortcodes' );
		if ( is_array( $shortcodes ) ) {
			Field_Registry::save_shortcode_settings( $shortcodes );
		}

		return rest_ensure_response(
			array(
				'success' => true,
				'saved'   => $saved,
			)
		);
	}

	/**
	 * Permission callback — requires manage_options capability.
	 *
	 * @return bool|\WP_Error
	 */
	public function check_permission(): bool|\WP_Error {
		if ( ! current_user_can( 'manage_options' ) ) {
			return new \WP_Error(
				'rest_forbidden',
				__( 'You do not have permission to access Global Settings.', 'by40q' ),
				array( 'status' => 403 )
			);
		}
		return true;
	}
}
