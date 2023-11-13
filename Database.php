<?php

$wgLBFactoryConf = [
	'class' => \Wikimedia\Rdbms\LBFactoryMulti::class,
	'sectionsByDB' => $wi->wikiDBClusters,
	'sectionLoads' => [
		'DEFAULT' => [
			'db1' => 1,
		],
		'c1' => [
			'db1' => 1,
		],
	],
	'serverTemplate' => [
		'dbname' => $wgDBname,
		'user' => $wgDBuser,
		'password' => $wgDBpassword,
		'type' => 'mysql',
		'ssl' => true,
		'flags' => DBO_DEFAULT,
		'variables' => [
			// https://mariadb.com/docs/reference/mdb/system-variables/innodb_lock_wait_timeout
			'innodb_lock_wait_timeout' => 15,
		],
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
	'hostsByName' => [
		'db1' => $wmgDBHostname,
	],
	'externalLoads' => [
		'echo' => [
			/** where the metawiki and metawikitide databases are located */
			'db1' => 1,
		],
	],
	'readOnlyBySection' => [
		// 'DEFAULT' => 'Please try again in a few minutes.',
		// 'c1' => 'Please try again in a few minutes.',
	],
];

// Disallow web request database transactions that are slower than 3 seconds
$wgMaxUserDBWriteDuration = 6;

// Max execution time for expensive queries of special pages (in milliseconds)
$wgMaxExecutionTimeForExpensiveQueries = 30000;
