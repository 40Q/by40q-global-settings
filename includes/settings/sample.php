<?php
/**
 * Sample tab — one field of every available type for testing and reference.
 *
 * Remove or comment out this file's require_once in settings.php once
 * you have confirmed all field types are working correctly.
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
				'key'   => 'sample',
				'label' => 'Sample Fields',
				'order' => 99,
			]
		);

		Field_Registry::register_field(
			[
				'key'         => 'sample_text',
				'label'       => 'Text',
				'type'        => 'text',
				'tab'         => 'sample',
				'default'     => '',
				'description' => 'Single-line text input.',
			]
		);

		Field_Registry::register_field(
			[
				'key'         => 'sample_textarea',
				'label'       => 'Textarea',
				'type'        => 'textarea',
				'tab'         => 'sample',
				'default'     => '',
				'description' => 'Multi-line plain text.',
			]
		);

		Field_Registry::register_field(
			[
				'key'         => 'sample_richtext',
				'label'       => 'Rich Text',
				'type'        => 'richtext',
				'tab'         => 'sample',
				'default'     => '',
				'description' => 'HTML textarea with live preview.',
			]
		);

		Field_Registry::register_field(
			[
				'key'         => 'sample_toggle',
				'label'       => 'Toggle',
				'type'        => 'toggle',
				'tab'         => 'sample',
				'default'     => false,
				'description' => 'On/off boolean switch.',
			]
		);

		Field_Registry::register_field(
			[
				'key'         => 'sample_image',
				'label'       => 'Image',
				'type'        => 'image',
				'tab'         => 'sample',
				'default'     => null,
				'description' => 'WordPress media library image (stores attachment ID).',
			]
		);

		Field_Registry::register_field(
			[
				'key'         => 'sample_url',
				'label'       => 'URL',
				'type'        => 'url',
				'tab'         => 'sample',
				'default'     => '',
				'description' => 'URL input — stored via esc_url_raw().',
			]
		);

		Field_Registry::register_field(
			[
				'key'         => 'sample_select',
				'label'       => 'Select',
				'type'        => 'select',
				'tab'         => 'sample',
				'default'     => 'option_a',
				'description' => 'Dropdown — requires a choices array.',
				'choices'     => [
					[ 'label' => 'Option A', 'value' => 'option_a' ],
					[ 'label' => 'Option B', 'value' => 'option_b' ],
					[ 'label' => 'Option C', 'value' => 'option_c' ],
				],
			]
		);

		// Input type hints.
		Field_Registry::register_field(
			[
				'key'         => 'sample_email',
				'label'       => 'Email (input_type: email)',
				'type'        => 'text',
				'input_type'  => 'email',
				'tab'         => 'sample',
				'default'     => '',
				'description' => 'Text field with input_type "email" — triggers browser email validation and mobile keyboard.',
			]
		);

		Field_Registry::register_field(
			[
				'key'         => 'sample_tel',
				'label'       => 'Phone (input_type: tel)',
				'type'        => 'text',
				'input_type'  => 'tel',
				'tab'         => 'sample',
				'default'     => '',
				'description' => 'Text field with input_type "tel" and a custom shortcode slug "sample_phone".',
			]
		);

		// Repeater field.
		Field_Registry::register_field(
			[
				'key'           => 'sample_links',
				'label'         => 'Links',
				'type'          => 'repeater',
				'repeater_type' => 'url',
				'sub_label'     => 'URL',
				'tab'           => 'sample',
				'default'       => [],
				'description'   => 'Repeater of URL sub-fields.',
			]
		);
	}
);
