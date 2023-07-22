<?php

namespace WikiForge\Config\Tests;

use MediaWiki\MediaWikiServices;
use phpmock\phpunit\PHPMock;
use PHPUnit\Framework\TestCase;
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
		$this->getFunctionMock( MediaWikiServices::class, 'getInstance' )
			->expects( $this->any() )
			->willReturnCallback( function () {
				$mockMediaWikiServices = $this->getMockBuilder( MediaWikiServices::class )
					->getMock();

					return $mockMediaWikiServices;
			} );
	}

	/**
	 * @covers ::getLocalDatabases
	 */
	public function testGetLocalDatabasesWhenWgLocalDatabasesIsSet() {
		// Mock $wgLocalDatabases
		$mockWgLocalDatabases = ['db1', 'db2'];

		// Ensure that $wgLocalDatabases is initially not set
		$this->assertNull($GLOBALS['wgLocalDatabases']);

		// Set the global variable for testing purposes
		$GLOBALS['wgLocalDatabases'] = $mockWgLocalDatabases;

		$result = WikiForgeFunctions::getLocalDatabases();
		$this->assertEquals($mockWgLocalDatabases, $result);
	}

	/**
	 * @covers ::getLocalDatabases
	 */
	public function testGetLocalDatabasesWhenWgLocalDatabasesIsNotSet() {
		// Ensure that $wgLocalDatabases is initially not set
		$this->assertNull($GLOBALS['wgLocalDatabases']);

		$result = WikiForgeFunctions::getLocalDatabases();
		$this->assertNull($result);
	}

	/**
	 * @covers ::getLocalDatabases
	 */
	public function testGetLocalDatabasesInCliMode() {
		// Simulate being in CLI mode
		$_SERVER['PHP_SAPI'] = 'cli';

		// Mock the readDbListFile method to return specific values
		$mockDatabases = ['db3', 'db4'];
		$this->mockReadDbListFile(['databases-farm', 'deleted-farm'], $mockDatabases);

		$result = WikiForgeFunctions::getLocalDatabases();
		$expectedResult = array_merge($mockDatabases, $mockDatabases);
		$this->assertEquals($expectedResult, $result);
	}

	/**
	 * @covers ::getLocalDatabases
	 */
	public function testGetLocalDatabasesNotInCliMode() {
		// Simulate not being in CLI mode
		$_SERVER['PHP_SAPI'] = 'fpm';

		// Mock the readDbListFile method to return specific values
		$mockDatabases = ['db5', 'db6'];
		$this->mockReadDbListFile(['databases-farm'], $mockDatabases);

		$result = WikiForgeFunctions::getLocalDatabases();
		$this->assertEquals($mockDatabases, $result);
	}

	private function mockReadDbListFile($fileNames, $returnValue) {
		$mock = $this->getMockBuilder(YourClassName::class)
			->setMethods(['readDbListFile'])
			->getMock();

		foreach ($fileNames as $index => $fileName) {
			$mock->expects($this->at($index))
				->method('readDbListFile')
				->with($fileName)
				->willReturn($returnValue);
		}

		// Replace the real method with the mock
		$this->replaceMethodWithMock($mock);

		return $mock;
	}

	private function replaceMethodWithMock($mock) {
		$reflectionClass = new ReflectionClass(WikiForgeFunctions::class);
		$method = $reflectionClass->getMethod('readDbListFile');
		$method->setAccessible(true);
		$method->setValue($mock, $mock->readDbListFile);
	}

	/**
	 * @covers ::getWikiFarm
	 */
	public function testGetWikiFarmReturnsString(): void {
		// Test when the current database is from 'wikiforge'
		$this->expectsMockedGetCurrentDatabase( 'testwiki' );
		$wikiFarm = WikiForgeFunctions::getWikiFarm();
		$this->assertEquals( 'wikiforge', $wikiFarm, "getWikiFarm should return 'wikiforge' when the current database is from 'wikiforge'" );

		// Test when the current database is from 'wikitide'
		$this->expectsMockedGetCurrentDatabase( 'testwikitide' );
		$wikiFarm = WikiForgeFunctions::getWikiFarm();
		$this->assertEquals( 'wikitide', $wikiFarm, "getWikiFarm should return 'wikitide' when the current database is from 'wikitide'" );

		// Test when the current database is not recognized
		$this->expectsMockedGetCurrentDatabase( 'wikitest' );
		$wikiFarm = WikiForgeFunctions::getWikiFarm();
		$this->assertEquals( 'wikitide', $wikiFarm, "getWikiFarm should return 'wikitide' when the current database is not recognized" );
	}

	private function expectsMockedGetCurrentDatabase( $returnValue ): void {
		$reflectionClass = new ReflectionClass( WikiForgeFunctions::class );
		$currentDatabaseProperty = $reflectionClass->getProperty( 'currentDatabase' );
		$currentDatabaseProperty->setAccessible( true );
		$currentDatabaseProperty->setValue( null );
		putenv( 'PHPUNIT_WIKI=' . $returnValue );
	}
}
