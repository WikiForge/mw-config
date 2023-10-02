<?php

use Psr\Log\LogLevel;

$wmgLogDir = '/var/log/mediawiki';

$wgDBerrorLog = "$wmgLogDir/debuglogs/database.log";

$wgDebugLogGroups = [
	'404' => "$wmgLogDir/debuglogs/404.log",
	'api' => "$wmgLogDir/debuglogs/api.log",
	'captcha' => "$wmgLogDir/debuglogs/captcha.log",
	'CentralAuth' => "$wmgLogDir/debuglogs/CentralAuth.log",
	'collection' => "$wmgLogDir/debuglogs/collection.log",
	'CreateWiki' => "$wmgLogDir/debuglogs/CreateWiki.log",
	'Echo' => "$wmgLogDir/debuglogs/Echo.log",
	'error' => "$wmgLogDir/debuglogs/php-error.log",
	'exception' => "$wmgLogDir/debuglogs/exception.log",
	'exec' => "$wmgLogDir/debuglogs/exec.log",
	'Math' => "$wmgLogDir/debuglogs/Math.log",
	'ManageWiki' => "$wmgLogDir/debuglogs/ManageWiki.log",
	'memcached' => [
		'destination' => "$wmgLogDir/debuglogs/memcached.log",
		'level' => LogLevel::ERROR,
	],
	'OAuth' => "$wmgLogDir/debuglogs/OAuth.log",
	'redis' => [
		'destination' => "$wmgLogDir/debuglogs/redis.log",
		'level' => LogLevel::WARNING,
	],
	'thumbnail' => "$wmgLogDir/debuglogs/thumbnail.log",
	'VisualEditor' => "$wmgLogDir/debuglogs/VisualEditor.log",
];

if ( wfHostname() === 'test11.wikiforge.net' ) {
	$wgShowExceptionDetails = true;
	$wgDebugDumpSql = true;
}

if ( $wgCommandLineMode ) {
	error_reporting( -1 );
	ini_set( 'display_startup_errors', 1 );
	ini_set( 'display_errors', 1 );

	$wgShowExceptionDetails = true;
	$wgDebugDumpSql = true;
}
