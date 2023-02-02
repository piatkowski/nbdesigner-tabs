<?php defined( 'ABSPATH' ) || die( 'No direct access.' ); ?>

<div class="categorydiv">
    <ul class="categorychecklist form-no-clear">
		<?php
		$saved_categories = get_post_meta( $post->ID, '_nbdf_categories', true );
		if ( empty( $saved_categories ) || ! is_array( $saved_categories ) ) {
			$saved_categories = [];
		}
		$checklist = wp_terms_checklist(
			0,
			[
				'taxonomy'      => 'product_cat',
				'echo'          => false,
				'selected_cats' => $saved_categories
			]
		);
		echo str_replace( 'tax_input[product_cat][]', 'nbdt_categories[]', $checklist );
		?>
    </ul>
</div>

