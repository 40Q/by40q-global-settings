/**
 * TypeScript definitions for the Global Settings React app.
 */

export type FieldType =
	| 'text'
	| 'textarea'
	| 'richtext'
	| 'toggle'
	| 'image'
	| 'url'
	| 'select'
	| 'repeater';

export type FieldValue = string | boolean | number | null | FieldValue[];

export interface SelectChoice {
	label: string;
	value: string;
}

export interface FieldDefinition {
	key: string;
	label: string;
	type: FieldType;
	tab: string;
	default: FieldValue;
	description: string;
	choices: SelectChoice[];
	value: FieldValue;
	/** HTML input type hint for text fields (e.g. 'email', 'tel', 'number'). */
	inputType?: 'text' | 'email' | 'tel' | 'url' | 'number' | 'password' | 'search' | 'date' | 'datetime-local' | 'time';
	/** Sub-field type for repeater fields. */
	repeaterType?: string;
	/** Label for a single repeater item. */
	subLabel?: string;
	/** Disable shortcode registration for this field. */
	disable_shortcode?: boolean;
}

export interface TabDefinition {
	key: string;
	label: string;
	order: number;
	fields: FieldDefinition[];
}

export type SettingsSchema = TabDefinition[];

/** Flat map of field key → current value, used for the form state. */
export type FieldValues = Record<string, FieldValue>;

/** Shortcode configuration for a single text field (managed in the dashboard). */
export interface ShortcodeSetting {
	enabled: boolean;
	slug: string;
}

/** Map of text field key → shortcode setting. */
export type ShortcodeSettings = Record<string, ShortcodeSetting>;

/** Shape of the REST GET response. */
export interface GetSettingsResponse {
	schema: SettingsSchema;
	shortcodes: ShortcodeSettings;
}

/** Shape of the REST POST request body. */
export interface SaveSettingsRequest {
	values: FieldValues;
	shortcodes?: ShortcodeSettings;
}

/** Shape of the REST POST response. */
export interface SaveSettingsResponse {
	success: boolean;
	saved: FieldValues;
}
