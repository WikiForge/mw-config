<?php

// Per-wiki settings that are incompatible with LocalSettings.php
switch ( $wi->dbname ) {
	case 'betatestwiki':
		$wgDplSettings['functionalRichness'] = 4;
		break;
	case 'metawiki':
	case 'metawikitide':
		wfLoadExtensions( [
			'FileStorageMonitor',
			'GlobalWatchlist',
			'ImportDump',
			'IncidentReporting',
			'RemovePII',
			'SecurePoll',
		] );

		$wgFileStorageMonitorAWSBucketName = $wgAWSBucketName;
		$wgFileStorageMonitorAWSRegion = $wgAWSRegion;
		$wgFileStorageMonitorAWSAccessKey = $wmgAWSAccessKey;
		$wgFileStorageMonitorAWSSecretKey = $wmgAWSAccessSecretKey;
		break;
	case 'votewikitide':
		wfLoadExtensions( [
			 'SecurePoll',
		] );
		break;
}
