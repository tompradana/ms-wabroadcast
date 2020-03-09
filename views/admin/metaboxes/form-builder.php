<?php /*
	<?php $formdata = get_post_meta( $post->ID, '_ms_wabroadcast_form_data', true ); ?>
	<div id="build-wrap"></div>
	<div class="render-wrap"></div>
	<input type="hidden" name="mswa-formdata">
	<a class="button button-primary" href="javascript://" id="edit-form">Edit Form</a>
	<div style="clear: both"></div>
	*/
?>
<div class="ms-wa">
	<div class="field">
		<label><?php _e( 'Form title', 'ms-wabroadcast' ); ?></label>
		<input type="text" class="widefat" name="_mswa_form_title" value="<?php echo $form_title; ?>">
	</div>

	<div class="field">
		<label><?php _e( 'Select a template', 'ms-wabroadcast' ); ?></label>
		<select name="_mswa_template" class="widefat">
			<option value="default" <?php selected( $template, 'default' ); ?>><?php _e( 'Default', 'ms-wabroadcast' ); ?></option>
			<option value="custom" <?php selected( $template, 'custom' ); ?>><?php _e( 'Custom', 'ms-wabroadcast' ); ?></option>
		</select>
	</div>

	<div id="custom-template" class="field" style="display: none">
		<label><?php _e( 'HTML custom template', 'ms-wabroadcast' ); ?></label>
		<textarea name="_mswa_template_custom" rows="5" class="widefat"><?php echo $template_html; ?></textarea>
		<p><small><code>Available tags: {{input_name}} {{input_phone}} {{input_email}}</code></small></p>
		<p><small><code>Available attribute name: _mswa_input_name, _mswa_input_phone, _mswa_input_email</code></small></p>
	</div>

	<div class="field">
		<label><?php _e( 'Submit button text', 'ms-wabroadcast' ); ?></label>
		<input type="text" class="widefat" name="_mswa_submit_buttontext" value="<?php echo $buttontext; ?>">
	</div>
</div>