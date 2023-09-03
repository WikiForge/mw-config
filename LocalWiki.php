<?php

// Per-wiki settings that are incompatible with LocalSettings.php
switch ( $wi->dbname ) {
	case 'accountsinternalwiki':
		wfLoadExtension( 'LdapAuthentication' );

		$wgAuthManagerAutoConfig['primaryauth'] += [
			LdapPrimaryAuthenticationProvider::class => [
				'class' => LdapPrimaryAuthenticationProvider::class,
				'args' => [ [
					// don't allow local non-LDAP accounts
					'authoritative' => true,
				] ],
				// must be smaller than local pw provider
				'sort' => 50,
			],
		];

		break;
	case 'betatestwiki':
		$wgDplSettings['functionalRichness'] = 4;
		break;
	case 'hubwiki':
		wfLoadExtensions( [
			'FileStorageMonitor',
			'GlobalWatchlist',
			'ImportDump',
			'IncidentReporting',
			'SecurePoll',
		] );

		$wgFileStorageMonitorAWSBucketName = $wgAWSBucketName;
		$wgFileStorageMonitorAWSRegion = $wgAWSRegion;
		$wgFileStorageMonitorAWSAccessKey = $wmgAWSAccessKey;
		$wgFileStorageMonitorAWSSecretKey = $wmgAWSAccessSecretKey;
		break;
	case 'metawikitide':
		wfLoadExtensions( [
			'GlobalWatchlist',
			'ImportDump',
			'IncidentReporting',
			'RemovePII',
			'SecurePoll',
		] );
		break;
}
