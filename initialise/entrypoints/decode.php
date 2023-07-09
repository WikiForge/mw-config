<?php

require_once '/srv/mediawiki/config/initialise/WikiForgeFunctions.php';
require WikiForgeFunctions::getMediaWiki( 'includes/WebStart.php' );

use MediaWiki\MediaWikiServices;

$uri = strtok( $_SERVER['REQUEST_URI'], '?' );
$queryString = $_SERVER['QUERY_STRING'] ?? '';

$decodedUri = urldecode( $uri );
$decodedUri = str_replace( '/w/index.php', '', $decodedUri );
$decodedUri = str_replace( '/index.php', '', $decodedUri );

$articlePath = str_replace( '/$1', '', $wgArticlePath );
$redirectUrl = ( $articlePath ?: '/' ) . $decodedUri;

if ( $decodedUri && !str_contains( $queryString, 'title' ) ) {
	$path = parse_url( $decodedUri, PHP_URL_PATH );
	$segments = explode( '/', $path );
	$title = end( $segments );

	$decodedQueryString = urldecode( $queryString );
	parse_str( $decodedQueryString, $queryParameters );

	$queryParameters['title'] = $title;
}

if ( $queryString || isset( $queryParameters ) ) {
	if ( !isset( $queryParameters ) ) {
		$decodedQueryString = preg_replace_callback( '/%((?!26)[0-9A-F]{2})/i', static function ( array $matches ): string {
			return urldecode( $matches[0] );
		}, $queryString );

		parse_str( $decodedQueryString, $queryParameters );
	}

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

		if ( $wgMainPageIsDomainRoot && $title === wfMessage( 'mainpage' )->text() ) {
			$articlePath = '';
			$title = '';
		}

		// These cause issues if they aren't encoded.
		// There is still an issue with & becoming ?
		// and the first ?action= becoming &action=
		// which breaks it.
		$title = str_replace( '%', '%25', $title );
		$title = str_replace( '&', '%26', $title );
		$title = str_replace( '?', '%3F', $title );

		$redirectUrl = $articlePath . '/' . $title;
	}

	if ( !empty( $queryParameters ) ) {
		if ( isset( $queryParameters['token'] ) ) {
			// This can not be decoded or it breaks the edit token for
			// things such as the Moderation extension
			$queryParameters['token'] = urlencode( $queryParameters['token'] );
			$queryParameters['token'] = str_replace( '%5C', '\\', $queryParameters['token'] );
		}

		$redirectUrl .= '?' . http_build_query( $queryParameters );
	}
}

$redirectUrl = str_replace( ' ', '_', $redirectUrl );
$redirectUrl = str_replace( '\\', '%5C', $redirectUrl );
header( 'Location: ' . $redirectUrl, true, 301 );

exit();
