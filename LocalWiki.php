<?php

// Per-wiki settings that are incompatible with LocalSettings.php
switch ( $wi->dbname ) {
	case 'betatestwiki':
		$wgDplSettings['functionalRichness'] = 4;
		break;
	case 'hubwiki':
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
}
