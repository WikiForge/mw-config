<?php

define( 'MW_ENTRY_POINT', 'index' );

require_once '/srv/mediawiki/config/initialise/WikiForgeFunctions.php';
require WikiForgeFunctions::getMediaWiki( 'includes/WebStart.php' );

if ( $wgArticlePath === '/$1' && strpos( $_SERVER['REQUEST_URI'], '/wiki/' ) !== 0 ) {
	// Redirect to the same page maintaining the path
	$output = RequestContext::getMain()->getOutput();
	$output->redirect( $_SERVER['REQUEST_URI'], 302 );
} elseif ( $wgArticlePath === '/wiki/$1' && strpos( $_SERVER['REQUEST_URI'], '/wiki/' ) === 0 ) {
	// Redirect to the same page maintaining the path
	$output = RequestContext::getMain()->getOutput();
	$output->redirect( '/wiki' . $_SERVER['REQUEST_URI'], 302 );
}

require_once WikiForgeFunctions::getMediaWiki( 'includes/PHPVersionCheck.php' );
wfEntryPointCheck( 'html', dirname( $_SERVER['SCRIPT_NAME'] ) );

wfIndexMain();

function wfIndexMain() {
	$mediaWiki = new MediaWiki();
	$mediaWiki->run();
}
