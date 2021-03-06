<?php

namespace SMW\Test;

use SMW\ApiQueryResultFormatter;
use SMW\ArrayAccessor;

use SMWQueryResult;

/**
 * Tests for the ApiQueryResultFormatter class
 *
 * @file
 *
 * @license GNU GPL v2+
 * @since   1.9
 *
 * @author mwjames
 */

/**
 * @covers \SMW\ApiQueryResultFormatter
 *
 * @ingroup Test
 *
 * @group SMW
 * @group SMWExtension
 */
class ApiQueryResultFormatterTest extends SemanticMediaWikiTestCase {

	/**
	 * Returns the name of the class to be tested
	 *
	 * @return string|false
	 */
	public function getClass() {
		return '\SMW\ApiQueryResultFormatter';
	}

	/**
	 * Helper method that returns a ApiQueryResultFormatter object
	 *
	 * @since 1.9
	 *
	 * @param SMWQueryResult $queryResult
	 *
	 * @return ApiQueryResultFormatter
	 */
	private function getInstance( SMWQueryResult $queryResult ) {
		return new ApiQueryResultFormatter( $queryResult );
	}

	/**
	 * @test ApiQueryResultFormatter::__construct
	 *
	 * @since 1.9
	 */
	public function testConstructor() {
		$this->assertInstanceOf(
			$this->getClass(),
			$this->getInstance( $this->newMockObject()->getMockQueryResult() )
		);
	}

	/**
	 * @test ApiQueryResultFormatter::setIndexedTagName
	 * @test ApiQueryResultFormatter::setIsRawMode
	 *
	 * @since 1.9
	 */
	public function testSetIndexedTagNameException() {

		$this->SetExpectedException( 'InvalidArgumentException' );

		$instance = $this->getInstance( $this->newMockObject()->getMockQueryResult() );
		$instance->setIsRawMode( true );
		$index = array();

		$instance->setIndexedTagName( $index, null );
		$this->assertTrue( true );

	}

	/**
	 * @test ApiQueryResultFormatter::doFormat
	 * @test ApiQueryResultFormatter::setIsRawMode
	 * @test ApiQueryResultFormatter::setFormat
	 * @test ApiQueryResultFormatter::getResult
	 * @test ApiQueryResultFormatter::getContinueOffset
	 * @dataProvider resultDataProvider
	 *
	 * @since 1.9
	 *
	 * @param array $test
	 * @param array $expected
	 */
	public function testResultFormat( array $test, array $expected ) {

		$queryResult = $this->newMockObject( array(
			'toArray'           => $test['result'],
			'getErrors'         => array(),
			'hasFurtherResults' => $test['furtherResults']
		) )->getMockQueryResult();

		$instance = $this->getInstance( $queryResult );
		$instance->setIsRawMode( $test['rawMode'] );
		$instance->setFormat( $test['format'] );
		$instance->doFormat();

		$this->assertEquals( 'query', $instance->getType() );
		$this->assertEquals( $expected['result'], $instance->getResult() );
		$this->assertEquals( $expected['continueOffset'], $instance->getContinueOffset() );

	}

	/**
	 * @test ApiQueryResultFormatter::doFormat
	 * @test ApiQueryResultFormatter::setIsRawMode
	 * @test ApiQueryResultFormatter::setFormat
	 * @test ApiQueryResultFormatter::getResult
	 * @dataProvider errorDataProvider
	 *
	 * @since 1.9
	 *
	 * @param array $test
	 * @param array $expected
	 */
	public function testErrorFormat( array $test, array $expected ) {

		$queryResult = $this->newMockObject( array(
			'toArray'           => array(),
			'getErrors'         => $test['errors'],
			'hasFurtherResults' => false
		) )->getMockQueryResult();

		$instance = $this->getInstance( $queryResult );

		$instance->setIsRawMode( $test['rawMode'] );
		$instance->setFormat( $test['format'] );
		$instance->doFormat();

		$this->assertEquals( 'error', $instance->getType() );
		$this->assertEquals( $expected, $instance->getResult() );

	}

	/**
	 * Provides a query array and its expected printrequest array
	 *
	 * @return array
	 */
	public function resultDataProvider() {
		$result = array(
			'results' => array(
				'Foo' => array(
					'printouts' => array( 'lula' => array( 'lila' ) )
				)
			),
			'printrequests' => array( 'Bar' ),
			'meta' => array( 'count' => 5, 'offset' => 5 )
		);

		$xml = array(
			'results' => array(
				array(
					'printouts' => array(
						array( 'label' => 'lula', 'lila', '_element' => 'value'	),
						'_element' => 'property' )
					),
					'_element' => 'subject'
				),
			'printrequests' => array(
				'Bar',
				'_element' => 'printrequest'
			),
			'meta' => array( 'count' => 5, 'offset' => 5, '_element' => 'meta' )
		);

		return array(

			// #0 Without further results
			array(
				array(
					'result'  => $result,
					'rawMode' => false,
					'format'  => 'lala',
					'furtherResults' => false
				),
				array(
					'result' => $result,
					'continueOffset' => false
				)
			),

			// #1 Without further results + XML
			array(
				array(
					'result'  => $result,
					'rawMode' => true,
					'format'  => 'XML',
					'furtherResults' => false
				),
				array(
					'result' => $xml,
					'continueOffset' => false
				)
			),

			// #2 With further results
			array(
				array(
					'result' => $result,
					'rawMode' => false,
					'format'  => 'lala',
					'furtherResults' => true
				),
				array(
					'result' => $result,
					'continueOffset' => 10
				)
			),

			// #3 With further results + XML
			array(
				array(
					'result' => $result,
					'rawMode' => true,
					'format'  => 'XML',
					'furtherResults' => true
				),
				array(
					'result' => $xml,
					'continueOffset' => 10
				)
			),

			// #4 Extended subject data + XML
			array(
				array(

					'result' => array(
						'results' => array(
							'Foo' => array(
								'printouts' => array(
									'lula' => array( 'lila' ) ),
								'fulltext' => 'Foo' )
							),
						'printrequests' => array( 'Bar' ),
						'meta' => array(
							'count' => 5,
							'offset' => 5
						)
					),
					'rawMode' => true,
					'format'  => 'XML',
					'furtherResults' => true
				),
				array(
					'result' =>  array(
						'results' => array(
							array(
								'printouts' => array(
									array(
										'label' => 'lula',
										'lila', '_element' => 'value'
									), '_element' => 'property' ),
								'fulltext' => 'Foo'
								), '_element' => 'subject'
							),
						'printrequests' => array( 'Bar', '_element' => 'printrequest' ),
						'meta' => array(
							'count' => 5,
							'offset' => 5,
							'_element' => 'meta'
						)
					),
					'continueOffset' => 10
				)
			),

			// #5 printouts without values + XML
			array(
				array(

					'result' => array(
						'results' => array(
							'Foo' => array(
								'printouts' => array( 'lula' ),
								'fulltext' => 'Foo' )
							),
						'printrequests' => array( 'Bar' ),
						'meta' => array(
							'count' => 5,
							'offset' => 5
						)
					),
					'rawMode' => true,
					'format'  => 'XML',
					'furtherResults' => true
				),
				array(
					'result' =>  array(
						'results' => array(
							array(
								'printouts' => array( '_element' => 'property' ),
								'fulltext' => 'Foo'
								),
							'_element' => 'subject'
							),
						'printrequests' => array( 'Bar', '_element' => 'printrequest' ),
						'meta' => array(
							'count' => 5,
							'offset' => 5,
							'_element' => 'meta'
						)
					),
					'continueOffset' => 10
				)
			),

			// #6 empty results + XML
			array(
				array(

					'result' => array(
						'results' => array(),
						'printrequests' => array( 'Bar' ),
						'meta' => array(
							'count' => 0,
							'offset' => 0
						)
					),
					'rawMode' => true,
					'format'  => 'XML',
					'furtherResults' => false
				),
				array(
					'result' =>  array(
						'results' => array(),
						'printrequests' => array( 'Bar', '_element' => 'printrequest' ),
						'meta' => array(
							'count' => 0,
							'offset' => 0,
							'_element' => 'meta'
						)
					),
					'continueOffset' => 0
				)
			)

		);
	}

	/**
	 * Provides error samples
	 *
	 * @return array
	 */
	public function errorDataProvider() {
		$errors = array( 'Foo', 'Bar' );

		return array(

			// #0
			array(
				array(
					'rawMode'=> false,
					'format' => 'lala',
					'errors' => $errors
				),
				array(
					'query' => $errors
				)
			),

			// #1
			array(
				array(
					'rawMode'=> true,
					'format' => 'lala',
					'errors' => $errors
				),
				array(
					'query' => $errors
				)
			),

			// #2
			array(
				array(
					'rawMode'=> false,
					'format' => 'XML',
					'errors' => $errors
				),
				array(
					'query' => $errors
				)
			),

			// #3
			array(
				array(
					'rawMode'=> true,
					'format' => 'XML',
					'errors' => $errors
				),
				array(
					'query' => array_merge( $errors, array( '_element' => 'info' ) )
				)
			)
		);
	}
}
