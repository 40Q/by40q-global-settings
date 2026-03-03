/**
 * SettingsTabs — renders a @wordpress/components TabPanel.
 *
 * Each tab contains its registered fields. If only one tab is registered,
 * fields are rendered directly without a tab wrapper.
 */

import { createElement } from '@wordpress/element';
import { TabPanel } from '@wordpress/components';
import type { FieldValues, SettingsSchema, ShortcodeSettings } from '../types';
import FieldRenderer from './fields/FieldRenderer';

interface SettingsTabsProps {
	schema:            SettingsSchema;
	values:            FieldValues;
	onChange:          ( key: string, value: FieldValues[ string ] ) => void;
	shortcodeSettings: ShortcodeSettings;
	onShortcodeChange: ( key: string, enabled: boolean, slug: string ) => void;
}

export default function SettingsTabs( { schema, values, onChange, shortcodeSettings, onShortcodeChange }: SettingsTabsProps ) {
	if ( schema.length === 0 ) {
		return createElement(
			'p',
			{ className: 'by40q-global-settings__empty' },
			'No settings have been registered yet.'
		);
	}

	// If there is only one tab, skip the tab chrome and render fields directly.
	if ( schema.length === 1 ) {
		return createElement(
			'div',
			{ className: 'by40q-global-settings__fields' },
			...schema[ 0 ].fields.map( ( field ) =>
				createElement( FieldRenderer, {
					key:               field.key,
					field,
					value:             values[ field.key ] ?? field.default,
					onChange:          ( value ) => onChange( field.key, value ),
					shortcodeSettings,
					onShortcodeChange,
				} )
			)
		);
	}

	const tabs = schema.map( ( tab ) => ( {
		name:  tab.key,
		title: tab.label,
	} ) );

	return createElement(
		TabPanel,
		{
			className: 'by40q-global-settings__tabs',
			tabs,
			children: ( tab: { name: string; title: string } ) => {
				const tabData = schema.find( ( t ) => t.key === tab.name );
				if ( ! tabData ) {
					return null;
				}
				return createElement(
					'div',
					{ className: 'by40q-global-settings__fields' },
					...tabData.fields.map( ( field ) =>
						createElement( FieldRenderer, {
							key:               field.key,
							field,
							value:             values[ field.key ] ?? field.default,
							onChange:          ( value ) => onChange( field.key, value ),
							shortcodeSettings,
							onShortcodeChange,
						} )
					)
				);
			},
		}
	);
}
