<?php

require_once '/srv/mediawiki/config/initialise/WikiForgeFunctions.php';
require_once WikiForgeFunctions::getMediaWiki( 'includes/WebStart.php' );

use MediaWiki\MediaWikiServices;

$uri = $_SERVER['REQUEST_URI'];
$queryString = $_SERVER['QUERY_STRING'] ?? '';

$decodedUri = urldecode( $uri );
$decodedUri = str_replace( '/w/index.php', '', $decodedUri );

$articlePath = str_replace( '/$1', '', $wgArticlePath );
$redirectUrl = $articlePath . $decodedUri;

if ( $queryString ) {
	$decodedQueryString = urldecode( $queryString );
	parse_str( $decodedQueryString, $queryParameters );

	if ( isset( $queryParameters['useformat'] ) ) {
		$_GET['useformat'] = $queryParameters['useformat'];
		unset( $queryParameters['useformat'] );
	}

	if ( isset( $queryParameters['title'] ) ) {
		$title = $queryParameters['title'];
		unset( $queryParameters['title'] );

		if ( mb_strtolower( mb_substr( $title, 0, 1 ) ) === mb_substr( $title, 0, 1 ) ) {
			$currentTitle = Title::newFromText( $title );
			if ( $currentTitle ) {
				$namespaceInfo = MediaWikiServices::getInstance()->getNamespaceInfo();
				if ( $namespaceInfo->isCapitalized( $currentTitle->getNamespace() ) ) {
					$title = ucfirst( $title );
				}
			}
		}

		$redirectUrl = $articlePath . '/' . $title;
	}

	if ( !empty( $queryParameters ) ) {
		$redirectUrl .= '?' . http_build_query( $queryParameters );
	}
}

$redirectUrl = str_replace( ' ', '_', $redirectUrl );

$decodedRedirectUrl = urldecode( $redirectUrl );
$decodedRedirectUrl = str_replace( '/w/index.php', '', $decodedRedirectUrl );

if ( $decodedUri !== $decodedRedirectUrl ) {
	header( 'Location: ' . $redirectUrl, true, 302 );
	exit;
}
?>
