<?php

namespace WikiForge\Config\Tests;

use PHPUnit\Framework\TestCase;
use phpmock\phpunit\PHPMock;
use MediaWiki\MediaWikiServices;
use ReflectionClass;
use WikiForgeFunctions;

require_once __DIR__ . '/../initialise/WikiForgeFunctions.php';

/**
 * @coversDefaultClass \WikiForgeFunctions
 */
class WikiForgeFunctionsTest extends TestCase {
	use PHPMock;

	protected function setUp(): void {
		parent::setUp();

		if ( !defined( 'PHPUNIT_TEST' ) ) {
			define( 'PHPUNIT_TEST', true );
		}

		// Set $_SERVER['HTTP_HOST']
		$_SERVER['HTTP_HOST'] = 'example.com';

		// Mock the getInstance method of MediaWikiServices using php-mock
		$this->getFunctionMock(MediaWikiServices::class, 'getInstance')
			->expects($this->any())
			->willReturnCallback(function () {
				 $mockMediaWikiServices = $this->getMockBuilder(MediaWikiServices::class)
					->getMock();

					return $mockMediaWikiServices;
				});

		$this->getFunctionMock('WikiForgeFunctions', 'constant')
            ->expects($this->any())
            ->with('WikiForgeFunctions::CACHE_DIRECTORY')
            ->willReturn(__DIR__ . '/stubs' );
	}

	/**
	 * @covers ::getLocalDatabases
	 */
	public function testGetLocalDatabasesReturnsArrayOrNull(): void {
		// Test when there are local databases available
		$mockedLocalDatabases = ['db1wiki', 'db2wiki', 'db3wiki'];
		$this->expectsMockedLocalDatabases($mockedLocalDatabases);

		$localDatabases = WikiForgeFunctions::getLocalDatabases();
		$this->assertIsArray($localDatabases, "getLocalDatabases should return an array when there are local databases available");
		$this->assertEquals($mockedLocalDatabases, WikiForgeFunctions::CACHE_DIRECTORY, "getLocalDatabases should return the correct array of local databases");

		// Test when there are no local databases available
		$this->expectsMockedLocalDatabases([]);
		$localDatabases = WikiForgeFunctions::getLocalDatabases();
		$this->assertNull($localDatabases, "getLocalDatabases should return null when there are no local databases available");

		// Test when an error occurs while fetching local databases
		$this->expectsMockedLocalDatabases(null);
		$localDatabases = WikiForgeFunctions::getLocalDatabases();
		$this->assertNull($localDatabases, "getLocalDatabases should return null when an error occurs while fetching local databases");
	}

	/**
	 * @covers ::readDbListFile
	 */
	public function testReadDbListFileReturnsArrayOrString(): void {
		// Test when the database list file exists and contains valid data
		$mockedDatabaseList = ['db1' => 'data1', 'db2' => 'data2'];
		$this->expectsMockedReadDbListFile('databases-wikiforge', $mockedDatabaseList);

		$databases = $this->expectsMockedReadDbListFile->readDbListFile('databases-wikiforge');
		$this->assertIsArray($databases, "readDbListFile should return an array when the database list file exists and contains valid data");
		$this->assertEquals($mockedDatabaseList, $databases, "readDbListFile should return the correct array of databases");

		// Test when the database list file exists but is empty or contains invalid data
		$this->expectsMockedReadDbListFile('databases-wikiforge', []);
		$databases = WikiForgeFunctions::readDbListFile('databases-wikiforge');
		$this->assertEquals($databases, [], "readDbListFile should return an empty array when the database list file is empty or contains invalid data");

		// Test when the database list file does not exist
		$this->expectsMockedReadDbListFile('non_existent_db_list', null);
		$databases = WikiForgeFunctions::readDbListFile('non_existent_db_list');
		$this->assertNull($databases, "readDbListFile should return null when the database list file does not exist");

		// Test when fetching a specific database from the list
		$this->expectsMockedReadDbListFile('databases-wikiforge', ['db1' => 'data1', 'db2' => 'data2']);
		$database = 'db2';
		$result = WikiForgeFunctions::readDbListFile('databases-wikiforge', true, $database);
		$this->assertEquals('data2', $result, "readDbListFile should return the correct database when fetching a specific database from the list");
	}

	/**
	 * @covers ::getWikiFarm
	 */
	public function testGetWikiFarmReturnsString(): void {
		// Test when the current database is from 'wikiforge'
		$this->expectsMockedGetCurrentDatabase( 'testwiki' );
		$wikiFarm = WikiForgeFunctions::getWikiFarm();
		$this->assertEquals('wikiforge', $wikiFarm, "getWikiFarm should return 'wikiforge' when the current database is from 'wikiforge'");

		// Test when the current database is from 'wikitide'
		$this->expectsMockedGetCurrentDatabase( 'testwikitide' );
		$wikiFarm = WikiForgeFunctions::getWikiFarm();
		$this->assertEquals('wikitide', $wikiFarm, "getWikiFarm should return 'wikitide' when the current database is from 'wikitide'");

		// Test when the current database is not recognized
		$this->expectsMockedGetCurrentDatabase( 'wikitest' );
		$wikiFarm = WikiForgeFunctions::getWikiFarm();
		$this->assertEquals('wikitide', $wikiFarm, "getWikiFarm should return 'wikitide' when the current database is not recognized");
	}

	private function expectsMockedLocalDatabases($returnValue) {
		$this->expectsMockedReadDbListFile( 'databases-wikiforge', $returnValue );
		$mockedObject = $this->getMockBuilder(WikiForgeFunctions::class)
			->onlyMethods(['getLocalDatabases'])
			->getMock();

		$mockedObject->method('getLocalDatabases')
			->willReturn($returnValue);

		return $mockedObject;
	}

	private function expectsMockedReadDbListFile($dblist, $returnValue): void {
		$mockedObject = $this->getMockBuilder(WikiForgeFunctions::class)
			->onlyMethods(['readDbListFile'])
			->getMock();

		$mockedData = json_decode(file_get_contents(__DIR__ . '/mocked_databases.json'), true);

		$mockedObject->method('readDbListFile')
			->willReturnCallback(function ($dblist, $onlyDBs, $database, $fromServer) use ($mockedData) {
				return $mockedData[$dblist];
			});
	}

	private function expectsMockedGetCurrentDatabase($returnValue): void {
		$reflectionClass = new ReflectionClass(WikiForgeFunctions::class);
		$currentDatabaseProperty = $reflectionClass->getProperty('currentDatabase');
		$currentDatabaseProperty->setAccessible(true);
		$currentDatabaseProperty->setValue(null);
		putenv( 'PHPUNIT_WIKI=' . $returnValue);
	}
}
