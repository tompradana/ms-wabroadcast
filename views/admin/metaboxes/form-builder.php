<?php /*
	<?php $formdata = get_post_meta( $post->ID, '_fonnletter_broadcast_form_data', true ); ?>
	<div id="build-wrap"></div>
	<div class="render-wrap"></div>
	<input type="hidden" name="fonnletter-formdata">
	<a class="button button-primary" href="javascript://" id="edit-form">Edit Form</a>
	<div style="clear: both"></div>
	*/
?>
<div class="fonnletter">
	<div class="field">
		<label><?php _e( 'Form title', 'fonnletter' ); ?></label>
		<input type="text" class="widefat" name="_fonnletter_form_title" value="<?php echo $form_title; ?>">
	</div>

	<div class="field">
		<label><?php _e( 'Select a template', 'fonnletter' ); ?></label>
		<select name="_fonnletter_template" class="widefat">
			<option value="default" <?php selected( $template, 'default' ); ?>><?php _e( 'Default', 'fonnletter' ); ?></option>
			<option value="custom" <?php selected( $template, 'custom' ); ?>><?php _e( 'Custom', 'fonnletter' ); ?></option>
		</select>
	</div>

	<div id="custom-template" class="field" style="display: none">
		<label><?php _e( 'HTML custom template', 'fonnletter' ); ?></label>
		<textarea name="_fonnletter_template_custom" rows="5" class="widefat"><?php echo $template_html; ?></textarea>
		<p><small><code>Available tags: {{input_name}} {{input_phone}} {{input_email}}</code></small></p>
		<p><small><code>Available attribute name: _fonnletter_input_name, _fonnletter_input_phone, _fonnletter_input_email</code></small></p>
	</div>

	<div class="field">
		<label><?php _e( 'Submit button text', 'fonnletter' ); ?></label>
		<input type="text" class="widefat" name="_fonnletter_submit_buttontext" value="<?php echo $buttontext; ?>">
	</div>
</div>