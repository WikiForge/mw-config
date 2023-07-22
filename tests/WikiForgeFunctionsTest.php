<?php

namespace WikiForge\Config\Tests;

use PHPUnit\Framework\TestCase;
use MediaWiki\MediaWikiServices;
use ReflectionClass;
use WikiForgeFunctions;

require_once __DIR__ . '/../initialise/WikiForgeFunctions.php';

/**
 * @coversDefaultClass \WikiForgeFunctions
 */
class WikiForgeFunctionsTest extends TestCase {

	protected function setUp(): void {
		// Mock MediaWikiServices
		$mockMediaWikiServices = $this->getMockBuilder(MediaWikiServices::class)
			->disableOriginalConstructor()
			->getMock();

		// Replace the actual MediaWikiServices instance with a mocked instance
		$this->replaceInstance(MediaWikiServices::class, $mockMediaWikiServices);
	}

	/**
	 * @covers ::getLocalDatabases
	 */
	public function testGetLocalDatabasesReturnsArrayOrNull(): void {
		// Test when there are local databases available
		$mockedLocalDatabases = ['db1', 'db2', 'db3'];
		$this->expectsMockedLocalDatabases($mockedLocalDatabases);

		$localDatabases = WikiForgeFunctions::getLocalDatabases();
		$this->assertIsArray($localDatabases, "getLocalDatabases should return an array when there are local databases available");
		$this->assertEquals($mockedLocalDatabases, $localDatabases, "getLocalDatabases should return the correct array of local databases");

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

		$databases = WikiForgeFunctions::readDbListFile('databases-wikiforge');
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
		$this->expectsMockedGetCurrentDatabase('https://wikiforge.net');
		$wikiFarm = WikiForgeFunctions::getWikiFarm();
		$this->assertEquals('wikiforge', $wikiFarm, "getWikiFarm should return 'wikiforge' when the current database is from 'wikiforge'");

		// Test when the current database is from 'wikitide'
		$this->expectsMockedGetCurrentDatabase('https://wikitide.com');
		$wikiFarm = WikiForgeFunctions::getWikiFarm();
		$this->assertEquals('wikitide', $wikiFarm, "getWikiFarm should return 'wikitide' when the current database is from 'wikitide'");

		// Test when the current database is not recognized
		$this->expectsMockedGetCurrentDatabase('https://example.com');
		$wikiFarm = WikiForgeFunctions::getWikiFarm();
		$this->assertNotEquals('wikitide', $wikiFarm, "getWikiFarm should return an unrecognized database when the current database is not recognized");
		$this->assertNotEquals('wikiforge', $wikiFarm, "getWikiFarm should return an unrecognized database when the current database is not recognized");
	}

	private function expectsMockedLocalDatabases($returnValue): void {
		$mockedObject = $this->getMockBuilder(WikiForgeFunctions::class)
			->onlyMethods(['getLocalDatabases'])
			->getMock();

		$mockedObject->method('getLocalDatabases')
			->willReturn($returnValue);

		$this->replaceInstance(WikiForgeFunctions::class, $mockedObject);
	}

	private function expectsMockedReadDbListFile($dblist, $returnValue): void {
		$mockedObject = $this->getMockBuilder(WikiForgeFunctions::class)
			->onlyMethods(['readDbListFile'])
			->getMock();

		$mockedObject->method('readDbListFile')
			->with($dblist)
			->willReturn($returnValue);

		$this->replaceInstance(WikiForgeFunctions::class, $mockedObject);
	}

	private function expectsMockedGetCurrentDatabase($returnValue): void {
		$mockedObject = $this->getMockBuilder(WikiForgeFunctions::class)
			->onlyMethods(['getCurrentDatabase'])
			->getMock();

		$mockedObject->method('getCurrentDatabase')
			->willReturn($returnValue);

		$this->replaceInstance(WikiForgeFunctions::class, $mockedObject);
	}

	private function replaceInstance($class, $instance) {
		$reflectionClass = new ReflectionClass($class);
		$property = $reflectionClass->getProperty('instance');
		$property->setAccessible(true);
		$property->setValue($instance);
	}
}
