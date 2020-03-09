<div class="ms-wa">
	<div class="field">
		<label><?php _e( 'Phone Number', 'ms-wabroadcast' ); ?></label>
		<input class="widefat" type="text" name="_mswa_member_phone" value="<?php echo get_post_meta( $post->ID, '_mswa_member_phone', true ); ?>">
	</div>
	<div class="field">
		<label><?php _e( 'Email Address', 'ms-wabroadcast' ); ?></label>
		<input class="widefat" type="text" name="_mswa_member_email" value="<?php echo get_post_meta( $post->ID, '_mswa_member_email', true ); ?>">
	</div>
</div>