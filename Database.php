<?php

$wgLBFactoryConf = [
	'class' => \Wikimedia\Rdbms\LBFactoryMulti::class,
	'sectionsByDB' => $wi->wikiDBClusters,
	'sectionLoads' => [
		'DEFAULT' => [
			'db21' => 1,
		],
		'c1' => [
			'db21' => 1,
		],
	],
	'serverTemplate' => [
		'dbname' => $wgDBname,
		'user' => $wgDBuser,
		'password' => $wgDBpassword,
		'type' => 'mysql',
		'flags' => DBO_DEFAULT,
		'variables' => [
			// https://mariadb.com/docs/reference/mdb/system-variables/innodb_lock_wait_timeout
			'innodb_lock_wait_timeout' => 15,
		],
	],
	'hostsByName' => [
		'db21' => '10.0.2.6',
	],
	'externalLoads' => [
		'echo' => [
			/** where the metawiki database is located */
			'db21' => 1,
		],
	],
	'readOnlyBySection' => [
		// 'DEFAULT' => 'Please try again in a few minutes.',
		// 'c1' => 'Please try again in a few minutes.',
	],
];

// Disallow web request database transactions that are slower than 3 seconds
$wgMaxUserDBWriteDuration = 60;

// Max execution time for expensive queries of special pages (in milliseconds)
$wgMaxExecutionTimeForExpensiveQueries = 30000;
