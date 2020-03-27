<div id="broadcast-message" class="fonnletter wrap">
	<h2><?php _e( 'Plugin Settings', 'fonnletter' ); ?></h2>
	<div class="container">
		<div class="card">
			<div id="fb-editor"></div>
			<form id="settings-form" method="post">
				<div class="field">
					<label><?php _e( 'Fonnte Token:', 'fonnletter' ); ?></label>
					<input type="password" name="fonnletter_token" value="<?php echo get_option( 'fonnletter_token' ); ?>" class="widefat">
					<small>Get a <a href="https://fonnte.com/" target="_blank">Fonnte token here</a>.</small>
				</div>

				<div class="field">
					<label><?php _e( 'Webhook URL:', 'fonnletter' ); ?></label>
					<input type="text" class="widefat" onClick="this.setSelectionRange(0, this.value.length)" value="<?php echo get_rest_url(null,'fonnletter/v1/webhook'); ?>">
				</div>

				<div class="field">
					<label><input type="checkbox" name="fonnletter_allow_samenumber" <?php checked( get_option( 'fonnletter_allow_samenumber' ), 'on' ); ?>> <?php _e( 'Allow member to register multiple times.', 'fonnletter' ); ?></label>
				</div>

				<div class="field">
					<label><input type="checkbox" name="fonnletter_auto_activate_member" <?php checked( get_option( 'fonnletter_auto_activate_member' ), 'on' ); ?>> <?php _e( 'Auto activate new member.', 'fonnletter' ); ?></label>
				</div>

				<div class="field">
					<label><input type="checkbox" name="fonnletter_wanotif" <?php checked( get_option( 'fonnletter_wanotif' ), 'on' ); ?>> <?php _e( 'Send Welcome Message for New Member', 'fonnletter' ); ?></label>
				</div>

				<div class="field">
					<label><?php _e( 'Notification Message:', 'fonnletter' ); ?></label>
					<textarea  id="message" name="fonnletter_wanotif_message"><?php if ( '' <> get_option( 'fonnletter_wanotif_message' ) ) { echo get_option( 'fonnletter_wanotif_message' ); } else { ?>Halo {{name}}! terima kasih sudah berminat berlangganan!

Silahkan balas dengan *YA* untuk berlangganan. 😉<?php } ?></textarea>
					<small>Availabel tags: {{name}} {{phone}} {{email}} {{campaign_name}}</small>
				</div>

				<div class="field">
					<label><?php _e( 'Activation Message:', 'fonnletter' ); ?></label>
					<textarea  id="message2" name="fonnletter_activation_message"><?php if ( '' <> get_option( 'fonnletter_activation_message' ) ) { echo get_option( 'fonnletter_activation_message' ); } else { ?>Halo {{name}}! Kamu sudah berlangganan

Nikmati informasi terbaru dari kami. 💌<?php } ?></textarea>
					<small>Availabel tags: {{name}} {{phone}} {{email}} {{campaign_name}}</small>
				</div>

				<div class="field">
					<label><?php _e( 'Deactivation Message:', 'fonnletter' ); ?></label>
					<textarea  id="message3" name="fonnletter_deactivation_message"><?php if ( '' <> get_option( 'fonnletter_deactivation_message' ) ) { echo get_option( 'fonnletter_deactivation_message' ); } else { ?>Halo {{name}}! Kamu sudah tidak lagi berlangganan

Jika ini adalah kesalahan balas *YA* untuk berlangganan kembali.<?php } ?></textarea>
					<small>Availabel tags: {{name}} {{phone}} {{email}} {{campaign_name}}</small>
				</div>

				<div class="field">
					<label><?php _e( 'Default Info Message:', 'fonnletter' ); ?></label>
					<textarea  id="message3" name="fonnletter_default_info_message"><?php if ( '' <> get_option( 'fonnletter_default_info_message' ) ) { echo get_option( 'fonnletter_default_info_message' ); } else { ?>1️⃣ Balas *YA* untuk berlangganan dari semua channel yang Anda ikuti.

2️⃣ Balas *YA [IDCAMPAIGN]* untuk berlangganan dari channel tertentu yang Anda ikuti.

3⃣ Balas *STOP* atau *UNSUBSCRIBE* untuk berhenti berlangganan dari semua channel.

4⃣ Balas *STOP [IDCAMPAIGN]* atau *UNSUBSCRIBE [IDCAMPAIGN]* untuk berhenti berlangganan dari channel tertentu.

5️⃣ Balas *INFO* untuk melihat daftar channel yang Anda ikuti.<?php } ?></textarea>
					<small>Availabel tags: {{name}} {{phone}} {{email}} {{campaign_name}}</small>
				</div>

				<div class="field">
					<?php submit_button( __( 'Save Settings', 'fonnletter' ), $type = 'primary', $name = 'submit', $wrap = false, $other_attributes = null ); ?>
					<?php submit_button( __( 'Reset Settings', 'fonnletter' ), $type = 'secondary', $name = 'reset', $wrap = false, $other_attributes = array( 'style' => 'float:right;', 'onclick' => 'return confirm(\'Are you sure?\')' ) ); ?>
				</div>
			</form>
		</div>
	</div>
</div>