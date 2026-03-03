import { createElement } from '@wordpress/element';
import { TextareaControl } from '@wordpress/components';
import type { FieldDefinition, FieldValue } from '../../types';

interface Props {
	field:    FieldDefinition;
	value:    FieldValue;
	onChange: ( value: FieldValue ) => void;
}

export default function TextareaField( { field, value, onChange }: Props ) {
	return createElement( TextareaControl, {
		label:    field.label,
		value:    ( value as string ) ?? '',
		onChange: ( next: string ) => onChange( next ),
		__nextHasNoMarginBottom: true,
	} );
}
