<?php defined( 'ABSPATH' ) || die( 'No direct access.' ); ?>
    <label class="screen-reader-text" for="excerpt"><?php _e( 'Excerpt' ) ?></label>
<?php
wp_editor(
	html_entity_decode( $post->post_excerpt ),
	'excerpt',
	[
		'editor_height' => 250,
		'wpautop'       => false,
		'media_buttons' => true,
		'teeny'         => false,
		'tinymce'       => [
                'toolbar3' => 'fontsizeselect',
                'fontsize_formats' => '8pt 9pt 10pt 11pt 12pt 13pt 14pt 15pt 16pt 18pt 20pt 22pt 24pt 26pt 30pt 36pt'
        ]
	]
);
