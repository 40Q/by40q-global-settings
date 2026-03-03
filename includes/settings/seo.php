<?php
/**
 * SEO tab — sample submenu page with SEO-related fields.
 *
 * This partial also registers its own submenu page via register_submenu().
 * The tab is scoped to that page with 'page' => 'seo'.
 *
 * @package By40Q\GlobalSettings
 */

declare( strict_types=1 );

namespace By40Q\GlobalSettings;

defined( 'ABSPATH' ) || exit;

add_action(
	'by40q_register_global_settings',
	function () {

		// Register the sidebar submenu page.
		Field_Registry::register_submenu(
			[
				'key'   => 'seo',
				'label' => 'SEO',
				'order' => 10,
			]
		);

		// Register a tab scoped to this submenu page.
		Field_Registry::register_tab(
			[
				'key'   => 'seo_general',
				'label' => 'General',
				'page'  => 'seo',
				'order' => 10,
			]
		);

		Field_Registry::register_field(
			[
				'key'         => 'seo_site_title',
				'label'       => 'Site Title Override',
				'type'        => 'text',
				'tab'         => 'seo_general',
				'default'     => '',
				'description' => 'Replaces the default WP site title in <title> tags.',
			]
		);

		Field_Registry::register_field(
			[
				'key'         => 'seo_meta_description',
				'label'       => 'Default Meta Description',
				'type'        => 'textarea',
				'tab'         => 'seo_general',
				'default'     => '',
				'description' => 'Fallback meta description used when a page has none set.',
			]
		);

		Field_Registry::register_field(
			[
				'key'         => 'seo_og_image',
				'label'       => 'Default OG Image',
				'type'        => 'image',
				'tab'         => 'seo_general',
				'default'     => null,
				'description' => 'Fallback Open Graph image for social sharing.',
			]
		);
	}
);
