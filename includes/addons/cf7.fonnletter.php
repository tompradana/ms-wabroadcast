<?php
function fonnletter_cf7_send( $WPCF7_ContactForm ){
	$currentformInstance  	= WPCF7_ContactForm::get_current();
	$contactformsubmition 	= WPCF7_Submission::get_instance();
	$form_id 				= $currentformInstance->id();

	$fonnletter_enable = get_post_meta( $form_id, '_fonnletter_enable', true );
	$admin_number = get_post_meta( $form_id, '_fonnletter_admin_number', true );
	$admin_message = get_post_meta( $form_id, '_fonnletter_admin_message', true );
	$user_message = get_post_meta( $form_id, '_fonnletter_user_message', true );

	if ( $fonnletter_enable == 'on' ) {
		$posted_data = array();

		if ( $contactformsubmition ) {
			$posted_data = $contactformsubmition->get_posted_data(); 
		}

		$nama = isset( $posted_data['fonnletter-name'] ) ? $posted_data['fonnletter-name'] : '';
		$phone = isset( $posted_data['fonnletter-phone'] ) ? $posted_data['fonnletter-phone'] : '';
		$email = isset( $posted_data['fonnletter-email'] ) ? $posted_data['fonnletter-email'] : '';
		
		$user_phones = array(
			array(
				'nama'	=> $nama,
				'nomer'	=> $phone
			)
		);

		$admin_phones = array(
			array(
				'nama'	=> 'admin - ' . get_bloginfo( 'name' ),
				'nomer'	=> $admin_number
			)
		);

		FONNLETTER()->send_message( $user_phones, $user_message );
		FONNLETTER()->send_message( $admin_phones, $admin_message );
	}
}
add_action( 'wpcf7_before_send_mail', 'fonnletter_cf7_send', 10, 1 );

function fonnletter_cf7_panels( $panels ) {
	$panels['fonnletter-panel'] = array(
		'title' 	=> 'Fonnletter Settings',
		'callback' 	=> 'fonnletter_cf7_panel_callback',
	);

	return $panels;
}
add_filter( 'wpcf7_editor_panels', 'fonnletter_cf7_panels' );

function fonnletter_cf7_panel_callback( $post ) { 
	?>
	<h2><?php echo esc_html( __( 'Fonnletter Settings', 'contact-form-7' ) ); ?></h2>
	<fieldset>
		<legend>Pengaturan integrasi Fonnte dan Contact Form 7</legend>
		<p>
			<label><input type="checkbox" name="fonnletter_enable" <?php checked(  get_post_meta( $post->id(), '_fonnletter_enable', true ), 'on' ); ?>> Aktifkan Fonnletter</label>
		</p>

		<p>
			<label>Nomor WhatsApp Admin</label>
			<input type="text" class="large-text" id="fonnletter_admin_number" name="fonnletter_admin_number" value="<?php echo get_post_meta( $post->id(), '_fonnletter_admin_number', true ); ?>">
		</p>
		<p>
			<label>Pesan untuk User</label>
			<textarea id="fonnletter_user_message" name="fonnletter_user_message" cols="100" rows="6" class="large-text"><?php echo get_post_meta( $post->id(), '_fonnletter_user_message', true ); ?></textarea>
		</p>
		<p>
			<label>Pesan untuk Admin</label>
			<textarea id="fonnletter_admin_message" name="fonnletter_admin_message" cols="100" rows="6" class="large-text"><?php echo get_post_meta( $post->id(), '_fonnletter_admin_message', true ); ?></textarea>
		</p>
	</fieldset>
	<?php
}

function fonnletter_cf7_save_form( $contact_form, $args, $context ) {
	$form_id = $args['id'];

	update_post_meta( $form_id, '_fonnletter_enable', $args['fonnletter_enable'] );

	update_post_meta( $form_id, '_fonnletter_admin_number', $args['fonnletter_admin_number'] );
	update_post_meta( $form_id, '_fonnletter_admin_message', $args['fonnletter_admin_message'] );
	update_post_meta( $form_id, '_fonnletter_user_message', $args['fonnletter_user_message'] );
}
add_action( 'wpcf7_save_contact_form', 'fonnletter_cf7_save_form', 10, 3 );