<?php

namespace SMW\Test;

use SMW\UnusedPropertiesQueryPage;
use SMW\MessageFormatter;

use SMWDataItem;

/**
 * Tests for the UnusedPropertiesQueryPage class
 *
 * @file
 *
 * @license GNU GPL v2+
 * @since   1.9
 *
 * @author mwjames
 */

/**
 * @covers \SMW\UnusedPropertiesQueryPage
 * @covers \SMW\QueryPage
 *
 * @ingroup Test
 *
 * @group SMW
 * @group SMWExtension
 */
class UnusedPropertiesQueryPageTest extends SemanticMediaWikiTestCase {

	/**
	 * Returns the name of the class to be tested
	 *
	 * @return string|false
	 */
	public function getClass() {
		return '\SMW\UnusedPropertiesQueryPage';
	}

	/**
	 * Helper method that returns a DIWikiPage object
	 *
	 * @since 1.9
	 *
	 * @return DIWikiPage
	 */
	private function getMockDIWikiPage( $exists = true ) {

		$text  = $this->newRandomString();

		$title = $this->newMockObject( array(
			'exists'  => $exists,
			'getText' => $text,
			'getNamespace'    => NS_MAIN,
			'getPrefixedText' => $text
		) )->getMockTitle();

		$diWikiPage = $this->newMockObject( array(
			'getTitle'  => $title,
		) )->getMockDIWikiPage();

		return $diWikiPage;
	}

	/**
	 * Helper method that returns a UnusedPropertiesQueryPage object
	 *
	 * @since 1.9
	 *
	 * @param $result
	 * @param $values
	 *
	 * @return UnusedPropertiesQueryPage
	 */
	private function newInstance( $result = null, $values = array() ) {

		$collector = $this->newMockObject( array(
			'getResults' => $result
		) )->getMockCacheableObjectCollector();

		$mockStore = $this->newMockObject( array(
			'getPropertyValues'          => $values,
			'getUnusedPropertiesSpecial' => $collector
		) )->getMockStore();

		$instance = new UnusedPropertiesQueryPage( $mockStore, $this->newSettings() );
		$instance->setContext( $this->newContext() );

		return $instance;
	}

	/**
	 * @test UnusedPropertiesQueryPage::__construct
	 *
	 * @since 1.9
	 */
	public function testConstructor() {
		$this->assertInstanceOf( $this->getClass(), $this->newInstance() );
	}

	/**
	 * @test UnusedPropertiesQueryPage::formatResult
	 * @dataProvider getUserDefinedDataProvider
	 *
	 * @since 1.9
	 */
	public function testFormatResult( $isUserDefined ) {

		// Skin stub object
		$skin = $this->getMock( 'Skin' );

		// DIProperty
		$instance = $this->newInstance();

		$property = $this->newMockObject( array(
			'isUserDefined' => $isUserDefined,
			'getDiWikiPage' => $this->getMockDIWikiPage( true ),
			'getLabel'      => $this->newRandomString(),
		) )->getMockDIProperty();

		$expected = $property->getDiWikiPage()->getTitle()->getText();
		$result   = $instance->formatResult( $skin, $property );

		$this->assertInternalType( 'string', $result );
		$this->assertContains( $expected, $result );

		// Multiple entries
		$instance = $this->newInstance();
		$multiple = array( $this->getMockDIWikiPage(), $this->getMockDIWikiPage() );

		$property = $this->newMockObject( array(
			'isUserDefined' => $isUserDefined,
			'getDiWikiPage' => $this->getMockDIWikiPage( true ),
			'getLabel'      => $this->newRandomString(),
		) )->getMockDIProperty();

		$expected = $property->getDiWikiPage()->getTitle()->getText();
		$instance = $this->newInstance( null, $multiple );

		$result   = $instance->formatResult( $skin, $property );

		$this->assertInternalType( 'string', $result );
		$this->assertContains( $expected, $result );

		// DIError
		$instance = $this->newInstance();
		$error    = $this->newRandomString();

		$result   = $instance->formatResult(
			$skin,
			$this->newMockobject( array( 'getErrors' => $error ) )->getMockDIError()
		);

		$this->assertInternalType( 'string', $result );
		$this->assertContains( $error, $result );

	}

	/**
	 * @test UnusedPropertiesQueryPage::formatResult
	 *
	 * @since 1.9
	 */
	public function testInvalidResultException() {

		$this->setExpectedException( '\SMW\InvalidResultException' );

		$instance = $this->newInstance();
		$skin = $this->getMock( 'Skin' );

		$this->assertInternalType( 'string', $instance->formatResult( $skin, null ) );

	}

	/**
	 * isUserDefined switcher
	 *
	 * @return array
	 */
	public function getUserDefinedDataProvider() {
		return array( array( true ), array( false ) );
	}

	/**
	 * @test SMWUnusedPropertiesPage::getResults
	 *
	 * @since 1.9
	 */
	public function testGetResults() {

		$expected = 'Lala';

		$instance = $this->newInstance( $expected );
		$this->assertEquals( $expected, $instance->getResults( null ) );

	}
}
