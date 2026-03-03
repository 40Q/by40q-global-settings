/**
 * Type declarations for globals injected by PHP via wp_localize_script.
 */

// Allow importing SCSS files as side-effects (e.g. `import './settings.scss'`).
declare module '*.scss' {}
declare module '*.css' {}

declare global {
	interface Window {
		by40qGlobalSettings: {
			restUrl: string;
			nonce: string;
			/** Page context — 'main' for the primary page, or the submenu key. */
			page: string;
		};
	}
}

export {};
