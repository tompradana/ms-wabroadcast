<?php
	$input_name 	= '<input type="text" name="_fonnletter_input_name" placeholder="' . esc_attr( 'Full name', 'fonnletter' ) . '" required="true">';
	$template_html 	= str_replace( '{{input_name}}', $input_name, $template_html );

	$input_phone 	= '<input type="text" name="_fonnletter_input_phone" placeholder="' . esc_attr( 'WhatsApp number', 'fonnletter' ) . '" required="true">';
	$template_html 	= str_replace( '{{input_phone}}', $input_phone, $template_html );

	$input_email 	= '<input type="email" name="_fonnletter_input_email" placeholder="' . esc_attr( 'Email address', 'fonnletter' ) . '" required="true">';
	$template_html 	= str_replace( '{{input_email}}', $input_email, $template_html );

	echo do_shortcode( $template_html );
?>