<?php

return [
    'client_id' => env('CLIENT_ID', ''),
    'client_secret' => env('CLIENT_SECRET', ''),
    'nde_url' => env('NDE_URL', ''),
    'test_username' => env('TEST_USERNAME',''),
    'test_password' => env('TEST_PASSWORD', ''),
    'http_verify' => env('HTTP_VERIFY', true),
    'nde_support_email' => env('NDE_SUPPORT_EMAIL', 'support@neubus.com'),
    'MEDIA_URL' => env('MEDIA_URL', ''),
    'FS_URL' => env('FS_URL', ''),
    'DOCUVIEW_URL' => env('DOCUVIEW_URL', ''),

    /*
	 * The URL/path of the shared encryption key.
	 *
	 * This value will be used only if `shared_key` is empty.
	 */
    'shared_key_url' => env('SHARED_KEY_URL', ''),

	/*
	 * The shared encryption key as either a plain string or base64 encoded value (use "base64:"
	 * prefix, like "app.key").
	 *
	 * This value will be used over `shared_key_url`, if not empty.
	 */
    'shared_key' => env('SHARED_KEY', ''),

    /*
	 * Name of the cookie where tokens are stored.
	 */
    'shared_cookie_name' => env('SHARED_KEY_COOKIE_NAME', 'ncore_auth'),

    'STARRS_URL' => env('STARRS_URL', ''),

    'GOOGLE_RECAPTCHA_KEY' => env('GOOGLE_RECAPTCHA_KEY', '')
];
