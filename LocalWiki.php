<?php

// Per-wiki settings that are incompatible with LocalSettings.php
switch ( $wi->dbname ) {
	case 'dcmultiversewiki':
		$wgForeignFileRepos[] = [
			'class' => ForeignAPIRepo::class,
			'name' => 'miraheze',
			'apibase' => 'https://dcmultiverse.miraheze.org/w/api.php',
			'url' => 'https://static.miraheze.org/dcmultiversewiki',
			'thumbUrl' => 'https://static.miraheze.org/dcmultiversewiki/thumb',
			'hashLevels' => 2,
			'transformVia404' => true,
			'fetchDescription' => true,
			'descriptionCacheExpiry' => 43200,
			'apiThumbCacheExpiry' => 0,
		];
		break;
	case 'metawiki':
		wfLoadExtension( 'GlobalWatchlist' );
		break;
}
