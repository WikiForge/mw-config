<?php

if ( !isset( $_GET['action'] ) ) {
	// Set an action value, so that MediaWiki
	// doesn't automatically redirect before we
	// do our own, as MediaWiki's will sometimes
	// redirect everything to the main page.
	$_GET['action'] = 'redirect';
}

require_once '/srv/mediawiki/config/initialise/WikiForgeFunctions.php';
require WikiForgeFunctions::getMediaWiki( 'index.php' );

if ( $wgArticlePath === '/$1' ) {
	// Redirect to the same page maintaining the path
	$output = RequestContext::getMain()->getOutput();
	$output->redirect( $_SERVER['REQUEST_URI'], 302 );
} elseif ( $wgArticlePath === '/wiki/$1' ) {
	// Redirect to the same page maintaining the path
	$output = RequestContext::getMain()->getOutput();
	$output->redirect( '/wiki' . $_SERVER['REQUEST_URI'], 302 );
}
