<?php

namespace SMW\Test;

use SMWFactbox;
use SMW\Settings;

use Title;
use ParserOutput;

/**
 * Tests for the SMWFactbox class
 *
 * @since 1.9
 *
 * @file
 * @ingroup SMW
 * @ingroup Test
 *
 * @licence GNU GPL v2+
 * @author mwjames
 */

/**
 * Tests for the SMWFactbox class
 *
 * @ingroup Test
 *
 * @group SMW
 * @group SMWExtension
 */
class FactboxTest extends ParserTestCase {

	/**
	 * Returns the name of the class to be tested
	 *
	 * @return string
	 */
	public function getClass() {
		return '\SMWFactbox';
	}

	/**
	 * Provides text sample together with the expected magic word and an
	 * indication of a possible output string
	 *
	 * @return array
	 */
	public function getTextProvider() {
		return array(
			// #0 __NOFACTBOX__, this test should not generate a factbox output
			array(
				'Lorem ipsum dolor sit amet consectetuer auctor at quis' .
				' [[Foo::dictumst cursus]]. Nisl sit condimentum Quisque facilisis' .
				' Suspendisse [[Bar::tincidunt semper]] facilisi dolor Aenean. Ut' .
				' __NOFACTBOX__ ',
				array(
					'magicWords' => array( 'SMW_NOFACTBOX' ),
					'textOutput' => ''
				)
			),

			// #1 __SHOWFACTBOX__, this test should generate a factbox output
			array(
				'Lorem ipsum dolor sit amet consectetuer auctor at quis' .
				' [[Foo::dictumst cursus]]. Nisl sit condimentum Quisque facilisis' .
				' Suspendisse [[Bar::tincidunt semper]] facilisi dolor Aenean. Ut' .
				' __SHOWFACTBOX__',
				array(
					'magicWords' => array( 'SMW_SHOWFACTBOX' ),
					'textOutput' => 'smwfactboxhead' // lazy check because we use assertContains
				)
			),
		);
	}

	/**
	 * Helper method that returns a Settings object
	 *
	 * @param $title
	 *
	 * @return Settings
	 */
	private function getSettingsForTitle( Title $title ) {
		$settings =array(
			'smwgNamespacesWithSemanticLinks' => array( $title->getNamespace() => true ),
			'smwgLinksInValues' => false,
			'smwgInlineErrors' => true,
		);
		return $this->getSettings( $settings );
	}

	/**
	 * @test ParserTextProcessor::parse
	 * @dataProvider getTextProvider
	 *
	 * @since 1.9
	 *
	 * @param $text
	 * @param array $expected
	 */
	public function testMagicWordsOutput( $text, array $expected ) {
		$title = $this->getTitle();
		$settings = $this->getSettingsForTitle( $title );
		$parserOutput  = $this->getParserOutput();
		$textProcessor = $this->getParserTextProcessor(
			$title,
			$parserOutput,
			$settings
		);

		// Use the text processor to add text sample
		$textProcessor->parse( $text );

		// Check the magic words stripped and added by the text processor
		if ( method_exists( $parserOutput, 'getExtensionData' ) ) {
			$this->assertEquals(
				$expected['magicWords'],
				$parserOutput->getExtensionData( 'smwmagicwords' )
			);
		} else {
			$this->assertEquals(
				$expected['magicWords'],
				$parserOutput->mSMWMagicWords
			);
		}
	}

	/**
	 * @test SMWFactbox::getFactboxTextFromOutput
	 * @dataProvider getTextProvider
	 *
	 * @since 1.9
	 *
	 * @param $text
	 * @param array $expected
	 */
	public function testGetFactboxTextFromOutput( $text, array $expected ) {
		$title = $this->getTitle();
		$settings = $this->getSettingsForTitle( $title );
		$parserOutput  = $this->getParserOutput();
		$textProcessor = $this->getParserTextProcessor(
			$title,
			$parserOutput,
			$settings
		);

		// Use the text processor to add text sample
		$textProcessor->parse( $text );

		$result = SMWFactbox::getFactboxTextFromOutput( $parserOutput, $title );
		$this->assertInternalType( 'string', $result );

		// Doing a lazy sanity check on the result
		if ( $expected['textOutput'] !== '' ) {
			$this->assertContains( $expected['textOutput'], $result );
		} else {
			$this->assertEmpty( $result );
		}
	}
}
