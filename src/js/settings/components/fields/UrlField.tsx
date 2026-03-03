import { createElement } from '@wordpress/element';
import { TextControl } from '@wordpress/components';
import type { FieldDefinition, FieldValue } from '../../types';

interface Props {
	field:    FieldDefinition;
	value:    FieldValue;
	onChange: ( value: FieldValue ) => void;
}

export default function UrlField( { field, value, onChange }: Props ) {
	return createElement( TextControl, {
		label:    field.label,
		type:     'url',
		value:    ( value as string ) ?? '',
		onChange: ( next: string ) => onChange( next ),
		placeholder: 'https://',
		__nextHasNoMarginBottom: true,
		__next40pxDefaultSize: true,
	} );
}
