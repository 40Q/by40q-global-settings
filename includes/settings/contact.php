<?php
/**
 * Contact tab — fields for contact details displayed site-wide.
 *
 * @package By40Q\GlobalSettings
 */

declare( strict_types=1 );

namespace By40Q\GlobalSettings;

defined( 'ABSPATH' ) || exit;

add_action(
	'by40q_register_global_settings',
	function () {

		Field_Registry::register_tab(
			[
				'key'   => 'contact',
				'label' => 'Contact',
				'order' => 10,
			]
		);

		Field_Registry::register_field(
			[
				'key'         => 'contact_email',
				'label'       => 'Contact Email',
				'type'        => 'text',
				'input_type'  => 'email',
				'tab'         => 'contact',
				'default'     => '',
				'description' => 'Primary contact email address.',
			]
		);

		Field_Registry::register_field(
			[
				'key'         => 'contact_phone',
				'label'       => 'Contact Phone',
				'type'        => 'text',
				'input_type'  => 'tel',
				'tab'         => 'contact',
				'default'     => '',
				'description' => 'Primary contact phone number.',
			]
		);

		Field_Registry::register_field(
			[
				'key'         => 'contact_address',
				'label'       => 'Address',
				'type'        => 'textarea',
				'tab'         => 'contact',
				'default'     => '',
				'description' => 'Physical address displayed in the footer.',
			]
		);
	}
);
