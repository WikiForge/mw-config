<?php

// Per-wiki settings that are incompatible with LocalSettings.php
switch ( $wi->dbname ) {
	case 'metawiki':
		wfLoadExtensions( [
			'GlobalWatchlist',
			'ImportDump',
			'IncidentReporting',
			'RemovePII',
		] );
		break;
}
