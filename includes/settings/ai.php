<?php
/**
 * AI submenu page — fields for AI provider configuration.
 *
 * This partial also registers its own submenu page via register_submenu().
 * The tab is scoped to that page with 'page' => 'ai'.
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
				'key'   => 'ai',
				'label' => 'AI',
				'order' => 15,
			]
		);

		// Register a tab scoped to this submenu page.
		Field_Registry::register_tab(
			[
				'key'   => 'ai_general',
				'label' => 'General',
				'page'  => 'ai',
				'order' => 10,
			]
		);

		Field_Registry::register_field(
			[
				'key'                => 'ai_api_key',
				'label'              => 'API Key',
				'type'               => 'text',
				'tab'                => 'ai_general',
				'default'            => '',
				'description'        => 'AI provider API key (e.g. OpenAI). Stored encrypted in wp_options.',
				'disable_shortcode'  => true,
			]
		);

		Field_Registry::register_field(
			[
				'key'                => 'ai_context',
				'label'              => 'Context',
				'type'               => 'textarea',
				'tab'                => 'ai_general',
				'default'            => '',
				'description'        => 'Default system prompt or context sent with every AI request.',
				'disable_shortcode'  => true,
			]
		);
	}
);
