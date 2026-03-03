/**
 * RepeaterField — renders an ordered list of sub-field values of a single type.
 *
 * Avoids circular dependency with FieldRenderer by importing sub-field
 * components directly rather than going through FieldRenderer.
 */

import { createElement } from '@wordpress/element';
import { Button } from '@wordpress/components';
import type { FieldDefinition, FieldValue } from '../../types';

import TextField     from './TextField';
import TextareaField from './TextareaField';
import RichtextField from './RichtextField';
import ToggleField   from './ToggleField';
import ImageField    from './ImageField';
import UrlField      from './UrlField';
import SelectField   from './SelectField';

interface Props {
	field:    FieldDefinition;
	value:    FieldValue;
	onChange: ( value: FieldValue ) => void;
}

function defaultForType( type: string ): FieldValue {
	switch ( type ) {
		case 'toggle': return false;
		case 'image':  return null;
		default:       return '';
	}
}

function renderSubField(
	subType:  string,
	subField: FieldDefinition,
	val:      FieldValue,
	onChange: ( v: FieldValue ) => void
) {
	const props = { field: subField, value: val, onChange };
	switch ( subType ) {
		case 'textarea': return createElement( TextareaField, props );
		case 'richtext': return createElement( RichtextField, props );
		case 'toggle':   return createElement( ToggleField, props );
		case 'image':    return createElement( ImageField, props );
		case 'url':      return createElement( UrlField, props );
		case 'select':   return createElement( SelectField, props );
		default:         return createElement( TextField, props );
	}
}

export default function RepeaterField( { field, value, onChange }: Props ) {
	const items    = Array.isArray( value ) ? ( value as FieldValue[] ) : [];
	const subType  = field.repeaterType ?? 'text';
	const subLabel = field.subLabel || field.label;

	function update( nextItems: FieldValue[] ) {
		onChange( nextItems );
	}

	function addItem() {
		update( [ ...items, defaultForType( subType ) ] );
	}

	function removeItem( index: number ) {
		update( items.filter( ( _, i ) => i !== index ) );
	}

	function changeItem( index: number, val: FieldValue ) {
		const next = [ ...items ];
		next[ index ] = val;
		update( next );
	}

	return createElement(
		'div',
		{ className: 'by40q-repeater' },
		// Label
		createElement( 'label', { className: 'by40q-repeater__label components-base-control__label' }, field.label ),
		// Items
		...items.map( ( item, index ) => {
			const subField: FieldDefinition = {
				...field,
				key:     field.key + '_' + index,
				label:   subLabel + ' ' + ( index + 1 ),
				type:    subType as FieldDefinition['type'],
				value:   item,
				default: defaultForType( subType ),
			};
			return createElement(
				'div',
				{ key: index, className: 'by40q-repeater__item' },
				renderSubField( subType, subField, item, ( val ) => changeItem( index, val ) ),
				createElement(
					Button,
					{
						variant:       'link',
						isDestructive: true,
						className:     'by40q-repeater__remove',
						onClick:       () => removeItem( index ),
					},
					'Remove'
				)
			);
		} ),
		// Add button
		createElement(
			Button,
			{
				variant:   'secondary',
				className: 'by40q-repeater__add',
				onClick:   addItem,
			},
			'+ Add item'
		)
	);
}
