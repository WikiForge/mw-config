<?php

$uri = $_SERVER['REQUEST_URI'];
$queryString = $_SERVER['QUERY_STRING'] ?? '';

$decodedUri = urldecode( $uri );

$redirectUrl = '/wiki' . $decodedUri;
if ( $queryString ) {
	$decodedQueryString = urldecode( $queryString );
	$decodedQueryString = str_replace( '+', '_', $decodedQueryString );

	$redirectUrl .= '?' . $decodedQueryString;
}

header( 'Location: ' . $redirectUrl, true, 302 );

exit();
?>
