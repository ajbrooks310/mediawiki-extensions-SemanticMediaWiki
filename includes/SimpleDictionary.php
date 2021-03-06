<?php

namespace SMW;

use InvalidArgumentException;
use OutOfBoundsException;

/**
 * Class handling the implementation of a dictionary
 *
 * @file
 *
 * @license GNU GPL v2+
 * @since   1.9
 *
 * @author mwjames
 */

/**
 * Implementing a dictionary (associative array, hash array) which is a
 * collections of key, value pairs.
 *
 * @note Type casting ( (string)$key === $key ) is used instead of is_string()
 * because of measurable performance implications
 *
 * @par Example:
 * @code
 * $dictionary = new SimpleDictionary( array( 'Foo' => 'Bar' ) );
 *
 * $dictionary->has( 'Foo' ) returns true
 * $dictionary->get( 'Foo' ) returns 'Bar'
 * $dictionary->set( 'Foo', array( 'Lula', 'Bar') )
 * $dictionary->remove( 'Foo' )
 * @endcode
 *
 * @ingroup Utility
 */
class SimpleDictionary extends ObjectStorage implements ObjectDictionary {

	/**
	 * Returns whether a specific element is accessible
	 *
	 * @since 1.9
	 *
	 * @param mixed $key
	 *
	 * @return boolean
	 * @throws InvalidArgumentException
	 */
	public function has( $key ) {

		if ( !( (string)$key === $key ) ) {
			throw new InvalidArgumentException( 'The invoked key is not a string' );
		}

		return $this->contains( $key );
	}

	/**
	 * Adds a new element (key, value pair) to an existing collection
	 *
	 * @since 1.9
	 *
	 * @param string $key
	 * @param mixed $value
	 *
	 * @return HashArray
	 * @throws InvalidArgumentException
	 */
	public function set( $key, $value ) {

		if ( !( (string)$key === $key ) ) {
			throw new InvalidArgumentException( 'The invoked key is not a string' );
		}

		$this->attach( $key, $value );
		return $this;
	}

	/**
	 * Returns a container value
	 *
	 * @since 1.9
	 *
	 * @param string $key
	 *
	 * @return mixed
	 * @throws OutOfBoundsException
	 */
	public function get( $key ) {

		if ( !( $this->has( $key ) ) ) {
			throw new OutOfBoundsException( "'{$key}' is unknown" );
		}

		return $this->lookup( $key );
	}

	/**
	 * Removes an element from a collection
	 *
	 * @since 1.9
	 *
	 * @param string $key
	 *
	 * @return HashArray
	 * @throws InvalidArgumentException
	 */
	public function remove( $key ) {

		if ( !( (string)$key === $key ) ) {
			throw new InvalidArgumentException( 'The invoked key is not a string' );
		}

		$this->detach( $key );
		return $this;
	}

	/**
	 * Returns invoked array without conversion
	 *
	 * @since 1.9
	 *
	 * @return array
	 */
	public function toArray() {
		return $this->storage;
	}

	/**
	 * Merges elements of one or more arrays together
	 *
	 * @since 1.9
	 *
	 * @param array $mergeable
	 *
	 * @return HashArray
	 */
	public function merge( array $mergeable ) {
		$this->storage = array_merge( $this->storage, $mergeable );
		return $this;
	}

}
