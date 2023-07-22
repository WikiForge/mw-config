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

	private function expectsMockedGetCurrentDatabase($returnValue): void {
		$reflectionClass = new ReflectionClass(WikiForgeFunctions::class);
		$currentDatabaseProperty = $reflectionClass->getProperty('currentDatabase');
		$currentDatabaseProperty->setAccessible(true);
		$currentDatabaseProperty->setValue(null);
		putenv( 'PHPUNIT_WIKI=' . $returnValue);
	}
}
