<div id="broadcast-message" class="ms-wa wrap">
	<h2><?php _e( 'Plugin Settings', 'ms-wabroadcast' ); ?></h2>
	<div class="container">
		<div class="card">
			<div id="fb-editor"></div>
			<form id="settings-form" method="post">
				<div class="field">
					<label><?php _e( 'Fonnte Token:', 'ms-wabroadcast' ); ?></label>
					<input type="password" name="mswa_token" value="<?php echo get_option( 'mswa_token' ); ?>" class="widefat">
					<small>Get a <a href="https://fonnte.com/" target="_blank">Fonnte token here</a>.</small>
				</div>

				<div class="field">
					<label><input type="checkbox" name="mswa_allow_samenumber" <?php checked( get_option( 'mswa_allow_samenumber' ), 'on' ); ?>> <?php _e( 'Allow member to register multiple times.', 'ms-wabroadcast' ); ?></label>
				</div>

				<div class="field">
					<label><input type="checkbox" name="mswa_wanotif" <?php checked( get_option( 'mswa_wanotif' ), 'on' ); ?>> <?php _e( 'Send Welcome Message for New Member', 'ms-wabroadcast' ); ?></label>
				</div>

				<div class="field">
					<label><?php _e( 'Notification Message:', 'ms-wabroadcast' ); ?></label>
					<textarea  id="message" name="mswa_wanotif_message"><?php if ( '' <> get_option( 'mswa_wanotif_message' ) ) { echo get_option( 'mswa_wanotif_message' ); } else { ?>Halo {{name}}, thank you for joining us 💌.<?php } ?></textarea>
					<small>Availabel tags: {{name}} {{phone}} {{email}} {{campaign_name}}</small>
				</div>

				<div class="field">
					<?php submit_button( __( 'Save Settings', 'ms-wabroadcast' ), $type = 'primary', $name = 'submit', $wrap = false, $other_attributes = null ); ?>
				</div>
			</form>
		</div>
	</div>
</div>