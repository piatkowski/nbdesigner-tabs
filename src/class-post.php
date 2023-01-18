<?php

namespace NBDesignerTabs;

class Post {

	const POST_TYPE = 'nbd_tabs_post';
	const META_PREFIX = 'nbdtp_';

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

	function init() {
		add_action( 'init', [ $this, 'register_post_type' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ], 11 );
		add_action( 'save_post_' . self::POST_TYPE, [ $this, 'save_post' ], 10, 2 );
	}

	function register_post_type() {
		if ( post_type_exists( self::POST_TYPE ) ) {
			return;
		}

		register_post_type( self::POST_TYPE,
			[
				'labels'               => [
					'name' => __( 'Zakładki', 'nbdesigner-tabs' )
				],
				'public'               => false,
				'hierarchical'         => false,
				'show_ui'              => true,
				'has_archive'          => false,
				'map_meta_cap'         => true,
				'capability_type'      => 'post',
				'capabilities'         => [ 'manage_woocommerce' ],
				'publicly_queryable'   => false,
				'exclude_from_search'  => true,
				'query_var'            => true,
				'show_in_nav_menus'    => false,
				'show_in_menu'         => 'nbdesigner',
				'delete_with_user'     => false,
				'supports'             => [ 'title', 'excerpt', 'thumbnail' ],
				'register_meta_box_cb' => [ $this, 'register_meta_box' ]
			]
		);
	}

	function register_meta_box() {
		remove_meta_box(
			'postexcerpt',
			self::POST_TYPE,
			'normal'
		);

		add_meta_box(
			self::META_PREFIX . 'postexcerpt',
			__( 'Opis zakładki', 'nbdesigner-tabs' ),
			function ( $post, $data ) {
				include __DIR__ . '/../views/admin/post-excerpt.php';
			},
			self::POST_TYPE,
			'advanced',
			'high'
		);

		add_meta_box(
			self::META_PREFIX . 'main',
			__( 'Tworzenie zakładki', 'nbdesigner-tabs' ),
			function ( $post, $data ) {
				include __DIR__ . '/../views/admin/post-meta-box.php';
			},
			self::POST_TYPE,
			'advanced',
			'high'
		);

	}

	function enqueue_assets( $hook ) {
		global $post;
		if ( $post && $post->post_type === self::POST_TYPE ) {
			wp_enqueue_editor();
			wp_enqueue_script('jquery-ui-core');
			wp_enqueue_script('jquery-ui-sortable');
			wp_enqueue_script(
				self::META_PREFIX . 'admin_js',
				plugins_url( 'assets/js/bundle.js', Plugin::getPluginPath() ),
				[ 'jquery', 'jquery-ui-core', 'jquery-ui-sortable' ],
				time() //Plugin::VERSION
			);
			wp_register_style(
				self::META_PREFIX . 'admin_css',
				plugins_url( 'assets/css/admin.min.css', Plugin::getPluginPath() )
			);
			wp_enqueue_style( self::META_PREFIX . 'admin_css' );
		}
	}

	function save_post( $post_id, $post ) {
		if ( isset( $_POST['_nbdf_data'] ) ) {
			$data = wp_slash( sanitize_text_field( $_POST['_nbdf_data'] ) );
			update_post_meta( $post_id, '_nbdf_data', $data );
		}
	}

}
