<div id="ms-wabroadcast-campaign-<?php echo $id; ?>" class="ms-wa-campaign">
	<form name="ms-wa-campaign-form" data-campaign="<?php echo $id; ?>" class="ms-wabroadcast-template-<?php echo $template; ?>">
		<?php do_action( 'ms_wabroadcast_campaign_before_fields', $id, $template ); ?>
		
		<?php if ( $template == 'default' ) {
			include( MS_WABRDOADCAST_DIR . 'views/front/template-default.php' );
		} else {
			include( MS_WABRDOADCAST_DIR . 'views/front/template-custom.php' );
		}; ?>

		<?php wp_nonce_field( 'mswa_submit_campaign', 'mswa_submit_campaign_nonce' ); ?>

		<?php do_action( 'ms_wabroadcast_campaign_after_fields', $id, $template ); ?>
	</form>
</div>