/**
 * ShortcodeHint — displays a copyable shortcode badge for text fields.
 */

import { createElement, useState } from '@wordpress/element';
import { Button } from '@wordpress/components';

interface Props {
	shortcode: string;
}

export default function ShortcodeHint( { shortcode }: Props ) {
	const [ copied, setCopied ] = useState( false );

	function handleCopy() {
		navigator.clipboard.writeText( '[' + shortcode + ']' ).then( () => {
			setCopied( true );
			setTimeout( () => setCopied( false ), 1500 );
		} );
	}

	return createElement(
		'span',
		{ className: 'by40q-shortcode-hint' },
		createElement( 'code', { className: 'by40q-shortcode-hint__code' }, '[', shortcode, ']' ),
		createElement(
			Button,
			{
				variant:   'link',
				className: 'by40q-shortcode-hint__copy',
				onClick:   handleCopy,
			},
			copied ? 'Copied!' : 'Copy'
		)
	);
}
