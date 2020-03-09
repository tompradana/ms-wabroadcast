<?php if ( $form_title ) : ?>
<h4><?php echo $form_title; ?></h4>
<?php endif; ?>
<div class="field">
	<label><?php _e( 'Full Name', 'ms-wabroadcast' ); ?></label>
    <input type="text" name="_mswa_input_name" placeholder="<?php esc_attr_e( 'Full name', 'ms-wabroadcast' ); ?>" required="true">
</div>
<div class="field">
	<label><?php _e( 'WhatsApp Number', 'ms-wabroadcast' ); ?></label>
    <input type="text" name="_mswa_input_phone" placeholder="<?php esc_attr_e( '628123456789', 'ms-wabroadcast' ); ?>" required="true">
</div>
<div class="field">
	<label><?php _e( 'Email Address', 'ms-wabroadcast' ); ?></label>
    <input type="email" name="_mswa_input_email" placeholder="<?php esc_attr_e( 'Email address', 'ms-wabroadcast' ); ?>" required="true">
</div>