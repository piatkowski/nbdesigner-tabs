<?php defined( 'ABSPATH' ) || die( 'No direct access.' ); ?>
    <label class="screen-reader-text" for="excerpt"><?php _e( 'Excerpt' ) ?></label>
<?php
wp_editor(
	wp_specialchars_decode( $post->post_excerpt ),
	'excerpt',
	[
		'editor_height' => 250,
		'wpautop'       => false,
		'media_buttons' => false,
		'teeny'         => true,
		'tinymce'       => true
	]
);
