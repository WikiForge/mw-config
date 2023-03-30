<?php

// Per-wiki settings that are incompatible with LocalSettings.php
switch ( $wi->dbname ) {
	case 'dcmultiversewiki':
		$wgForeignFileRepos[] = [
			'class' => \MediaWiki\Extension\QuickInstantCommons\Repo::class,
			'name' => 'miraheze',
			'apibase' => 'https://dcmultiverse.miraheze.org/w/api.php',
			'url' => 'https://static.miraheze.org/dcmultiversewiki',
			'thumbUrl' => 'https://static.miraheze.org/dcmultiversewiki/thumb',
			'hashLevels' => 2,
			'transformVia404' => true,
			'fetchDescription' => true,
			'descriptionCacheExpiry' => 43200,
			'abbrvThreshold' => 160,
		];
		break;
	case 'metawiki':
		wfLoadExtension( 'GlobalWatchlist' );
		break;
}
