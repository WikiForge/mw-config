<?php

$wgMemCachedServers = [];
$wgMemCachedPersistent = false;

// $beta = preg_match( '/^(.*)\.nexttide\.org$/', $wi->server );

// mem11
$wgObjectCaches['memcached'] = [
	'class'                => MemcachedPeclBagOStuff::class,
	'serializer'           => 'php',
	'persistent'           => false,
	'servers'              => [ '127.0.0.1:11212' ],
	// Effectively disable the failure limit (0 is invalid)
	'server_failure_limit' => 1e9,
	// Effectively disable the retry timeout
	'retry_timeout'        => -1,
	'loggroup'             => 'memcached',
	// 500ms, in microseconds
	'timeout'              => 1 * 1e6,
];

$wgObjectCaches['mysql-multiwrite'] = [
	'class' => MultiWriteBagOStuff::class,
	'caches' => [
		0 => [
			'factory' => [ 'ObjectCache', 'getInstance' ],
			'args' => [ 'memcached' ]
		],
		1 => [
			'class' => SqlBagOStuff::class,
			'servers' => [
				'parsercache' => [
					'type'      => 'mysql',
					'host'      => $wmgDBHostname,
					'dbname'    => 'parsercache',
					'user'      => $wgDBuser,
					'password'  => $wgDBpassword,
					'ssl'       => true,
					'flags'     => 0,
					/**
					 * MediaWiki checks if the certificate presented by MariaDB is signed
					 * by the certificate authority listed in 'sslCAFile'. In emergencies
					 * this could be set to /etc/ssl/certs/ca-certificates.crt (all trusted
					 * CAs), but setting this to one CA reduces attack vector and CAs
					 * to dig through when checking the certificate provided by MariaDB.
					 *
					 * TEMPORARY: use ca-certificates.crt, as this doesn't seem to like LetsEncrypt.crt
					 */
					'sslCAFile' => '/etc/ssl/certs/ca-certificates.crt',
				],
			],
			'purgePeriod' => 0,
			'tableName' => 'pc',
			'shards' => 5,
			'reportDupes' => false
		],
	],
	'replication' => 'async',
	'reportDupes' => false
];

if ( $wi->wikifarm === 'wikitide' ) {
	$wgObjectCaches['redis-session'] = [
		'class' => RedisBagOStuff::class,
		'servers' => [ $wmgRedisHostname ],
		'password' => $wmgRedisPassword,
		'loggroup' => 'redis',
		'reportDupes' => false,
	];

	$wgSessionCacheType = 'redis-session';
} else {
	$wgSessionCacheType = 'memcached';

	// Same as $wgMainStash
	$wgMWOAuthSessionCacheType = 'db-replicated';
}

$wgMainCacheType = 'memcached';
$wgMessageCacheType = 'memcached';

$wgParserCacheType = 'mysql-multiwrite';
$wgParsoidCacheConfig = [
	'StashType' => null,
	'StashDuration' => 24 * 60 * 60,
	'CacheThresholdTime' => 0.0,
	// This created over 50,000 parsoidCachePrewarm jobs on a single wiki,
	// and creates jobs faster than the queues can run them.
	// Do not enable yet.
	'WarmParsoidParserCache' => false,
];

$wgLanguageConverterCacheType = CACHE_ACCEL;

// 5 days
$wgParserCacheExpireTime = 86400 * 5;

// 3 days
$wgRevisionCacheExpiry = 86400 * 3;

// 1 day
$wgObjectCacheSessionExpiry = 86400;

$wgDLPQueryCacheTime = 120;
$wgDplSettings['queryCacheTime'] = 120;

$wgEnableSidebarCache = true;

$wgUseLocalMessageCache = true;
$wgInvalidateCacheOnLocalSettingsChange = false;

/** if ( $beta ) {
	// test11 - No production traffic may use test11
	$wgObjectCaches['memcached-test11'] = [
		'class'                => MemcachedPeclBagOStuff::class,
		'serializer'           => 'php',
		'persistent'           => false,
		'servers'              => [ '127.0.0.1:11215' ],
		// Effectively disable the failure limit (0 is invalid)
		'server_failure_limit' => 1e9,
		// Effectively disable the retry timeout
		'retry_timeout'        => -1,
		'loggroup'             => 'memcached',
		// 500ms, in microseconds
		'timeout'              => 1 * 1e6,
	];

	$redisServerIP = '127.0.0.1:6379';

	$wgMainCacheType = 'memcached-test11';
	$wgMessageCacheType = 'memcached-test11';

	$wgSessionCacheType = 'memcached-test11';
	$wgMWOAuthSessionCacheType = 'memcached-test11';
}*/

$wgJobTypeConf['default'] = [
	'class' => JobQueueRedis::class,
	'redisServer' => $wmgRedisHostname,
	'redisConfig' => [
		'connectTimeout' => 2,
		'password' => $wmgRedisPassword,
		'compression' => 'gzip',
	],
	'claimTTL' => 3600,
	'daemonized' => true,
];

if ( PHP_SAPI === 'cli' ) {
	// APC not available in CLI mode
	$wgLanguageConverterCacheType = CACHE_NONE;
}
