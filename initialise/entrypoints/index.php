<?php

define( 'MW_ENTRY_POINT', 'index' );

require_once '/srv/mediawiki/config/initialise/WikiForgeFunctions.php';
require WikiForgeFunctions::getMediaWiki( 'includes/WebStart.php' );

if ( $wgArticlePath === '/$1' && strpos( strtoupper( $_SERVER['REQUEST_URI'] ), strtoupper( '/wiki/' ) ) === 0 ) {
	// Redirect to the same page maintaining the path
	header( 'Location: ' . str_replace( '/wiki/', '/', $_SERVER['REQUEST_URI'] ), true, 302 );
	exit;
} elseif ( $wgArticlePath === '/wiki/$1' && strpos( $_SERVER['REQUEST_URI'], '/wiki/' ) !== 0 ) {
	// Redirect to the same page maintaining the path
	header( 'Location: /wiki' . $_SERVER['REQUEST_URI'], true, 302 );
	exit;
}

if ( strpos( $_SERVER['REQUEST_URI'], '/w/index.php' ) !== 0 && ( $_GET['action'] ?? '' ) !== 'submit' ) {
	$queryString = $_SERVER['QUERY_STRING'];
	$articlePath = str_replace( '$1', '', $wgArticlePath );

	if ( preg_match( '/(.*)(?:^|&)title=([^&]*)(?:&|$)(.*)/', $queryString, $matches ) ) {
		$newArgs = $matches[1] . $matches[3];
		$redirectUrl = $articlePath . $matches['2'] . '?' . $newArgs;

		header( 'Location: ' . $redirectUrl, true, 302 );
		exit;
	}

	header( 'Location: ' . $articlePath, true, 302 );
	exit;
}

require_once WikiForgeFunctions::getMediaWiki( 'includes/PHPVersionCheck.php' );
wfEntryPointCheck( 'html', dirname( $_SERVER['SCRIPT_NAME'] ) );

wfIndexMain();

function wfIndexMain() {
	$mediaWiki = new MediaWiki();
	$mediaWiki->run();
}
