<?php

wfLoadSkin( 'Vector' );

wfLoadExtensions( [
	'AWS',
	'AbuseFilter',
	'AntiSpoof',
	'CheckUser',
	'CreateWiki',
	'CookieWarning',
	'ConfirmEdit',
	'ConfirmEdit/hCaptcha',
	'DataDump',
	'Echo',
	'GlobalNewFiles',
	'IPInfo',
	'ManageWiki',
	'OATHAuth',
	'OAuth',
	'Renameuser',
	'WebAuthn',
	'WikiDiscover',
] );

if ( $wi->wikifarm === 'wikitide' ) {
	wfLoadExtensions( [
		'CentralNotice',
		'EventLogging',
		'WikiTideMagic',
	] );
} else {
	wfLoadExtension( 'WikiForgeMagic' );
}
