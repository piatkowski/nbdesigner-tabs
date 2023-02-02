<?php

namespace NBDesignerTabs;

/*
 * [{"id":"180_58","type":"QuoteField","data":{"title":"Wierszyk #1","content":"Lorem Ipsum is simply\ndummy te\"xt of the printing\nand typesetting industry. \nLorem Ipsum has been \nthe industry's standard \ndummy text ever since \nthe 1500s, when an un."}},{"id":"403_11","type":"QuoteField","data":{"title":"Wierszyk #2","content":"Lorem Ipsum is simply\ndummy te\"xt of the printing\nand typesetting industry. \nLorem Ipsum has been \nthe industry's standard \ndummy text ever since \nthe 1500s, when an un."}}]
 */

abstract class Field {

	/**
	 * @var string
	 */
	protected $id = '';

	/**
	 * @var array internal field data (for example label, content)
	 */
	protected $data = [];

	/**
	 * @var mixed field state from user
	 */
	protected $state;

	/**
	 * @param $id
	 * @param $data
	 *
	 * @throws \Exception
	 */
	protected function __construct( $id, $data ) {
		if ( empty( $id ) || empty( $data ) || ! is_array( $data ) ) {
			throw new \Exception( 'Field needs $id and $data array' );
		}
		$this->id    = (string) $id;
		$this->data  = $data;
	}


	/**
	 * Factory method to create field instance
	 *
	 * @param $id
	 * @param $type
	 * @param $data
	 *
	 * @return CheckboxField|QuoteField
	 * @throws \Exception
	 */
	static function newInstance( $id, $type, $data ) {
		switch ( $type ) {
			case 'QuoteField':
				return new QuoteField( $id, $data );
			case 'CheckboxField':
				return new CheckboxField( $id, $data );
		}
		throw new \Exception( 'Unknown field type!' );
	}

	/**
	 * Set field's state
	 *
	 * @param $state
	 *
	 * @return void
	 */
	function set_state( $state ) {
		$this->state = $state;
	}

	abstract function render();
}
