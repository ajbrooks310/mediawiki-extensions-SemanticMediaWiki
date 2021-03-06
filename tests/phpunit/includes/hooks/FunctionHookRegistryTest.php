<?php

namespace SMW\Test;

use SMW\FunctionHookRegistry;

/**
 * Tests for the FunctionHookRegistry class
 *
 * @file
 *
 * @license GNU GPL v2+
 * @since   1.9
 *
 * @author mwjames
 */

/**
 * @covers \SMW\FunctionHookRegistry
 *
 * @ingroup Test
 *
 * @group SMW
 * @group SMWExtension
 */
class FunctionHookRegistryTest extends SemanticMediaWikiTestCase {

	/**
	 * Returns the name of the class to be tested
	 *
	 * @return string|false
	 */
	public function getClass() {
		return '\SMW\FunctionHookRegistry';
	}

	/**
	 * Helper method that returns a FunctionHook object
	 *
	 * @since 1.9
	 *
	 * @return FunctionHook
	 */
	private function newHook() {
		return $this->getMockForAbstractClass( '\SMW\FunctionHook' );
	}

	/**
	 * Helper method that returns a FunctionHookRegistry object
	 *
	 * @since 1.9
	 *
	 * @return FunctionHookRegistry
	 */
	private function newInstance() {
		return new FunctionHookRegistry();
	}

	/**
	 * @test FunctionHookRegistry::__construct
	 *
	 * @since 1.9
	 */
	public function testConstructor() {
		$this->assertInstanceOf(
			'\SMW\FunctionHook',
			FunctionHookRegistry::register( $this->newHook() ),
			'Failed asserting FunctionHook instance'
		);
	}

}
