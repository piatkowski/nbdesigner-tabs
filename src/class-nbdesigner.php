<?php

namespace NBDesignerTabs;

class NBDesigner {

	private static $instance = null;

	private function __construct() {
		// private constructor
	}

	static function getInstance() {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}