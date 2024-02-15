<?php

$wgFileBackends[] = [
	'class'              => SwiftFileBackend::class,
	'name'               => 'wikiforge-swift',
	// This is the prefix for the container that it starts with.
	'wikiId'             => "wikiforge-$wgDBname",
	'lockManager'        => 'redisLockManager',
	'swiftAuthUrl'       => 'https://swift-lb.inside.wf/auth',
	'swiftStorageUrl'    => 'https://swift-lb.inside.wf/v1/AUTH_mw',
	'swiftUser'          => 'mw:media',
	'swiftKey'           => $wmgSwiftPassword,
	'swiftTempUrlKey'    => '',
	'parallelize'        => 'implicit',
	'cacheAuthInfo'      => true,
	'readAffinity'       => true,
	'readUsers'           => [ 'mw:media' ],
	'writeUsers'          => [ 'mw:media' ],
	'connTimeout'         => 10,
	'reqTimeout'          => 900,
];

$wgLockManagers[] = [
	'name' => 'redisLockManager',
	'class' => RedisLockManager::class,
	'lockServers' => [
		// jobchron21
		'rdb1' => '10.0.2.8:6379',
	],
	'srvsByBucket' => [
		0 => [ 'rdb1' ]
	],
	'redisConfig' => [
		'connectTimeout' => 2,
		'readTimeout' => 2,
		'password' => $wmgRedisPassword,
	]
];

$wgGenerateThumbnailOnParse = false;
$wgUploadThumbnailRenderMethod = 'http';
$wgUploadThumbnailRenderHttpCustomHost = 'static.wikiforge.net';
$wgUploadThumbnailRenderHttpCustomDomain = 'swift-lb.wikiforge.net';

$wgThumbnailBuckets = [ 1920 ];
$wgThumbnailMinimumBucketDistance = 100;

// Thumbnail prerendering at upload time
$wgUploadThumbnailRenderMap = [ 320, 640, 800, 1024, 1280, 1920 ];

if ( $cwPrivate ) {
	$wgUploadThumbnailRenderMap = [];
	$wgUploadPath = '/w/img_auth.php';
	$wgImgAuthUrlPathMap = [
		'/avatars/' => 'mwstore://wikiforge-swift/avatars/',
		'/awards/' => 'mwstore://wikiforge-swift/awards/',
		'/dumps/' => 'mwstore://wikiforge-swift/dumps-backup/',
		'/score/' => 'mwstore://wikiforge-swift/score-render/',
		'/timeline/' => 'mwstore://wikiforge-swift/timeline-render/',
	];
}

$wgLocalFileRepo = [
	'class' => LocalRepo::class,
	'name' => 'local',
	'backend' => 'wikiforge-swift',
	'url' => $wgUploadBaseUrl ? $wgUploadBaseUrl . $wgUploadPath : $wgUploadPath,
	'scriptDirUrl' => $wgScriptPath,
	'hashLevels' => 2,
	'thumbScriptUrl' => $wgThumbnailScriptPath,
	'transformVia404' => true,
	'useJsonMetadata'   => true,
	'useSplitMetadata'  => true,
	'deletedHashLevels' => 3,
	'abbrvThreshold' => 160,
	'isPrivate' => $cwPrivate,
	'zones' => $cwPrivate
		? [
			'thumb' => [ 'url' => "$wgScriptPath/thumb_handler.php" ] ]
		: [],
];
