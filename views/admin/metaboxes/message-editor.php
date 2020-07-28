<div class="fonnletter">
	<div class="field">
		<label><?php _e( 'Dikirim setelah hari ke X user terdaftar.', 'fonnletter' ); ?></label>
		<input type="number" min="1" step="1" name="_fonnletter_day_to_send" class="widefat" placeholder="1" value="<?php echo get_post_meta( $post->ID, '_fonnletter_day_to_send', true ); ?>">
	</div>
</div>