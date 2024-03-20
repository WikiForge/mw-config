<?php

wfLoadSkin( 'Vector' );

wfLoadExtensions( [
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
	'ImportDump',
	'Interwiki',
	'InterwikiDispatcher',
	'LoginNotify',
	'ManageWiki',
	'MessageCachePerformance',
	'NativeSvgHandler',
	'Nuke',
	'OATHAuth',
	'OAuth',
	'ParserFunctions',
	// Remove once Parsoid migration is done
	'ParserMigration',
	'QuickInstantCommons',
	'Scribunto',
	'TorBlock',
	'WebAuthn',
	'WikiDiscover',
	'WikiEditor',
	'WikiForgeMagic',
	'cldr',
] );
