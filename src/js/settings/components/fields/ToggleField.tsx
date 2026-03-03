import { createElement } from '@wordpress/element';
import { ToggleControl } from '@wordpress/components';
import type { FieldDefinition, FieldValue } from '../../types';

interface Props {
	field:    FieldDefinition;
	value:    FieldValue;
	onChange: ( value: FieldValue ) => void;
}

export default function ToggleField( { field, value, onChange }: Props ) {
	return createElement( ToggleControl, {
		label:    field.label,
		checked:  ( value as boolean ) ?? false,
		onChange: ( next: boolean ) => onChange( next ),
		__nextHasNoMarginBottom: true,
	} );
}
