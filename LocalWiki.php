<?php

// Per-wiki settings that are incompatible with LocalSettings.php
switch ( $wi->dbname ) {
	case 'accountsinternalwiki':
	case 'techwiki':
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
	case 'snxyzincubatorwikitide':
		$wgLogos = [
			'1x' => "https://static.wikiforge.net/snxyzincubatorwikitide/2/2e/Incubator_Logo.2023.svg",
			'svg' => "https://static.wikiforge.net/snxyzincubatorwikitide/2/2e/Incubator_Logo.2023.svg",
			'icon' => "https://static.wikiforge.net/snxyzincubatorwikitide/2/2e/Incubator_Logo.2023.svg",
			'wordmark' => [
				'src' => "https://static.wikiforge.net/snxyzincubatorwikitide/d/d5/Wordmark_EN.svg",
				'1x' => "https://static.wikiforge.net/snxyzincubatorwikitide/d/d5/Wordmark_EN.svg",
				'width' => 135,
				'height' => 20,
			],
			'tagline' => [
				'src' => "https://static.wikiforge.net/snxyzincubatorwikitide/6/60/Tagline_EN.svg",
				'width' => 135,
				'height' => 15,
			],
			'variants' => [
				'vi' => [
					'wordmark' => [
						'src' => "https://static.wikiforge.net/snxyzincubatorwikitide/4/41/Wordmark_VI.svg",
						'1x' => "https://static.wikiforge.net/snxyzincubatorwikitide/4/41/Wordmark_VI.svg",
						'width' => 135,
						'height' => 20,
					],
					'tagline' => [
						'src' => "https://static.wikiforge.net/snxyzincubatorwikitide/1/1b/Tagline_VI.svg",
						'width' => 135,
						'height' => 15,
					],
				],
			],
		];
		break;
}
