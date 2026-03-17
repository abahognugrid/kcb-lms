<?php

return [
	'postmark' => [
		'token' => env('POSTMARK_TOKEN'),
	],

	'ses' => [
		'key' => env('AWS_ACCESS_KEY_ID'),
		'secret' => env('AWS_SECRET_ACCESS_KEY'),
		'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
	],

	'gnugrid' => [
		'key' => env('GNUGRID_KEY'),
		'secret' => env('GNUGRID_SECRET'),
		'url' => env('GNUGRID_URL'),
	],

	'partners' => [],
];
