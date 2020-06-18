<div id="fonnletterbroadcast-campaign-<?php echo $id; ?>" class="fonnletter-campaign">
	<form name="fonnletter-campaign-form" data-campaign="<?php echo $id; ?>" class="fonnletterbroadcast-template-<?php echo $template; ?>">
		<?php do_action( 'fonnletter_broadcast_campaign_before_fields', $id, $template ); ?>
		
		<?php if ( $template == 'default' ) {
			include( FONNLETTER_DIR . 'views/front/template-default.php' );
		} else {
			include( FONNLETTER_DIR . 'views/front/template-custom.php' );
		}; ?>

		<?php wp_nonce_field( 'fonnletter_submit_campaign', 'fonnletter_submit_campaign_nonce' ); ?>

		<?php do_action( 'fonnletter_broadcast_campaign_after_fields', $id, $template ); ?>
	</form>
</div>