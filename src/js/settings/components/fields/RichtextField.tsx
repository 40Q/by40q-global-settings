/**
 * RichtextField — HTML editor backed by a <TextareaControl>.
 *
 * Stores raw HTML. A live preview is rendered below the textarea.
 *
 * Note: For a full block-editor RichText experience, this component can be
 * upgraded to use @wordpress/block-editor's <RichText> inside a
 * <BlockEditorProvider> — see the developer documentation README.
 */

import { createElement } from '@wordpress/element';
import { TextareaControl } from '@wordpress/components';
import type { FieldDefinition, FieldValue } from '../../types';

interface Props {
	field:    FieldDefinition;
	value:    FieldValue;
	onChange: ( value: FieldValue ) => void;
}

export default function RichtextField( { field, value, onChange }: Props ) {
	const html = ( value as string ) ?? '';

	return createElement(
		'div',
		{ className: 'by40q-field--richtext' },
		createElement( TextareaControl, {
			label:    field.label,
			value:    html,
			rows:     6,
			help:     'Enter HTML content.',
			onChange: ( next: string ) => onChange( next ),
			__nextHasNoMarginBottom: true,
		} ),
		html && createElement(
			'div',
			{ className: 'by40q-field__richtext-preview' },
			createElement( 'p', { className: 'description' }, 'Preview:' ),
			createElement( 'div', {
				className:              'by40q-field__richtext-preview-content',
				dangerouslySetInnerHTML: { __html: html },
			} )
		)
	);
}
