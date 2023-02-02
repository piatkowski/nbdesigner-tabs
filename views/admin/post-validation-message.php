<?php defined( 'ABSPATH' ) || die( 'No direct access.' ); ?>
<?php
global $post;
$validation_message = stripslashes( get_post_meta( $post->ID, '_nbdf_validation_message', true ) );
?>
<p>Wpisz treść komunikatu o błędzie w przypadku, gdy Klient nie zaznaczył żadnego checkboxa w obrębie tej zakładki.</p>
<textarea name="_nbdf_validation_message" id="nbdf_validation_message"><?php echo esc_html($validation_message); ?></textarea>