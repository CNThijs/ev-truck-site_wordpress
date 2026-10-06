<?php
/**
 * Forms: contact form and trend report request. Storage of submissions, the report codes, the handler, emails and
 * privacy hooks. See docs/integrations.md.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/forms/settings.php';
require_once __DIR__ . '/forms/storage.php';
require_once __DIR__ . '/forms/codes.php';
require_once __DIR__ . '/forms/mail.php';
require_once __DIR__ . '/forms/handler.php';
require_once __DIR__ . '/forms/render.php';
require_once __DIR__ . '/forms/privacy.php';
require_once __DIR__ . '/forms/download.php';
