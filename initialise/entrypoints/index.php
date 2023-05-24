<?php

define( 'MW_ENTRY_POINT', 'index' );

require_once '/srv/mediawiki/config/initialise/WikiForgeFunctions.php';
require WikiForgeFunctions::getMediaWiki( 'includes/WebStart.php' );


if ( $wgArticlePath === '/$1' && str_contains( strtoupper( $_SERVER['REQUEST_URI'] ), strtoupper( '/wiki/' ) ) ) {
	// Redirect to the same page maintaining the path
	header( 'Location: ' . str_replace( '/wiki/', '/', $_SERVER['REQUEST_URI'] ), true, 301 );
	exit;
} elseif ( $wgArticlePath === '/wiki/$1' && !str_contains( $_SERVER['REQUEST_URI'], '/wiki/' ) && !str_contains( $_SERVER['REQUEST_URI'], '/w/' ) && !( $wgMainPageIsDomainRoot && $_SERVER['REQUEST_URI'] === '/' ) ) {
	// Redirect to the same page maintaining the path
	header( 'Location: /wiki' . $_SERVER['REQUEST_URI'], true, 301 );
	exit;
}

if ( $wgArticlePath === '/$1' || ( $wgMainPageIsDomainRoot && $_SERVER['REQUEST_URI'] !== '/' ) ) {
	// Try to redirect the main page to domain root if using $wgMainPageIsDomainRoot
	$title = '';
	if ( isset( $_SERVER['REQUEST_URI'] ) ) {
		$path = parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH );
		$segments = explode( '/', $path );
		$title = end( $segments );
	}

	// Check if the title matches the main page title
	if ( $wgMainPageIsDomainRoot && $_SERVER['REQUEST_URI'] !== '/' && $title === str_replace( ' ', '_', wfMessage( 'mainpage' )->text() ) ) {
		// Redirect to the domain root
		header( 'Location: /', true, 301 );
		exit;
	}

	if ( mb_strtolower( mb_substr( $title, 0, 1 ) ) === mb_substr( $title, 0, 1 ) ) {
		$currentTitle = Title::newFromText( $title );
		if ( $currentTitle ) {
			$namespaceInfo = MediaWiki\MediaWikiServices::getInstance()->getNamespaceInfo();
			if ( $namespaceInfo->isCapitalized( $currentTitle->getNamespace() ) ) {
				header( 'Location: ' . str_replace( $title, ucfirst( $title ), $_SERVER['REQUEST_URI'] ), true, 301 );
				exit;
			}

			// Don't need a global here
			unset( $namespaceInfo );
		}

		// Don't need a global here
		unset( $currentTitle );
	}

	// Don't need a global here
	unset( $title );
}

require_once WikiForgeFunctions::getMediaWiki( 'includes/PHPVersionCheck.php' );
wfEntryPointCheck( 'html', dirname( $_SERVER['SCRIPT_NAME'] ) );

wfIndexMain();

function wfIndexMain() {
	$mediaWiki = new MediaWiki();
	$mediaWiki->run();
}
