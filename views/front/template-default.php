<?php if ( $form_title ) : ?>
<h4><?php echo $form_title; ?></h4>
<?php endif; ?>
<div class="field">
	<label><?php _e( 'Full Name', 'fonnletter' ); ?></label>
    <input type="text" name="_fonnletter_input_name" placeholder="<?php esc_attr_e( 'Full name', 'fonnletter' ); ?>" required="true">
</div>
<div class="field">
	<label><?php _e( 'WhatsApp Number', 'fonnletter' ); ?></label>
    <input type="text" name="_fonnletter_input_phone" placeholder="<?php esc_attr_e( '628123456789', 'fonnletter' ); ?>" required="true">
</div>
<div class="field">
	<label><?php _e( 'Email Address', 'fonnletter' ); ?></label>
    <input type="email" name="_fonnletter_input_email" placeholder="<?php esc_attr_e( 'Email address', 'fonnletter' ); ?>" required="true">
</div>