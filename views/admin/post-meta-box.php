<?php defined( 'ABSPATH' ) || die( 'No direct access.' ); ?>
<?php
global $post;
$data = htmlspecialchars_decode( stripslashes( get_post_meta( $post->ID, '_nbdf_data', true ) ), ENT_QUOTES );
if ( empty( $data ) ) {
	$data = '[]';
}
?>
<div id="nbdesigner-tabs-root">
    <input type="hidden" name="_nbdf_data" id="nbdf_data">
    <div class="fields"></div>
    <div class="toolbar">
        <button type="button" class="button" data-type="QuoteField">+ <?php _e( 'Pole tekstowe' ); ?></button>
        <button type="button" class="button" data-type="CheckboxField">+ <?php _e( 'Checkbox' ); ?></button>
    </div>
</div>
<script type="text/javascript">
    document.getElementById('nbdf_data').value = JSON.stringify(<?php echo $data; ?>);
</script>
<!-- <textarea id="nbdesigner-tabs-editor" class="wp-editor-area"></textarea> -->

