<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Third Party Services
	|--------------------------------------------------------------------------
	|
	| This file is for storing the credentials for third party services such
	| as Mailgun, Postmark, AWS and more. This file provides the de facto
	| location for this type of information, allowing packages to have
	| a conventional file to locate the various service credentials.
	|
	*/
	'postmark' => [
		'token' => env('POSTMARK_TOKEN'),
	],

	'ses' => [
		'key' => env('AWS_ACCESS_KEY_ID'),
		'secret' => env('AWS_SECRET_ACCESS_KEY'),
		'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
	],

	'slack' => [
		'notifications' => [
			'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
			'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
		],
	],

	'gnugrid' => [
		'key' => env('GNUGRID_KEY'),
		'secret' => env('GNUGRID_SECRET'),
		'url' => env('GNUGRID_URL'),
	],

	'partners' => [],

	'sms' => [
		'DMARK_PROD' => [
			'url' => env('DMARK_ENDPOINT'),
			'spname' => env('DMARK_USERNAME'),
			'sppass' => env('DMARK_PASSWORD', 'us-east-1'),
		],
		'DMARK_TEST' => [
			'url' => env('DMARK_TEST_ENDPOINT'),
			'spname' => env('DMARK_TEST_USERNAME'),
			'sppass' => env('DMARK_TEST_PASSWORD', 'us-east-1'),
		],
		'MTECH_PROD' => [
			'url' => env('MTECH_ENDPOINT'),
		],
		'MTECH_TEST' => [
			'url' => env('MTECH_TEST_ENDPOINT'),
		],
		'AFRICASTALKING_PROD' => [
			'url' => env('AFRICASTALKING_ENDPOINT'),
			'spname' => env('AFRICASTALKING_USERNAME'),
			'sppass' => env('AFRICASTALKING_PASSWORD', 'us-east-1'),
			'sender_id' => env('AFRICASTALKING_SENDER_ID'),
		],
		'AFRICASTALKING_TEST' => [
			'url' => env('AFRICASTALKING_TEST_ENDPOINT'),
			'spname' => env('AFRICASTALKING_TEST_USERNAME'),
			'sppass' => env('AFRICASTALKING_TEST_PASSWORD', 'us-east-1'),
			'sender_id' => env('AFRICASTALKING_TEST_SENDER_ID'),
		],
		'ego' => [
			'url' => env('EGOSMS_BASE_URL'),
			'username' => env('EGOSMS_USERNAME'),
			'password' => env('EGOSMS_PASSWORD'),
			'sender_id' => env('EGOSMS_SENDER_ID'),
		],
		'africastalking' => [
			'url' => env('AFRICASTALKING_ENDPOINT'),
			'username' => env('AFRICASTALKING_USERNAME'),
			'password' => env('AFRICASTALKING_PASSWORD', 'us-east-1'),
		]
	],

	'airtel' => [
		'production' => [
			'url' => env('AIRTEL_PRODUCTION_URL'),
			'airtel_public_key' => env('AIRTEL_PRODUCTION_PUBLIC_KEY'),
			'key' => env('AIRTEL_PRODUCTION_CLIENT_KEY'),
			'secret' => env('AIRTEL_PRODUCTION_CLIENT_SECRET'),
			'pin' => env('AIRTEL_PRODUCTION_PIN'),
		],
		'test' => [
			'url' => env('AIRTEL_TEST_URL'),
			'airtel_public_key' => env('AIRTEL_TEST_PUBLIC_KEY'),
			'client_key' => env('AIRTEL_TEST_CLIENT_KEY'),
			'client_secret' => env('AIRTEL_TEST_CLIENT_SECRET'),
			'pin' => env('AIRTEL_TEST_PIN'),
		],
	],
	'twilio' => [
		'account' => env('TWILIO_SID'),
		'token' => env('TWILIO_AUTH_TOKEN'),
		'from' => env('TWILIO_WHATSAPP_NUMBER'),
	],
];
