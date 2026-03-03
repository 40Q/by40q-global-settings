/**
 * FieldRenderer — routes to the correct field component based on `field.type`.
 */

import { createElement } from '@wordpress/element';
import type { FieldDefinition, FieldValue, ShortcodeSettings } from '../../types';
import TextField      from './TextField';
import TextareaField  from './TextareaField';
import RichtextField  from './RichtextField';
import ToggleField    from './ToggleField';
import ImageField     from './ImageField';
import UrlField       from './UrlField';
import SelectField    from './SelectField';
import RepeaterField  from './RepeaterField';
import ShortcodeControl from './ShortcodeControl';

interface FieldRendererProps {
	field:              FieldDefinition;
	value:              FieldValue;
	onChange:           ( value: FieldValue ) => void;
	shortcodeSettings:  ShortcodeSettings;
	onShortcodeChange:  ( key: string, enabled: boolean, slug: string ) => void;
}

export default function FieldRenderer( { field, value, onChange, shortcodeSettings, onShortcodeChange }: FieldRendererProps ) {
	const wrapperClass = `by40q-field by40q-field--${ field.type }`;

	const fieldEl = ( () => {
		switch ( field.type ) {
			case 'text':
				return createElement( TextField, { field, value, onChange } );
			case 'textarea':
				return createElement( TextareaField, { field, value, onChange } );
			case 'richtext':
				return createElement( RichtextField, { field, value, onChange } );
			case 'toggle':
				return createElement( ToggleField, { field, value, onChange } );
			case 'image':
				return createElement( ImageField, { field, value, onChange } );
			case 'url':
				return createElement( UrlField, { field, value, onChange } );
			case 'select':
				return createElement( SelectField, { field, value, onChange } );
			case 'repeater':
				return createElement( RepeaterField, { field, value, onChange } );
			default:
				return createElement( TextField, { field, value, onChange } );
		}
	} )();

	return createElement(
		'div',
		{ className: wrapperClass },
		fieldEl,
		field.description && createElement(
			'p',
			{ className: 'by40q-field__description description' },
			field.description
		),
		( [ 'text', 'textarea', 'richtext', 'url' ] as string[] ).includes( field.type ) && ! field.disable_shortcode && createElement( ShortcodeControl, {
			enabled:     shortcodeSettings[ field.key ]?.enabled ?? false,
			slug:        shortcodeSettings[ field.key ]?.slug ?? '',
			defaultSlug: field.key,
			onChange:    ( enabled: boolean, slug: string ) => onShortcodeChange( field.key, enabled, slug ),
		} )
	);
}
