<div id="broadcast-message" class="ms-wa wrap">
	<h2><?php _e( 'Broadcast Message', 'ms-wabroadcast' ); ?></h2>
	<div class="container">
		<div class="card">
			<form id="broadcast-form" method="post">
				<h3><?php _e( 'Send Broadcast Message', 'ms-wabroadcast' ); ?></h3>
				<div class="field">
					<label><?php _e( 'Send to:', 'ms-wabroadcast' ); ?></label>
					<select id="campaign" name="campaign" class="widefat" required="true">
						<option value=""><?php _e( '-- Select source --', 'ms-wabroadcast' ); ?>
						<option value="all"><?php _e( 'All Members', 'ms-wabroadcast' ); ?>
						<?php
						query_posts( 'post_type=ms_wa_campaign&post_status=publish&posts_per_page=-1' );
						while ( have_posts() ) : the_post();
							echo '<option value="'.get_the_ID().'">'.get_the_title().' ('.$this->get_total_members(get_the_ID()).' members)</option>';
						endwhile; 
						wp_reset_query();
						?>
					</select>
				</div>

				<div class="field">
					<label><?php _e( 'Message:', 'ms-wabroadcast' ); ?></label>
					<textarea required="true" id="message" name="message">Broadcast message 🤝.</textarea>
				</div>

				<div class="field">
					<?php submit_button( __( 'Broadcast Messages', 'ms-wabroadcast' ), $type = 'primary', $name = 'submit', $wrap = false, $other_attributes = null ); ?>
				</div>
			</form>
		</div>
	</div>
</div>