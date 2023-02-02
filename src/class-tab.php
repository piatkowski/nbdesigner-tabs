<?php

namespace NBDesignerTabs;

class Tab {
	/**
	 * @var int Tab ID
	 */
	protected $id;
	/**
	 * @var array Tab's fields
	 */
	protected $fields;
	/**
	 * @var array Tab is shown under matching $categories
	 */
	protected $categories;

	/**
	 * @var string URL to image file from WP media directory
	 */
	protected $thumbnail;

	/**
	 * @var string Tab title
	 */
	protected $title;

	/**
	 * @var string Tab description
	 */
	protected $description;

	/**
	 * @var string Message to the user
	 */
	protected $validation_message;

	/**
	 * @throws \Exception
	 */
	function __construct( $post_id ) {
		if ( (int) $post_id === 0 ) {
			throw new \Exception( 'Invlid tab id' );
		}

		if ( get_post_type( $post_id ) !== Post::POST_TYPE ) {
			throw new \Exception( 'Post should be type of ' . Post::POST_TYPE );
		}

		$this->id = $post_id;

		$fields = stripslashes( get_post_meta( $post_id, '_nbdf_data', true ) );
		$fields = json_decode( $fields, true );
		if ( is_array( $fields ) ) {
			$this->initialize_fields( $fields );
		}
		$categories       = get_post_meta( $post_id, '_nbdf_categories', true );
		$this->categories = is_array( $categories ) ? $categories : [];

		$this->thumbnail = get_the_post_thumbnail_url( $post_id, 'full' );

		$post              = get_post( $post_id );
		$this->description = $post->post_excerpt;
		$this->title       = $post->post_title;
		unset( $post );

		$this->validation_message = (string) get_post_meta( $post_id, '_nbdf_validation_message', true );
	}

	/**
	 * Creates field's instances in $field array
	 *
	 * @param $fields
	 *
	 * @return void
	 * @throws \Exception
	 */
	private function initialize_fields( $fields ) {
		foreach ( $fields as $field ) {
			if ( ! isset( $field['id'] ) || ! isset( $field['type'] ) || ! isset( $field['data'] ) ) {
				continue;
			}
			$this->fields[ $field['id'] ] = Field::newInstance(
				$field['id'],
				$field['type'],
				$field['data']
			);
		}
	}

	/**
	 * @return int
	 */
	function get_id() {
		return $this->id;
	}

	/**
	 * @return array
	 */
	function get_fields() {
		return $this->fields;
	}

	/**
	 * @return array
	 */
	function get_categories() {
		return $this->categories;
	}

	/**
	 * @return false|string
	 */
	function get_thumbnail() {
		return $this->thumbnail;
	}

	/**
	 * @return string
	 */
	function get_title() {
		return $this->title;
	}

	/**
	 * @return string
	 */
	function get_description() {
		return $this->description;
	}

	/**
	 * @return string
	 */
	function get_validation_message() {
		return $this->validation_message;
	}

	/**
	 * Set state to all fields
	 *
	 * @param $states
	 *
	 * @return void
	 */
	function set_state( $states ) {
		if ( ! is_array( $states ) ) {
			return;
		}
		foreach ( $states as $field_id => $field_state ) {
			if ( ! isset( $this->fields[ $field_id ] ) ) {
				continue;
			}
			$this->fields[ $field_id ]->set_state( $field_state );
		}
	}
}