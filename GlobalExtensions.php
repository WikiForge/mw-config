<?php

wfLoadSkin( 'Vector' );

wfLoadExtensions( [
	'AWS',
	'AbuseFilter',
	'AntiSpoof',
	'BetaFeatures',
	'CreateWiki',
	'CookieWarning',
	'ConfirmEdit',
	'ConfirmEdit/hCaptcha',
	'DataDump',
	'DiscordNotifications',
	'DismissableSiteNotice',
	'Echo',
	'Interwiki',
	'InterwikiDispatcher',
	'LoginNotify',
	'ManageWiki',
	'NativeSvgHandler',
	'Nuke',
	'OATHAuth',
	'OAuth',
	'ParserFunctions',
	'QuickInstantCommons',
	'Renameuser',
	'Scribunto',
	'TorBlock',
	'WebAuthn',
	'WikiDiscover',
	'WikiEditor',
	'cldr',
] );

if ( $wi->wikifarm === 'wikitide' || self::getWikiFarm() === 'nexttide' ) {
	wfLoadExtensions( [
		'CentralNotice',
		'CheckUser',
		'EventLogging',
		// 'IPInfo',
		'WikiTideMagic',
	] );
} else {
	wfLoadExtension( 'WikiForgeMagic' );
}
