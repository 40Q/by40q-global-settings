import { createElement } from '@wordpress/element';
import { SelectControl } from '@wordpress/components';
import type { FieldDefinition, FieldValue } from '../../types';

interface Props {
	field:    FieldDefinition;
	value:    FieldValue;
	onChange: ( value: FieldValue ) => void;
}

export default function SelectField( { field, value, onChange }: Props ) {
	const options = [
		{ label: '— Select —', value: '' },
		...( field.choices ?? [] ).map( ( choice ) => ( {
			label: choice.label,
			value: choice.value,
		} ) ),
	];

	return createElement( SelectControl, {
		label:    field.label,
		value:    ( value as string ) ?? '',
		options,
		onChange: ( next: string ) => onChange( next ),
		__nextHasNoMarginBottom: true,
		__next40pxDefaultSize: true,
	} );
}
