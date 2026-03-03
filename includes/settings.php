<?php
/**
 * Global Settings — loader.
 *
 * This file requires each tab's partial from the settings/ subdirectory.
 * To add a new tab: create includes/settings/my-tab.php and require it here.
 *
 * Available field types:
 *   text | textarea | richtext | toggle | image | url | select
 *
 * @see includes/class-field-registry.php for full parameter docs.
 * @package By40Q\GlobalSettings
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/settings/contact.php';
require_once __DIR__ . '/settings/ai.php';
require_once __DIR__ . '/settings/seo.php';
require_once __DIR__ . '/settings/sample.php';
