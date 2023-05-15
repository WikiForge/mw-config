<?php

define( 'MW_ENTRY_POINT', 'index' );

require_once '/srv/mediawiki/config/initialise/WikiForgeFunctions.php';
require WikiForgeFunctions::getMediaWiki( 'includes/WebStart.php' );

if ( $wgArticlePath === '/$1' && strpos( strtoupper( $_SERVER['REQUEST_URI'] ), strtoupper( '/wiki/' ) ) !== 0 ) {
	// Redirect to the same page maintaining the path
	http_response_code( 302 );
	header( 'Location: ' . str_replace( '/wiki/', '/', $_SERVER['REQUEST_URI'] ) );
	exit;
} elseif ( $wgArticlePath === '/wiki/$1' && strpos( $_SERVER['REQUEST_URI'], '/wiki/' ) !== 0 ) {
	// Redirect to the same page maintaining the path
	http_response_code( 302 );
	header( 'Location: /wiki' . $_SERVER['REQUEST_URI'] );
	exit;
}

require_once WikiForgeFunctions::getMediaWiki( 'includes/PHPVersionCheck.php' );
wfEntryPointCheck( 'html', dirname( $_SERVER['SCRIPT_NAME'] ) );

wfIndexMain();

function wfIndexMain() {
	$mediaWiki = new MediaWiki();
	$mediaWiki->run();
}
