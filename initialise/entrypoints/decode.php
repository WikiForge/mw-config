<?php

$uri = $_SERVER['REQUEST_URI'];
$queryString = $_SERVER['QUERY_STRING'] ?? '';

$decodedUri = urldecode( $uri );

$decodedUri = str_replace( '/w/index.php', '', $decodedUri );

$redirectUrl = '/wiki' . $decodedUri;

if ( $queryString ) {
	$decodedQueryString = urldecode( $queryString );
	parse_str( $decodedQueryString, $queryParameters );

	if ( isset( $queryParameters['title'] ) ) {
		$title = $queryParameters['title'];
		unset( $queryParameters['title'] );

		$redirectUrl = '/wiki/' . $title;
	}

	if ( !empty( $queryParameters ) ) {
		$redirectUrl .= '?' . http_build_query( $queryParameters );
	}
}

header( 'Location: ' . $redirectUrl, true, 302 );
exit();
?>
