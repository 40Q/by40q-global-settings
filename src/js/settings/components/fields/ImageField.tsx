/**
 * ImageField — media picker backed by the WordPress media library.
 *
 * Opens the native WP media modal on button click, stores the attachment ID,
 * and displays a thumbnail preview of the selected image.
 */

import { createElement, useState, useEffect } from '@wordpress/element';
import { Button } from '@wordpress/components';
import type { FieldDefinition, FieldValue } from '../../types';

interface WpMedia {
	on: ( event: string, cb: () => void ) => WpMedia;
	open: () => void;
	state: () => {
		get: ( key: string ) => {
			first: () => { toJSON: () => { id: number; url: string; sizes?: { medium?: { url: string } } } };
		};
	};
}

declare global {
	interface Window {
		wp: {
			media: ( args: { title: string; button: { text: string }; multiple: boolean; library?: { type: string } } ) => WpMedia;
		};
	}
}

interface AttachmentPreview {
	id:  number;
	url: string;
}

interface Props {
	field:    FieldDefinition;
	value:    FieldValue;
	onChange: ( value: FieldValue ) => void;
}

export default function ImageField( { field, value, onChange }: Props ) {
	const attachmentId    = ( value as number ) || 0;
	const [ preview, setPreview ] = useState< AttachmentPreview | null >( null );

	// Fetch thumbnail URL for the saved attachment ID via REST API.
	useEffect( () => {
		if ( ! attachmentId ) {
			setPreview( null );
			return;
		}
		// Derive the WP REST base from the localized restUrl (strip the plugin route).
		const restBase = window.by40qGlobalSettings?.restUrl?.replace( /\/by40q\/.*$/, '' ) ?? '/wp-json';
		fetch( `${ restBase }/wp/v2/media/${ attachmentId }`, {
			headers: {
				'X-WP-Nonce': window.by40qGlobalSettings?.nonce ?? '',
			},
		} )
			.then( ( res ) => res.json() )
			.then( ( data: { id: number; source_url: string } ) => {
				setPreview( { id: data.id, url: data.source_url } );
			} )
			.catch( () => setPreview( null ) );
	}, [ attachmentId ] );

	const openMediaModal = () => {
		if ( ! window.wp?.media ) {
			/* eslint-disable-next-line no-alert */
			alert( 'WordPress media library is not available. Make sure wp-media is enqueued.' );
			return;
		}
		const frame = window.wp.media( {
			title:    'Select Image',
			button:   { text: 'Use this image' },
			multiple: false,
			library:  { type: 'image' },
		} );

		frame
			.on( 'select', () => {
				// .get('selection') returns a Backbone Collection; .first() gets the single model.
				const attachment = frame.state().get( 'selection' ).first().toJSON();
				const thumbUrl   = attachment.sizes?.medium?.url ?? attachment.url;
				onChange( attachment.id );
				setPreview( { id: attachment.id, url: thumbUrl } );
			} )
			.open();
	};

	return createElement(
		'div',
		{ className: 'by40q-field--image' },
		createElement( 'label', { className: 'by40q-field__label components-base-control__label' }, field.label ),
		preview && createElement(
			'div',
			{ className: 'by40q-field__image-preview' },
			createElement( 'img', {
				src:    preview.url,
				alt:    field.label,
				width:  150,
				height: 'auto',
				style:  { display: 'block', marginBottom: '8px', borderRadius: '4px' },
			} )
		),
		createElement(
			'div',
			{ className: 'by40q-field__image-actions' },
			createElement(
				Button,
				{
					variant: 'secondary',
					onClick: openMediaModal,
				},
				preview ? 'Change Image' : 'Select Image'
			),
			preview && createElement(
				Button,
				{
					variant:     'tertiary',
					isDestructive: true,
					onClick: () => {
						onChange( 0 );
						setPreview( null );
					},
					style: { marginLeft: '8px' },
				},
				'Remove'
			)
		),
		attachmentId > 0 && createElement(
			'p',
			{ className: 'description', style: { marginTop: '4px' } },
			`Attachment ID: ${ attachmentId }`
		)
	);
}
