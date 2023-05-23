<?php

require_once '/srv/mediawiki/config/initialise/WikiForgeFunctions.php';
require WikiForgeFunctions::getMediaWiki( 'includes/WebStart.php' );

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

		$redirectUrl = $articlePath . '/' . $title;
	}

	if ( !empty( $queryParameters ) ) {
		$redirectUrl .= '?' . http_build_query( $queryParameters );
	}
}

// TODO: use ucfirst() and support $wgCapitalLinks and $wgCapitalLinkOverrides
$redirectUrl = str_replace( ' ', '_', $redirectUrl );
header( 'Location: ' . $redirectUrl, true, 301 );

exit();
?>
