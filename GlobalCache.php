<?php

$wgMemCachedServers = [];
$wgMemCachedPersistent = false;

// mem1
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
					'type'     => 'mysql',
					'host'     => 'db1.wikiforge.net',
					'dbname'   => 'parsercache',
					'user'     => $wgDBuser,
					'password' => $wgDBpassword,
					'flags'    => 0,
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

$wgSessionCacheType = 'memcached';

// Same as $wgMainStash
$wgMWOAuthSessionCacheType = 'db-replicated';

$redisServerIP = '[2a10:6740::6:306]:6379';

$wgMainCacheType = 'memcached';
$wgMessageCacheType = 'memcached';

$wgParserCacheType = 'mysql-multiwrite';

$wgLanguageConverterCacheType = CACHE_ACCEL;

// 5 days
$wgParserCacheExpireTime = 86400 * 5;

// 3 days
$wgRevisionCacheExpiry = 86400 * 3;

// 1 day
$wgObjectCacheSessionExpiry = 86400;

$wgDLPQueryCacheTime = 120;
$wgDplSettings['queryCacheTime'] = 120;

// Disable sidebar cache for select wikis as a solution to T8732, T9699, and T9884
if ( $wgDBname !== 'solarawiki' && $wgDBname !== 'constantnoblewiki' && $wgDBname !== 'nonciclopediawiki' ) {
	$wgEnableSidebarCache = true;
}

$wgUseLocalMessageCache = true;
$wgInvalidateCacheOnLocalSettingsChange = false;

$wgJobTypeConf['default'] = [
	'class' => JobQueueRedis::class,
	'redisServer' => $redisServerIP,
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

unset( $redisServerIP );
