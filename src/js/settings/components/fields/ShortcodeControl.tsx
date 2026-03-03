/**
 * ShortcodeControl — per-field shortcode toggle and editable slug for the admin UI.
 *
 * Shown below each text field. Allows the user to:
 *   1. Toggle shortcode registration on/off.
 *   2. Edit the shortcode slug (pre-filled with tab_fieldkey as default).
 *   3. Copy the shortcode to the clipboard.
 */

import { createElement, useState } from '@wordpress/element';
import { ToggleControl, TextControl, Button } from '@wordpress/components';

interface Props {
	enabled:     boolean;
	slug:        string;
	defaultSlug: string;
	onChange:    ( enabled: boolean, slug: string ) => void;
}

export default function ShortcodeControl( { enabled, slug, defaultSlug, onChange }: Props ) {
	const [ copied, setCopied ] = useState( false );

	// Use defaultSlug as placeholder when user hasn't typed a custom slug yet.
	const activeSlug = slug || defaultSlug;

	function handleToggle( next: boolean ) {
		onChange( next, slug );
	}

	function handleSlugChange( next: string ) {
		onChange( enabled, next );
	}

	function handleCopy() {
		navigator.clipboard.writeText( '[' + activeSlug + ']' ).then( () => {
			setCopied( true );
			setTimeout( () => setCopied( false ), 1500 );
		} );
	}

	return createElement(
		'div',
		{ className: 'by40q-shortcode-control' },
		createElement( ToggleControl, {
			label:    'Enable shortcode',
			checked:  enabled,
			onChange: handleToggle,
			__nextHasNoMarginBottom: true,
		} ),
		enabled ? createElement(
			'div',
			{ className: 'by40q-shortcode-control__slug-row' },
			createElement( 'span', { className: 'by40q-shortcode-control__bracket' }, '[' ),
			createElement( TextControl, {
				value:                  activeSlug,
				onChange:               handleSlugChange,
				__nextHasNoMarginBottom: true,
				__next40pxDefaultSize:  true,
			} ),
			createElement( 'span', { className: 'by40q-shortcode-control__bracket' }, ']' ),
			createElement(
				Button,
				{
					variant:   'link',
					className: 'by40q-shortcode-control__copy',
					onClick:   handleCopy,
				},
				copied ? 'Copied!' : 'Copy'
			)
		) : null
	);
}
