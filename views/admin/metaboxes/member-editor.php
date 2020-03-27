<div class="fonnletter">
	<div class="field">
		<label><?php _e( 'Campaign', 'fonnletter' ); ?></label>
		<select name="_fonnletter_member_campaign_id" class="widefat">
			<?php
				$q = new WP_Query( array( 'post_type' => 'ms_wa_campaign', 'posts_per_page' => -1 ) );
			?>
			<option value="">No Campaign</option>
			<?php if ( $q->have_posts() ) {
				while ( $q->have_posts() ) {
					$q->the_post();
					?>
					<option value="<?php the_ID(); ?>" <?php selected( get_post_meta( $post->ID, '_fonnletter_member_campaign_id', true ), get_the_ID() ); ?>><?php the_title(); ?></option>
					<?php
				}
			}; wp_reset_postdata(); ?>
		</select>
	</div>
	<div class="field">
		<label><?php _e( 'Status', 'fonnletter' ); ?></label>
		<select name="_fonnletter_member_status" class="widefat">
			<option value="inactive" <?php selected( get_post_meta( $post->ID, '_fonnletter_member_status', true ), 'inactive' ) ?>>Not Active</option>
			<option value="active" <?php selected( get_post_meta( $post->ID, '_fonnletter_member_status', true ), 'active' ) ?>>Active</option>
		</select>
	</div>
	<div class="field">
		<label><?php _e( 'Phone Number', 'fonnletter' ); ?></label>
		<input class="widefat" type="text" name="_fonnletter_member_phone" value="<?php echo get_post_meta( $post->ID, '_fonnletter_member_phone', true ); ?>">
	</div>
	<div class="field">
		<label><?php _e( 'Email Address', 'fonnletter' ); ?></label>
		<input class="widefat" type="text" name="_fonnletter_member_email" value="<?php echo get_post_meta( $post->ID, '_fonnletter_member_email', true ); ?>">
	</div>
</div>