import { createElement } from '@wordpress/element';
import { TextControl } from '@wordpress/components';
import type { FieldDefinition, FieldValue } from '../../types';

interface Props {
	field:    FieldDefinition;
	value:    FieldValue;
	onChange: ( value: FieldValue ) => void;
}

export default function TextField( { field, value, onChange }: Props ) {
	return createElement( TextControl, {
		label:    field.label,
		value:    ( value as string ) ?? '',
		onChange: ( next: string ) => onChange( next ),
		type:     field.inputType || undefined,
		__nextHasNoMarginBottom: true,
		__next40pxDefaultSize: true,
	} );
}
