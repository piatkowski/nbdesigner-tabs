<?php
/**
 * Plugin Name: NBDesigner Tabs
 * Description: Create custom tabs in the NBDesigner's editor
 * Version: 1.0.0
 * Author: Krzysztof Piątkowski
 * License: GPL2
 */

namespace NBDesignerTabs;

defined( 'ABSPATH' ) || die( 'No direct access.' );

require_once __DIR__ . '/loader.php';

if ( ! class_exists( 'NBDesignerTabs\Plugin' ) ) {

	class Plugin {

		const VERSION = '1.0.0';

		private static $instance = null;

		private function __construct() {
			// private constructor
		}

		public static function getInstance() {
			if ( self::$instance === null ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		static function getPluginPath() {
			return __FILE__;
		}

		public function init() {
			Post::getInstance()->init();
		}
	}

	Plugin::getInstance()->init();

}