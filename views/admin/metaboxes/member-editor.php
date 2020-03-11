<div class="ms-wa">
	<div class="field">
		<label><?php _e( 'Campaign', 'ms-wabroadcast' ); ?></label>
		<select name="_mswa_member_campaign_id" class="widefat">
			<?php
				$q = new WP_Query( array( 'post_type' => 'ms_wa_campaign', 'posts_per_page' => -1 ) );
			?>
			<option value="">No Campaign</option>
			<?php if ( $q->have_posts() ) {
				while ( $q->have_posts() ) {
					$q->the_post();
					?>
					<option value="<?php the_ID(); ?>" <?php selected( get_post_meta( $post->ID, '_mswa_member_campaign_id', true ), get_the_ID() ); ?>><?php the_title(); ?></option>
					<?php
				}
			}; wp_reset_postdata(); ?>
		</select>
	</div>
	<div class="field">
		<label><?php _e( 'Status', 'ms-wabroadcast' ); ?></label>
		<select name="_mswa_member_status" class="widefat">
			<option value="inactive" <?php selected( get_post_meta( $post->ID, '_mswa_member_status', true ), 'inactive' ) ?>>Not Active</option>
			<option value="active" <?php selected( get_post_meta( $post->ID, '_mswa_member_status', true ), 'active' ) ?>>Active</option>
		</select>
	</div>
	<div class="field">
		<label><?php _e( 'Phone Number', 'ms-wabroadcast' ); ?></label>
		<input class="widefat" type="text" name="_mswa_member_phone" value="<?php echo get_post_meta( $post->ID, '_mswa_member_phone', true ); ?>">
	</div>
	<div class="field">
		<label><?php _e( 'Email Address', 'ms-wabroadcast' ); ?></label>
		<input class="widefat" type="text" name="_mswa_member_email" value="<?php echo get_post_meta( $post->ID, '_mswa_member_email', true ); ?>">
	</div>
</div>