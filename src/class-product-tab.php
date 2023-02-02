<?php

namespace NBDesignerTabs;

final class ProductTab {

	private $tabs;

	/**
	 * @throws \Exception
	 */
	function __construct( $post_id, $states ) {
		if ( (int) $post_id === 0 ) {
			throw new \Exception( 'Invlid tab id' );
		}

		if ( get_post_type( $post_id ) !== 'product' ) {
			throw new \Exception( 'Post should be type of `product`' );
		}

		$product      = wc_get_product( $post_id );
		$categories   = $product->get_category_ids();
		$product_tabs = $product->get_meta( '_nbdt_product_tabs' );
		if ( ! is_array( $product_tabs ) ) {
			$product_tabs = [];
		}
		unset( $product );

		$tabs = [];

		$tab_posts = get_posts( [
			'post_type'   => Post::POST_TYPE,
			'post_status' => 'publish',
			'numberposts' => - 1
		] );

		foreach ( $tab_posts as $tab_post ) {
			$tab_instance      = new Tab( $tab_post->ID );
			$common_categories = array_intersect( $categories, $tab_instance->get_categories() );
			if ( ! empty( $common_categories ) ) {
				$tabs[ $tab_post->ID ] = $tab_instance;
			}
			unset( $tab_instance );
		}

		$states = is_array( $states ) ? $states : [];

		foreach ( $product_tabs as $tab_id ) {
			if ( ! isset( $tabs[ $tab_id ] ) ) {
				$tabs[ $tab_id ] = new Tab( $tab_id );

				if ( isset( $states[ (int) $tab_id ] ) ) {
					$tabs[ $tab_id ]->set_state( $states[ (int) $tab_id ] );
				}
			}
		}

		unset( $product_tabs );
		unset( $tab_posts );

		$this->tabs = $tabs;
	}

	function get_tabs() {
		return $this->tabs;
	}

	function get_tab( $tab_id ) {
		return $this->tabs[ $tab_id ] ?? null;
	}

}