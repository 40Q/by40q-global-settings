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
			array(
				'key'   => 'ai',
				'label' => 'AI',
				'order' => 15,
			)
		);

		// Register a tab scoped to this submenu page.
		Field_Registry::register_tab(
			array(
				'key'   => 'ai_general',
				'label' => 'General',
				'page'  => 'ai',
				'order' => 10,
			)
		);

		// AI Provider Selection.
		Field_Registry::register_field(
			array(
				'key'               => 'ai_provider',
				'label'             => 'AI Provider',
				'type'              => 'select',
				'tab'               => 'ai_general',
				'default'           => 'openai',
				'choices'           => array(
					array(
						'label' => 'OpenAI',
						'value' => 'openai',
					),
					array(
						'label' => 'Anthropic',
						'value' => 'anthropic',
					),
					array(
						'label' => 'Custom Endpoint',
						'value' => 'custom',
					),
				),
				'description'       => 'Select the AI service provider for all 40Q AI tools.',
				'disable_shortcode' => true,
			)
		);

		// OpenAI Configuration.
		Field_Registry::register_field(
			array(
				'key'               => 'ai_openai_api_key',
				'label'             => 'OpenAI API Key',
				'type'              => 'text',
				'input_type'        => 'password',
				'tab'               => 'ai_general',
				'default'           => '',
				'description'       => 'Your OpenAI API key. Get one at <a href="https://platform.openai.com/api-keys" target="_blank">platform.openai.com</a>.',
				'disable_shortcode' => true,
			)
		);

		Field_Registry::register_field(
			array(
				'key'               => 'ai_openai_model',
				'label'             => 'OpenAI Model',
				'type'              => 'text',
				'tab'               => 'ai_general',
				'default'           => 'gpt-4o',
				'description'       => 'Model name (e.g., gpt-4o, gpt-4-turbo, gpt-3.5-turbo).',
				'disable_shortcode' => true,
			)
		);

		// Anthropic Configuration.
		Field_Registry::register_field(
			array(
				'key'               => 'ai_anthropic_api_key',
				'label'             => 'Anthropic API Key',
				'type'              => 'text',
				'input_type'        => 'password',
				'tab'               => 'ai_general',
				'default'           => '',
				'description'       => 'Your Anthropic API key. Get one at <a href="https://console.anthropic.com/" target="_blank">console.anthropic.com</a>.',
				'disable_shortcode' => true,
			)
		);

		Field_Registry::register_field(
			array(
				'key'               => 'ai_anthropic_model',
				'label'             => 'Anthropic Model',
				'type'              => 'text',
				'tab'               => 'ai_general',
				'default'           => 'claude-3-5-sonnet-20241022',
				'description'       => 'Model name (e.g., claude-3-5-sonnet-20241022, claude-3-opus-20240229).',
				'disable_shortcode' => true,
			)
		);

		// Custom Provider Configuration.
		Field_Registry::register_field(
			array(
				'key'               => 'ai_custom_endpoint',
				'label'             => 'Custom Endpoint URL',
				'type'              => 'url',
				'tab'               => 'ai_general',
				'default'           => '',
				'description'       => 'Full URL to your custom AI endpoint (for self-hosted or alternative providers).',
				'disable_shortcode' => true,
			)
		);

		Field_Registry::register_field(
			array(
				'key'               => 'ai_custom_api_key',
				'label'             => 'Custom API Key',
				'type'              => 'text',
				'input_type'        => 'password',
				'tab'               => 'ai_general',
				'default'           => '',
				'description'       => 'API key for custom endpoint (if required).',
				'disable_shortcode' => true,
			)
		);

		Field_Registry::register_field(
			array(
				'key'               => 'ai_custom_model',
				'label'             => 'Custom Model',
				'type'              => 'text',
				'tab'               => 'ai_general',
				'default'           => '',
				'description'       => 'Model name for custom endpoint.',
				'disable_shortcode' => true,
			)
		);

		// Global AI Context.
		Field_Registry::register_field(
			array(
				'key'               => 'ai_context',
				'label'             => 'Global Context',
				'type'              => 'textarea',
				'tab'               => 'ai_general',
				'default'           => '',
				'description'       => 'Default system prompt or context sent with every AI request across all tools.',
				'disable_shortcode' => true,
			)
		);
	}
);
