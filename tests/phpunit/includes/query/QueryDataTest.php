<?php

namespace SMW\Test;

use SMW\HashIdGenerator;
use SMW\QueryData;

use SMWQueryProcessor;
use Title;

/**
 * Tests for the QueryData class
 *
 * @file
 *
 * @license GNU GPL v2+
 * @since   1.9
 *
 * @author mwjames
 */

/**
 * @covers \SMW\QueryData
 *
 * @ingroup Test
 *
 * @group SMW
 * @group SMWExtension
 */
class QueryDataTest extends SemanticMediaWikiTestCase {

	/**
	 * Returns the name of the class to be tested
	 *
	 * @return string|false
	 */
	public function getClass() {
		return '\SMW\QueryData';
	}

	/**
	 * Helper method that returns a SMWQueryProcessor object
	 *
	 * @param array rawParams
	 *
	 * @return QueryProcessor
	 */
	private function getQueryProcessor( array $rawParams ) {
		return SMWQueryProcessor::getQueryAndParamsFromFunctionParams(
			$rawParams,
			SMW_OUTPUT_WIKI,
			SMWQueryProcessor::INLINE_QUERY,
			false
		);
	}

	/**
	 * Helper method that returns a QueryData object
	 *
	 * @param Title|null $title
	 *
	 * @return QueryData
	 */
	private function getInstance( Title $title = null ) {
		return new QueryData( $title );
	}

	/**
	 * @test QueryData::__construct
	 *
	 * @since 1.9
	 */
	public function testConstructor() {
		$instance = $this->getInstance( $this->getTitle() );
		$this->assertInstanceOf( $this->getClass(), $instance );
	}

	/**
	 * @test QueryData::getProperty
	 *
	 * @since 1.9
	 */
	public function testGetProperty() {
		$instance = $this->getInstance( $this->getTitle() );
		$this->assertInstanceOf( '\SMWDIProperty', $instance->getProperty() );
	}

	/**
	 * @test QueryData::getErrors
	 *
	 * @since 1.9
	 */
	public function testGetErrors() {
		$instance = $this->getInstance( $this->getTitle() );
		$this->assertInternalType( 'array', $instance->getErrors() );
	}

	/**
	 * @test QueryData::add
	 * @dataProvider queryDataProvider
	 *
	 * @since 1.9
	 *
	 * @param array $params
	 * @param array $expected
	 */
	public function testAddQueryData( array $params, array $expected ) {
		$title = $this->getTitle();
		$instance = $this->getInstance( $title );

		list( $query, $formattedParams ) = $this->getQueryProcessor( $params );
		$instance->setQueryId( new HashIdGenerator( $params ) );
		$instance->add( $query, $formattedParams );

		// Check the returned instance
		$this->assertInstanceOf( '\SMW\SemanticData', $instance->getContainer()->getSemanticData() );
		$this->assertSemanticData( $instance->getContainer()->getSemanticData(), $expected );
	}

	/**
	 * @test QueryData::add (Test instance exception)
	 * @dataProvider queryDataProvider
	 *
	 * @since 1.9
	 *
	 * @param array $params
	 * @param array $expected
	 * @throws MWException
	 */
	public function testQueryIdException( array $params, array $expected ) {

		$this->setExpectedException( '\SMW\UnknownIdException' );
		$title = $this->getTitle();
		$instance = $this->getInstance( $title );

		list( $query, $formattedParams ) = $this->getQueryProcessor( $params );
		$instance->add( $query, $formattedParams );

	}

	/**
	 * Provides data sample, the first array contains parametrized input
	 * value while the second array contains expected return results for the
	 * instantiated object.
	 *
	 * @return array
	 */
	public function queryDataProvider() {

		$provider = array();

		// #0
		// {{#ask: [[Modification date::+]]
		// |?Modification date
		// |format=list
		// }}
		$provider[] = array(
			array(
				'',
				'[[Modification date::+]]',
				'?Modification date',
				'format=list'
			),
			array(
				'propertyCount' => 4,
				'propertyKey' => array( '_ASKST', '_ASKSI', '_ASKDE', '_ASKFO' ),
				'propertyValue' => array( 'list', 1, 1, '[[Modification date::+]]' )
			)
		);

		// #1
		// {{#ask: [[Modification date::+]][[Category:Foo]]
		// |?Modification date
		// |?Has title
		// |format=list
		// }}
		$provider[] = array(
			array(
				'',
				'[[Modification date::+]][[Category:Foo]]',
				'?Modification date',
				'?Has title',
				'format=list'
			),
			array(
				'propertyCount' => 4,
				'propertyKey' => array( '_ASKST', '_ASKSI', '_ASKDE', '_ASKFO' ),
				'propertyValue' => array( 'list', 2, 1, '[[Modification date::+]] [[Category:Foo]]' )
			)
		);

		// #2 Unknown format, default table
		// {{#ask: [[Modification date::+]][[Category:Foo]]
		// |?Modification date
		// |?Has title
		// |format=bar
		// }}
		$provider[] = array(
			array(
				'',
				'[[Modification date::+]][[Category:Foo]]',
				'?Modification date',
				'?Has title',
				'format=bar'
			),
			array(
				'propertyCount' => 4,
				'propertyKey' => array( '_ASKST', '_ASKSI', '_ASKDE', '_ASKFO' ),
				'propertyValue' => array( 'table', 2, 1, '[[Modification date::+]] [[Category:Foo]]' )
			)
		);

		return $provider;
	}
}
