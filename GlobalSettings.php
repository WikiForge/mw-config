<?php

header( 'X-Wiki-Visibility: ' . ( $cwPrivate ? 'Private' : 'Public' ) );

// Determines what farm a wiki is on
header( 'X-Wiki-Farm: ' . $wi->wikifarm );

if ( $wi->wikifarm !== 'wikitide' ) {
	$wgSpecialPages['RequestWiki'] = WikiForge\WikiForgeMagic\Specials\SpecialRequestPremiumWiki::class;
	$wgSpecialPages['RequestWikiQueue'] = WikiForge\WikiForgeMagic\Specials\SpecialRequestPremiumWikiQueue::class;
}

// Extensions
if ( $wi->wikifarm === 'wikitide' && $wi->dbname !== 'votewikitide' ) {
	wfLoadExtensions( [
		'CentralAuth',
		'GlobalCssJs',
		'GlobalBlocking',
		'GlobalNewFiles',
		'GlobalPreferences',
	] );

	$wgMWOAuthSharedUserSource = 'CentralAuth';
	$wgOATHAuthDatabase = $wi::GLOBAL_DATABASE[$wi->wikifarm];
}

if ( $wi->wikifarm === 'wikiforge' && ( $wgWikiForgeEnableCheckUser ?? false ) ) {
	wfLoadExtensions( [
		'CheckUser',
		'IPInfo',
	] );

	$wgManageWikiPermissionsAdditionalRights['checkuser'] = [
		'abusefilter-privatedetails' => true,
		'abusefilter-privatedetails-log' => true,
		'checkuser' => true,
		'checkuser-log' => true,
		'checkuser-temporary-account' => true,
		'checkuser-temporary-account-log' => true,
	];

	if ( $wi->isExtensionActive( 'Moderation' ) ) {
		$wgManageWikiPermissionsAdditionalRights['checkuser'] += [ 'moderation-checkuser' => true ];
	}

	if ( $wi->isExtensionActive( 'SocialProfile' ) ) {
		$wgManageWikiPermissionsAdditionalRights['checkuser'] += [ 'editothersprofiles-private' => true ];
	}

	if ( $wi->isExtensionActive( 'SecurePoll' ) ) {
		$wgManageWikiPermissionsAdditionalRights['checkuser'] += [ 'securepoll-view-voter-pii' => true ];
	}

	$wgManageWikiPermissionsAdditionalAddGroupsSelf['bureaucrat'][] = 'checkuser';
	$wgManageWikiPermissionsAdditionalRemoveGroupsSelf['bureaucrat'][] = 'checkuser';
}

if ( $wi->isExtensionActive( 'chameleon' ) ) {
	wfLoadExtension( 'Bootstrap' );
}

if ( $wi->isExtensionActive( 'CirrusSearch' ) ) {
	wfLoadExtension( 'Elastica' );
	$wgSearchType = 'CirrusSearch';
	$wgCirrusSearchClusters = [
		'default' => [
			[
				'host' => 'search-elasticsearch-jtuqyxjkhmon354q2p2w2rvdwa.us-east-2.es.amazonaws.com',
				'port' => 80,
			],
		],
	];

	if ( $wi->isExtensionActive( 'RelatedArticles' ) ) {
		$wgRelatedArticlesUseCirrusSearch = true;
	}
}

if ( $wi->isExtensionActive( 'StandardDialogs' ) ) {
	wfLoadExtension( 'OOJSPlus' );
}

if ( $wi->isAnyOfExtensionsActive( 'Email Authorization', 'OpenID Connect', 'SimpleSAMLphp', 'WSOAuth' ) ) {
	wfLoadExtension( 'PluggableAuth' );
}

if ( ( ( $wgWikiTideCommons ?? false ) || ( $wgWikiForgeCommons ?? false ) ) && !$cwPrivate ) {
	wfLoadExtension( 'GlobalUsage' );
}

if ( $wi->wikifarm !== 'wikitide' && $wi->isAnyOfExtensionsActive( 'SearchVue', 'Upload Wizard' ) ) {
	wfLoadExtension( 'EventLogging' );
}

if ( $wi->isExtensionActive( 'InterwikiSorting' ) ) {
	$wgInterwikiSortingInterwikiSortOrders = include __DIR__ . '/InterwikiSortOrders.php';
}

if ( $wi->isAllOfExtensionsActive( '3d', 'MultimediaViewer' ) ) {
	$wgMediaViewerExtensions['stl'] = 'mmv.3d';
}

if ( $wi->isExtensionActive( 'Popups' ) ) {
	if ( $wmgShowPopupsByDefault ) {
		$wgPopupsHideOptInOnPreferencesPage = true;
		$wgPopupsOptInDefaultState = '1';
		$wgPopupsOptInStateForNewAccounts = '1';
		$wgPopupsReferencePreviewsBetaFeature = false;
	}
}

if ( $wi->isExtensionActive( 'SemanticMediaWiki' ) ) {
	require_once '/srv/mediawiki/config/SemanticMediaWiki.php';
}

if ( $wi->isExtensionActive( 'SocialProfile' ) ) {
	require_once "$IP/extensions/SocialProfile/SocialProfile.php";

	$wgSocialProfileFileBackend = 'AmazonS3';
}

if ( $wi->isExtensionActive( 'VisualEditor' ) ) {
	if ( $wmgVisualEditorEnableDefault ) {
		$wgDefaultUserOptions['visualeditor-enable'] = 1;
		$wgDefaultUserOptions['visualeditor-editor'] = 'visualeditor';
	} else {
		$wgDefaultUserOptions['visualeditor-enable'] = 0;
	}
}

if ( $wi->isAnyOfExtensionsActive( 'WikibaseClient', 'WikibaseRepository' ) ) {
	// Includes Wikibase Configuration. There is a global and per-wiki system here.
	require_once '/srv/mediawiki/config/Wikibase.php';
}

// If Flow, VisualEditor, or Linter is used, use the Parsoid php extension
if ( $wi->version >= 1.40 || $wi->isAnyOfExtensionsActive( 'Flow', 'VisualEditor', 'Linter' ) ) {
	wfLoadExtension( 'Parsoid', "$IP/vendor/wikimedia/parsoid/extension.json" );

	if ( $wi->isExtensionActive( 'VisualEditor' ) ) {
		$wgVisualEditorParsoidAutoConfig = false;
	}

	$wgVirtualRestConfig = [
		'paths' => [],
		'modules' => [
			'parsoid' => [
				'url' => 'https://mw-lb.wikiforge.net' . $wgRestPath,
				'domain' => $wi->server,
				'prefix' => $wi->dbname,
				'forwardCookies' => (bool)$cwPrivate,
				'restbaseCompat' => false,
				'timeout' => 30,
			],
		],
		'global' => [
			'timeout' => 360,
			'forwardCookies' => false,
			'HTTPProxy' => null,
		],
	];

	if ( $wi->isExtensionActive( 'Flow' ) ) {
		$wgFlowParsoidURL = 'https://mw-lb.wikiforge.net' . $wgRestPath;
		$wgFlowParsoidPrefix = $wi->dbname;
		$wgFlowParsoidTimeout = 30;
		$wgFlowParsoidForwardCookies = (bool)$cwPrivate;
	}
}

// Temporary to fix issue with uploading these
$wgHooks['MimeMagicInit'][] = static function ( MimeAnalyzer $mime ) {
	$mime->addExtraTypes( 'font/sfnt ttf' );
	$mime->addExtraTypes( 'font/woff woff' );
	$mime->addExtraTypes( 'font/woff2 woff2' );
};

// Expose $wgDBname to page HTML for WikiForgeDebugJS
$wgHooks['MakeGlobalVariablesScript'][] = static function ( &$vars, $out ): void {
	$vars['wgDBname'] = $out->getConfig()->get( 'DBname' );
};

// Action and article paths
$articlePath = str_replace( '$1', '', $wgArticlePath );

$wgDiscordNotificationWikiUrl = $wi->server . $articlePath;
$wgDiscordNotificationWikiUrlEnding = '';
$wgDiscordNotificationWikiUrlEndingDeleteArticle = '?action=delete';
$wgDiscordNotificationWikiUrlEndingDiff = '?diff=prev&oldid=';
$wgDiscordNotificationWikiUrlEndingEditArticle = '?action=edit';
$wgDiscordNotificationWikiUrlEndingHistory = '?action=history';
$wgDiscordNotificationWikiUrlEndingUserRights = 'Special:UserRights?user=';

/** TODO:
 * Add to ManageWiki (core)
 * Add rewrites to decode.php and index.php
 */
$wgActionPaths['view'] = $wgArticlePath;

// ?action=raw is not supported by this
// according to documentation
$actions = [
	'delete',
	'edit',
	'history',
	'info',
	'markpatrolled',
	'protect',
	'purge',
	'render',
	'revert',
	'rollback',
	'submit',
	'unprotect',
	'unwatch',
	'watch',
];

foreach ( $actions as $action ) {
	$wgActionPaths[$action] = $wgArticlePath . '?action=' . $action;
}

if ( ( $wgWikiForgeActionPathsFormat ?? 'default' ) !== 'default' ) {
	switch ( $wgWikiForgeActionPathsFormat ) {
		case 'specialpages':
			$wgActionPaths['edit'] = $articlePath . 'Special:EditPage/$1';
			$wgActionPaths['submit'] = $wgActionPaths['edit'];
			$wgActionPaths['delete'] = $articlePath . 'Special:DeletePage/$1';
			$wgActionPaths['protect'] = $articlePath . 'Special:ProtectPage/$1';
			$wgActionPaths['unprotect'] = $wgActionPaths['protect'];
			$wgActionPaths['history'] = $articlePath . 'Special:PageHistory/$1';
			$wgActionPaths['info'] = $articlePath . 'Special:PageInfo/$1';
			break;
		case '$1/action':
		case 'action/$1':
			foreach ( $actions as $action ) {
				$wgActionPaths[$action] = $articlePath . str_replace( 'action', $action, $wgWikiForgeActionPathsFormat );
			}

			break;
	}
}

// Don't need globals here
unset( $actions, $articlePath );

$wgAllowedCorsHeaders[] = 'X-WikiForge-Debug';

// AWS
$wgAWSCredentials = [
	'key' => $wmgAWSAccessKey,
	'secret' => $wmgAWSAccessSecretKey,
	'token' => false,
];

$wgAWSRegion = 'us-east-2';
$wgAWSBucketName = 'static.wikiforge.net';
$wgAWSBucketDomain = 'static.wikiforge.net';

$wgAWSRepoHashLevels = 2;
$wgAWSRepoDeletedHashLevels = 3;

$wgAWSBucketTopSubdirectory = '/' . $wgDBname;

// Closed Wikis
if ( $wi->wikifarm === 'wikitide' && $cwClosed ) {
	$wgRevokePermissions = [
		'*' => [
			'block' => true,
			'createaccount' => true,
			'delete' => true,
			'edit' => true,
			'protect' => true,
			'import' => true,
			'upload' => true,
			'undelete' => true,
		],
	];

	if ( $wi->isExtensionActive( 'Comments' ) ) {
		$wgRevokePermissions['*']['comment'] = true;
	}
}

// Public Wikis
if ( !$cwPrivate ) {
	/* if ( $wi->wikifarm === 'wikitide' ) {
		$wgRCFeeds['irc'] = [
			'formatter' => WikiTideIRCRCFeedFormatter::class,
			'uri' => 'udp://jobrunner1-private.wikiforge.net:5070',
			'add_interwiki_prefix' => false,
			'omit_bots' => true,
		];
	}*/

	$wgDiscordIncomingWebhookUrl = $wmgGlobalDiscordWebhookUrl;
	$wgDiscordExperimentalWebhook = $wmgDiscordExperimentalWebhook;

	$wgDataDumpDownloadUrl = "https://{$wmgUploadHostname}/{$wi->dbname}/dumps/\${filename}";
}

// Dynamic cookie settings dependant on $wgServer
if ( preg_match( '/wikiforge\.net$/', $wi->server ) ) {
	$wgCentralAuthCookieDomain = '.wikiforge.net';
	$wgMFStopRedirectCookieHost = '.wikiforge.net';
} elseif ( preg_match( '/wikitide\.com$/', $wi->server ) ) {
	$wgCentralAuthCookieDomain = '.wikitide.com';
	$wgMFStopRedirectCookieHost = '.wikitide.com';
} else {
	$wgCentralAuthCookieDomain = $wi->hostname;
	$wgMFStopRedirectCookieHost = $wi->hostname;
}

// DataDump
$wgDataDumpFileBackend = 'AmazonS3';

$wgDataDump = [
	'xml' => [
		'file_ending' => '.xml.gz',
		'useBackendTempStore' => true,
		'generate' => [
			'type' => 'mwscript',
			'script' => "$IP/maintenance/dumpBackup.php",
			'options' => [
				'--full',
				'--logs',
				'--uploads',
				'--output',
				'gzip:/tmp/${filename}',
			],
			'arguments' => [
				'--namespaces'
			],
		],
		'limit' => 1,
		'permissions' => [
			'view' => 'view-dump',
			'generate' => 'generate-dump',
			'delete' => 'delete-dump',
		],
		'htmlform' => [
			'name' => 'namespaceselect',
			'type' => 'namespaceselect',
			'exists' => true,
			'noArgsValue' => 'all',
			'hide-if' => [ '!==', 'generatedumptype', 'xml' ],
			'label-message' => 'datadump-namespaceselect-label'
		],
	],
	'image' => [
		'file_ending' => '.tar.gz',
		'useBackendTempStore' => true,
		'generate' => [
			'type' => 'mwscript',
			'script' => "$IP/extensions/" . ( $wi->wikifarm === 'wikitide' ? 'WikiTideMagic' : 'WikiForgeMagic' ) . '/maintenance/generateS3Backup.php',
			'options' => [
				'--filename',
				'${filename}'
			],
		],
		'limit' => 1,
		'permissions' => [
			'view' => 'view-dump',
			'generate' => 'managewiki-restricted',
			'delete' => 'delete-dump',
		],
	],
	'managewiki_backup' => [
		'file_ending' => '.json',
		'generate' => [
			'type' => 'mwscript',
			'script' => "$IP/extensions/" . ( $wi->wikifarm === 'wikitide' ? 'WikiTideMagic' : 'WikiForgeMagic' ) . '/maintenance/generateManageWikiBackup.php',
			'options' => [
				'--filename',
				'${filename}'
			],
		],
		'limit' => 1,
		'permissions' => [
			'view' => 'view-dump',
			'generate' => 'generate-dump',
			'delete' => 'delete-dump',
		],
	],
];

if ( $wi->isExtensionActive( 'Flow' ) ) {
	$wgDataDump['flow'] = [
		'file_ending' => '.xml.gz',
		'useBackendTempStore' => true,
		'generate' => [
			'type' => 'mwscript',
			'script' => "$IP/extensions/Flow/maintenance/dumpBackup.php",
			'options' => [
				'--full',
				'--output',
				'gzip:/tmp/${filename}',
			],
		],
		'limit' => 1,
		'permissions' => [
			'view' => 'view-dump',
			'generate' => 'generate-dump',
			'delete' => 'delete-dump',
		],
	];
}

// ContactPage configuration
if ( $wi->isExtensionActive( 'ContactPage' ) ) {
	$wgContactConfig = [
		'default' => [
			'RecipientUser' => $wmgContactPageRecipientUser ?? null,
			'SenderEmail' => $wgPasswordSender,
			'SenderName' => ( $wi->wikifarm === 'wikitide' ? 'WikiTide' : 'WikiForge' ) . ' No Reply',
			'RequireDetails' => true,
			// Should never be set to true
			'IncludeIP' => false,
			'MustBeLoggedIn' => false,
			'AdditionalFields' => [
				'Text' => [
					'label-message' => 'emailmessage',
					'type' => 'textarea',
					'rows' => 20,
					'required' => true,
				],
			],
			'DisplayFormat' => 'table',
			'RLModules' => [],
			'RLStyleModules' => [],
		],
	];
}

// UploadWizard configuration
if ( $wi->isExtensionActive( 'UploadWizard' ) ) {
	$wgUploadWizardConfig = [
		'campaignExpensiveStatsEnabled' => false,
		'flickrApiKey' => $wmgUploadWizardFlickrApiKey,
	];
}

if ( $wi->isExtensionActive( 'Score' ) ) {
	$wgScoreFileBackend = 'AmazonS3';
}

if ( $wi->isExtensionActive( 'EasyTimeline' ) ) {
	$wgTimelineFileBackend = 'AmazonS3';
}

// $wgFooterIcons
if ( (bool)$wmgWikiapiaryFooterPageName ) {
	$wgFooterIcons['poweredby']['wikiapiary'] = [
		'src' => 'https://static.wikiforge.net/commonswiki/b/b4/Monitored_by_WikiApiary.png',
		'url' => 'https://wikiapiary.com/wiki/' . str_replace( ' ', '_', $wmgWikiapiaryFooterPageName ),
		'alt' => 'Monitored by WikiApiary'
	];
}

// $wgLocalFileRepo
$wgGenerateThumbnailOnParse = false;

$wgThumbnailBuckets = [ 1920 ];
$wgThumbnailMinimumBucketDistance = 100;

// Thumbnail prerendering at upload time
$wgUploadThumbnailRenderMap = [ 320, 640, 800, 1024, 1280, 1920 ];

if ( $cwPrivate ) {
	$wgUploadThumbnailRenderMap = [];
	$wgUploadPath = '/w/img_auth.php';
}

$wgLocalFileRepo = [
	'class' => LocalRepo::class,
	'name' => 'local',
	'backend' => 'AmazonS3',
	'url' => $wgUploadBaseUrl ? $wgUploadBaseUrl . $wgUploadPath : $wgUploadPath,
	'scriptDirUrl' => $wgScriptPath,
	'hashLevels' => 2,
	'thumbScriptUrl' => $wgScriptPath . '/thumb.php',
	'transformVia404' => true,
	'disableLocalTransform' => true,
	'useJsonMetadata'   => true,
	'useSplitMetadata'  => true,
	'deletedHashLevels' => 3,
	'abbrvThreshold' => 160,
	'isPrivate' => $cwPrivate,
	'zones' => $cwPrivate
		? [
			'thumb' => [ 'url' => '/w/thumb_handler.php' ] ]
		: [],
];

// $wgForeignFileRepos
if ( $wmgEnableSharedUploads && $wmgSharedUploadDBname && in_array( $wmgSharedUploadDBname, $wgLocalDatabases ) ) {
	if ( !$wmgSharedUploadBaseUrl || $wmgSharedUploadBaseUrl === $wmgSharedUploadDBname ) {
		$wmgSharedUploadSubdomain = substr( $wmgSharedUploadDBname, 0, -4 );

		$wmgSharedUploadBaseUrl = "{$wmgSharedUploadSubdomain}.{$wgCreateWikiSubdomain}";
	}

	$wgForeignFileRepos[] = [
		'class' => ForeignDBViaLBRepo::class,
		'name' => "shared-{$wmgSharedUploadDBname}",
		'backend' => 'AmazonS3',
		'url' => "https://static.wikiforge.net/{$wmgSharedUploadDBname}",
		'hashLevels' => 2,
		'thumbScriptUrl' => false,
		'transformVia404' => true,
		'hasSharedCache' => true,
		'descBaseUrl' => "https://{$wmgSharedUploadBaseUrl}/wiki/File:",
		'scriptDirUrl' => "https://{$wmgSharedUploadBaseUrl}/w",
		'fetchDescription' => true,
		'descriptionCacheExpiry' => 86400 * 7,
		'wiki' => $wmgSharedUploadDBname,
		'initialCapital' => true,
		'zones' => [
			'public' => [
				'container' => 'local-public',
			],
			'thumb' => [
				'container' => 'local-thumb',
			],
			'temp' => [
				'container' => 'local-temp',
			],
			'deleted' => [
				'container' => 'local-deleted',
			],
		],
		'abbrvThreshold' => 160
	];
}

// WikiForge Commons
if ( $wi->wikifarm === 'wikiforge' && ( $wgDBname !== 'commonswiki' && $wgWikiForgeCommons ?? false ) ) {
	$wgForeignFileRepos[] = [
		'class' => ForeignDBViaLBRepo::class,
		'name' => 'wikiforgecommons',
		'backend' => 'AmazonS3',
		'url' => 'https://static.wikiforge.net/commonswiki',
		'hashLevels' => 2,
		'thumbScriptUrl' => false,
		'transformVia404' => true,
		'hasSharedCache' => true,
		'descBaseUrl' => 'https://commons.wikiforge.net/wiki/File:',
		'scriptDirUrl' => 'https://commons.wikiforge.net/w',
		'fetchDescription' => true,
		'descriptionCacheExpiry' => 86400 * 7,
		'wiki' => 'commonswiki',
		'initialCapital' => true,
		'zones' => [
			'public' => [
				'container' => 'local-public',
			],
			'thumb' => [
				'container' => 'local-thumb',
			],
			'temp' => [
				'container' => 'local-temp',
			],
			'deleted' => [
				'container' => 'local-deleted',
			],
		],
		'abbrvThreshold' => 160
	];
}

// WikiTide Commons
if ( $wi->wikifarm === 'wikitide' && ( $wgDBname !== 'commonswikitide' && $wgWikiTideCommons ?? false ) ) {
	$wgForeignFileRepos[] = [
		'class' => ForeignDBViaLBRepo::class,
		'name' => 'wikitidecommons',
		'backend' => 'AmazonS3',
		'url' => 'https://static.wikiforge.net/commonswikitide',
		'hashLevels' => 2,
		'thumbScriptUrl' => false,
		'transformVia404' => true,
		'hasSharedCache' => true,
		'descBaseUrl' => 'https://commons.wikitide.com/wiki/File:',
		'scriptDirUrl' => 'https://commons.wikitide.com/w',
		'fetchDescription' => true,
		'descriptionCacheExpiry' => 86400 * 7,
		'wiki' => 'commonswikitide',
		'initialCapital' => true,
		'zones' => [
			'public' => [
				'container' => 'local-public',
			],
			'thumb' => [
				'container' => 'local-thumb',
			],
			'temp' => [
				'container' => 'local-temp',
			],
			'deleted' => [
				'container' => 'local-deleted',
			],
		],
		'abbrvThreshold' => 160
	];
}

// $wgLogos
$wgLogos = [
	'1x' => $wgLogo,
];

$wgApexLogo = [
	'1x' => $wgLogos['1x'],
	'2x' => $wgLogos['1x'],
];

if ( $wgIcon ) {
	$wgLogos['icon'] = $wgIcon;
}

if ( $wgWordmark ) {
	$wgLogos['wordmark'] = [
		'src' => $wgWordmark,
		'width' => (int)$wgWordmarkWidth,
		'height' => (int)$wgWordmarkHeight,
	];
}

// $wgUrlShortenerAllowedDomains
$wgUrlShortenerAllowedDomains = [
	'(.*\.)?wikiforge\.net',
	'(.*\.)?wikitide\.com',
];

if (
	!preg_match( '/^(.*).wikiforge.net$/', $wi->hostname ) &&
	!preg_match( '/^(.*).wikitide.com$/', $wi->hostname )
) {
	$wgUrlShortenerAllowedDomains = array_merge(
		$wgUrlShortenerAllowedDomains,
		[ preg_quote( str_replace( 'https://', '', $wgServer ) ) ]
	);
}

// JsonConfig
if ( $wi->isExtensionActive( 'JsonConfig' ) ) {
	$wgJsonConfigs = [
		'Map.JsonConfig' => [
			'namespace' => 486,
			'nsName' => 'Data',
			// page name must end in ".map", and contain at least one symbol
			'pattern' => '/.\.map$/',
			'license' => 'CC-BY-SA 4.0',
			'isLocal' => false,
		],
		'Tabular.JsonConfig' => [
			'namespace' => 486,
			'nsName' => 'Data',
			// page name must end in ".tab", and contain at least one symbol
			'pattern' => '/.\.tab$/',
			'license' => 'CC-BY-SA 4.0',
			'isLocal' => false,
		],
	];

	if ( $wi->wikifarm === 'wikiforge' && $wgDBname !== 'commonswiki' ) {
		$wgJsonConfigs['Map.JsonConfig']['remote'] = [
			'url' => 'https://commons.wikiforge.net/w/api.php'
		];

		$wgJsonConfigs['Tabular.JsonConfig']['remote'] = [
			'url' => 'https://commons.wikiforge.net/w/api.php'
		];
	}

	if ( $wi->wikifarm === 'wikitide' && $wgDBname !== 'commonswikitide' ) {
		$wgJsonConfigs['Map.JsonConfig']['remote'] = [
			'url' => 'https://commons.wikitide.com/w/api.php'
		];

		$wgJsonConfigs['Tabular.JsonConfig']['remote'] = [
			'url' => 'https://commons.wikitide.com/w/api.php'
		];
	}
}

// Vector
$vectorVersion = $wgDefaultSkin === 'vector-2022' ? '2' : '1';
$wgVectorDefaultSkinVersionForExistingAccounts = $vectorVersion;

// Don't need a global here
unset( $vectorVersion );

// Licensing variables

$version = $wi->version;

// Alpha is only available on the test server,
// use beta (or stable if there currently is no beta)
// for foreign metawiki links if the version is alpha.
if ( $wi->version === WikiForgeFunctions::MEDIAWIKI_VERSIONS['alpha'] ) {
	$version = WikiForgeFunctions::MEDIAWIKI_VERSIONS['beta'] ??
		WikiForgeFunctions::MEDIAWIKI_VERSIONS['stable'];
}

/**
 * Default values.
 * We can not set these in LocalSettings.php, to prevent them
 * from causing absolute overrides.
 */
$wgRightsIcon = 'https://meta.wikiforge.net/' . $version . '/resources/assets/licenses/cc-by-sa.png';
$wgRightsText = 'Creative Commons Attribution Share Alike';
$wgRightsUrl = 'https://creativecommons.org/licenses/by-sa/4.0/';

/**
 * Override values from ManageWiki.
 * If set in LocalSettings.php, this will be overridden
 * by wiki values there, due to caching forcing SiteConfiguration
 * values to be absolute overrides. This is however how licensing should
 * be forced. LocalSettings.php values should take priority, which they do.
 */
switch ( $wmgWikiLicense ) {
	case 'arr':
		$wgRightsIcon = 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/67/License_icon-copyright-88x31.svg/88px-License_icon-copyright-88x31.svg.png';
		$wgRightsText = 'All Rights Reserved';
		$wgRightsUrl = false;
		break;
	case 'cc-by':
		$wgRightsIcon = 'https://meta.wikiforge.net/' . $version . '/resources/assets/licenses/cc-by.png';
		$wgRightsText = 'Creative Commons Attribution 4.0 International (CC BY 4.0)';
		$wgRightsUrl = 'https://creativecommons.org/licenses/by/4.0';
		break;
	case 'cc-by-nc':
		$wgRightsIcon = 'https://mirrors.creativecommons.org/presskit/buttons/88x31/png/by-nc.png';
		$wgRightsText = 'Creative Commons Attribution-NonCommercial 4.0 International (CC BY-NC 4.0)';
		$wgRightsUrl = 'https://creativecommons.org/licenses/by-nc/4.0/';
		break;
	case 'cc-by-nd':
		$wgRightsIcon = 'https://mirrors.creativecommons.org/presskit/buttons/88x31/png/by-nd.png';
		$wgRightsText = 'Creative Commons Attribution-NoDerivatives 4.0 International (CC BY-ND 4.0)';
		$wgRightsUrl = 'https://creativecommons.org/licenses/by-nd/4.0/';
		break;
	case 'cc-by-sa':
		$wgRightsIcon = 'https://meta.wikiforge.net/' . $version . '/resources/assets/licenses/cc-by-sa.png';
		$wgRightsText = 'Creative Commons Attribution-ShareAlike 4.0 International (CC BY-SA 4.0)';
		$wgRightsUrl = 'https://creativecommons.org/licenses/by-sa/4.0/';
		break;
	case 'cc-by-sa-2-0-kr':
		$wgRightsIcon = 'https://meta.wikiforge.net/' . $version . '/resources/assets/licenses/cc-by-sa.png';
		$wgRightsText = 'Creative Commons BY-SA 2.0 Korea';
		$wgRightsUrl = 'https://creativecommons.org/licenses/by-sa/2.0/kr';
		break;
	case 'cc-by-sa-nc':
		$wgRightsIcon = 'https://meta.wikiforge.net/' . $version . '/resources/assets/licenses/cc-by-nc-sa.png';
		$wgRightsText = 'Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International (CC BY-NC-SA 4.0)';
		$wgRightsUrl = 'https://creativecommons.org/licenses/by-nc-sa/4.0/';
		break;
	case 'cc-by-nc-nd':
		$wgRightsIcon = 'https://mirrors.creativecommons.org/presskit/buttons/88x31/png/by-nc-nd.png';
		$wgRightsText = 'Creative Commons Attribution-NonCommercial-NoDerivatives 4.0 International (CC BY-NC-ND 4.0)';
		$wgRightsUrl = 'https://creativecommons.org/licenses/by-nc-nd/4.0/';
		break;
	case 'cc-pd':
		$wgRightsIcon = 'https://meta.wikiforge.net/' . $version . '/resources/assets/licenses/cc-0.png';
		$wgRightsText = 'CC0 Public Domain';
		$wgRightsUrl = 'https://creativecommons.org/publicdomain/zero/1.0/';
		break;
	case 'gpl-v3':
		$wgRightsIcon = 'https://www.gnu.org/graphics/gplv3-or-later.png';
		$wgRightsText = 'GPLv3';
		$wgRightsUrl = 'https://www.gnu.org/licenses/gpl-3.0-standalone.html';
		break;
	case 'gfdl':
		$wgRightsIcon = 'https://www.gnu.org/graphics/gfdl-logo-tiny.png';
		$wgRightsText = 'GNU Free Document License 1.3';
		$wgRightsUrl = 'https://www.gnu.org/licenses/fdl-1.3.en.html';
		break;
	case 'empty':
		break;
}

// Don't need a global here
unset( $version );

/**
 * Make sure it works to override the footer icon
 * for other overrides in LocalSettings.php.
 */
if ( $wgConf->get( 'wgRightsIcon', $wi->dbname ) ) {
	$wgFooterIcons['copyright']['copyright'] = [
		'url' => $wgConf->get( 'wgRightsUrl', $wi->dbname ),
		'src' => $wgConf->get( 'wgRightsIcon', $wi->dbname ),
		'alt' => $wgConf->get( 'wgRightsText', $wi->dbname ),
	];
}

// Kilobytes
$wgMaxShellFileSize = 512 * 1024;
$wgMaxShellMemory = 1024 * 1024;

// 50 seconds
$wgMaxShellTime = 50;

$wgShellCgroup = '/sys/fs/cgroup/memory/mediawiki/job';

$wgJobRunRate = 0;
$wgSVGConverters['inkscape'] = '$path/inkscape -w $width -o $output $input';

// Scribunto
/** 50MB */
$wgScribuntoEngineConf['luasandbox']['memoryLimit'] = 50 * 1024 * 1024;
$wgScribuntoEngineConf['luasandbox']['cpuLimit'] = 10;
