/**
 * SettingsPage — top-level component.
 *
 * Fetches the schema from the REST API, manages field value state,
 * and handles save operations with feedback via @wordpress/notices.
 */

import { createElement, useState, useEffect, Fragment } from '@wordpress/element';
import { Button, Spinner, Notice } from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';
import type {
	FieldValues,
	GetSettingsResponse,
	SaveSettingsResponse,
	SettingsSchema,
	ShortcodeSettings,
} from '../types';
import SettingsTabs from './SettingsTabs';

type SaveStatus = 'idle' | 'saving' | 'saved' | 'error';

export default function SettingsPage() {
	const [ schema, setSchema ]   = useState< SettingsSchema >( [] );
	const [ values, setValues ]   = useState< FieldValues >( {} );
	const [ loading, setLoading ] = useState< boolean >( true );
	const [ status, setStatus ]         = useState< SaveStatus >( 'idle' );
	const [ shortcodes, setShortcodes ] = useState< ShortcodeSettings >( {} );

	// Fetch schema + current values on mount.
	useEffect( () => {
		const page = window.by40qGlobalSettings?.page ?? 'main';
		apiFetch< GetSettingsResponse >( { path: `/by40q/v1/global-settings?page=${ page }` } )
			.then( ( response ) => {
				setSchema( response.schema );

				// Flatten initial values from schema into a key→value map.
				const initialValues: FieldValues = {};
				response.schema.forEach( ( tab ) => {
					tab.fields.forEach( ( field ) => {
						initialValues[ field.key ] = field.value;
					} );
				} );
				setValues( initialValues );
				setShortcodes( response.shortcodes ?? {} );
				setLoading( false );
			} )
			.catch( () => {
				setLoading( false );
				setStatus( 'error' );
			} );
	}, [] );

	/**
	 * Update a single field value in local state.
	 */
	const handleChange = ( key: string, value: FieldValues[ string ] ) => {
		setValues( ( prev ) => ( { ...prev, [ key ]: value } ) );
		// Reset saved confirmation when user starts editing again.
		if ( status === 'saved' ) {
			setStatus( 'idle' );
		}
	};

	const handleShortcodeChange = ( key: string, enabled: boolean, slug: string ) => {
		setShortcodes( ( prev ) => ( { ...prev, [ key ]: { enabled, slug } } ) );
	};

	/**
	 * POST current values to the REST API.
	 */
	const handleSave = async () => {
		setStatus( 'saving' );
		try {
			await apiFetch< SaveSettingsResponse >( {
				path:   '/by40q/v1/global-settings',
				method: 'POST',
				data:   { values, shortcodes },
			} );
			setStatus( 'saved' );
		} catch {
			setStatus( 'error' );
		}
	};

	return createElement(
		Fragment,
		null,
		createElement(
			'div',
			{ className: 'by40q-global-settings' },
			createElement(
				'div',
				{ className: 'by40q-global-settings__header' },
				createElement( 'h1', null, 'Global Settings' ),
				createElement(
					'div',
					{ className: 'by40q-global-settings__actions' },
					status === 'saved' && createElement(
						Notice,
						{
							status:      'success',
							isDismissible: false,
							className:   'by40q-global-settings__notice',
						children:      'Settings saved.',
					},
					),
					status === 'error' && createElement(
						Notice,
						{
							status:      'error',
							isDismissible: false,
							className:   'by40q-global-settings__notice',
						children:      'Failed to save settings. Please try again.',
					},
					),
					createElement(
						Button,
						{
							variant:   'primary',
							onClick:   handleSave,
							isBusy:    status === 'saving',
							disabled:  loading || status === 'saving',
						},
						status === 'saving' ? 'Saving…' : 'Save Settings'
					)
				)
			),
			loading
				? createElement(
					'div',
					{ className: 'by40q-global-settings__loading' },
					createElement( Spinner, null ),
					createElement( 'p', null, 'Loading settings…' )
				)
				: createElement(
					SettingsTabs,
					{
						schema,
						values,
						onChange:          handleChange,
						shortcodeSettings: shortcodes,
						onShortcodeChange: handleShortcodeChange,
					}
				)
		)
	);
}
