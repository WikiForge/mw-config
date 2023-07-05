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
	'GlobalNewFiles',
	'Interwiki',
	'InterwikiDispatcher',
	'LoginNotify',
	'ManageWiki',
	'MobileDetect',
	'NativeSvgHandler',
	'Nuke',
	'OATHAuth',
	'OAuth',
	'ParserFunctions',
	'QuickInstantCommons',
	'Renameuser',
	'RottenLinks',
	'Scribunto',
	'SpamBlacklist',
	'StopForumSpam',
	'TitleBlacklist',
	'TorBlock',
	'WebAuthn',
	'WikiDiscover',
	'WikiEditor',
	'cldr',
] );

if ( $wi->wikifarm === 'wikitide' ) {
	wfLoadExtensions( [
		'CentralNotice',
		'CheckUser',
		'EventLogging',
		'IPInfo',
		'WikiTideMagic',
	] );
} else {
	wfLoadExtension( 'WikiForgeMagic' );
}
