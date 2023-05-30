<?php

// Per-wiki settings that are incompatible with LocalSettings.php
switch ( $wi->dbname ) {
	case 'betatestwiki':
		$wgDplSettings['functionalRichness'] = 4;
		break;
	case 'metawiki':
		wfLoadExtensions( [
			'GlobalWatchlist',
			'ImportDump',
			'IncidentReporting',
			'RemovePII',
		] );
		break;
}
