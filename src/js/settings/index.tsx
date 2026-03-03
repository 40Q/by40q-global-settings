/**
 * Entry point — mounts the Global Settings React app.
 */

import './settings.scss';
import { createElement, render } from '@wordpress/element';
import { SlotFillProvider } from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';
import SettingsPage from './components/SettingsPage';

// Register nonce with the apiFetch middleware so all REST calls are authenticated.
if ( window.by40qGlobalSettings?.nonce ) {
	apiFetch.use( apiFetch.createNonceMiddleware( window.by40qGlobalSettings.nonce ) );
}

const root = document.getElementById( 'by40q-global-settings-root' );

if ( root ) {
	render(
		createElement(
			SlotFillProvider,
			null,
			createElement( SettingsPage, null )
		),
		root
	);
}
