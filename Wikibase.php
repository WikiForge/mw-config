<?php

// Documentation for Wikibase: https://www.mediawiki.org/wiki/Wikibase/Installation/Advanced_configuration#Configuration

// You should only need to set $wgWBClientSettings['repoUrl'], $wgWBClientSettings['repoDatabase'] and $wgWBClientSettings['changesDatabase']
// on the wiki.

$entitySources = [
	'local' => [
		'entityNamespaces' => [
			'item' => $wmgWikibaseRepoItemNamespaceID,
			'property' => $wmgWikibaseRepoPropertyNamespaceID,
		],
		'repoDatabase' => $wmgWikibaseRepoDatabase,
		'baseUri' => $wmgWikibaseRepoUrl . '/entity/',
		'interwikiPrefix' => '',
		'rdfNodeNamespacePrefix' => 'wd',
		'rdfPredicateNamespacePrefix' => '',
		'type' => 'db'
	],
];

$wgWBClientSettings['tmpUnconnectedPagePagePropMigrationStage'] = MIGRATION_NEW;

if ( $wi->isExtensionActive( 'WikibaseLexeme' ) ) {
	$entitySources['local']['entityNamespaces']['lexeme'] = 146;
	$wgWBRepoSettings['entityNamespaces']['lexeme'] = 146;
}

$wgWBRepoSettings['entitySources'] = $entitySources;
$wgWBRepoSettings['localEntitySourceName'] = 'local';
$wgWBRepoSettings['entityNamespaces']['item'] = $wmgWikibaseRepoItemNamespaceID;
$wgWBRepoSettings['entityNamespaces']['property'] = $wmgWikibaseRepoPropertyNamespaceID;
$wgWBRepoSettings['allowEntityImport'] = $wmgAllowEntityImport;
$wgWBRepoSettings['enableEntitySearchUI'] = $wmgEnableEntitySearchUI;
$wgWBRepoSettings['federatedPropertiesEnabled'] = $wmgFederatedPropertiesEnabled;
$wgWBRepoSettings['formatterUrlProperty'] = $wmgFormatterUrlProperty ?: null;
$wgWBRepoSettings['canonicalUriProperty'] = $wmgCanonicalUriProperty ?: null;

$siteGroup = $wi->wikifarm === 'wikitide' ? 'wikitide' : 'wikiforge';

$wgWBRepoSettings['siteLinkGroups'] = [
	$siteGroup
];

$wgWBRepoSettings['specialSiteLinkGroups'] = [];

$wgWBClientSettings['entitySources'] = $entitySources;
$wgWBClientSettings['itemAndPropertySourceName'] = 'local';
$wgWBClientSettings['repoUrl'] = $wmgWikibaseRepoUrl;
$wgWBClientSettings['repoDatabase'] = $wmgWikibaseRepoDatabase;
$wgWBClientSettings['changesDatabase'] = $wmgWikibaseRepoDatabase;
$wgWBClientSettings['repositories'] = [
	'' => [
		'repoDatabase' => $wmgWikibaseRepoDatabase,
		'baseUri' => $wmgWikibaseRepoUrl . '/entity/',
		'entityNamespaces' => [
			'item' => $wmgWikibaseItemNamespaceID,
			'property' => $wmgWikibasePropertyNamespaceID
		],
		'prefixMapping' => [
			'' => ''
		]
	]
];

$wgWBClientSettings['siteGlobalID'] = $wi->dbname;
$wgWBClientSettings['repoScriptPath'] = '/w';
$wgWBClientSettings['repoArticlePath'] = $wgArticlePath;
$wgWBClientSettings['siteGroup'] = $siteGroup;
$wgWBClientSettings['repoNamespaces'] = [
	'wikibase-item' => 'Item',
	'wikibase-property' => 'Property'
];

$wgWBClientSettings['siteLinksGroups'] = [
	$siteGroup
];

$wgWBClientSettings['purgeCacheBatchSize'] = 100;
$wgWBClientSettings['recentChangesBatchSize'] = 100;

// Per-wiki goes below here

// don't need these to be globals
unset( $entitySources, $siteGroup );
