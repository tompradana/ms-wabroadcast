<?php
	$input_name 	= '<input type="text" name="_mswa_input_name" placeholder="' . esc_attr( 'Full name', 'ms-wabroadcast' ) . '" required="true">';
	$template_html 	= str_replace( '{{input_name}}', $input_name, $template_html );

	$input_phone 	= '<input type="text" name="_mswa_input_phone" placeholder="' . esc_attr( 'WhatsApp number', 'ms-wabroadcast' ) . '" required="true">';
	$template_html 	= str_replace( '{{input_phone}}', $input_phone, $template_html );

	$input_email 	= '<input type="email" name="_mswa_input_email" placeholder="' . esc_attr( 'Email address', 'ms-wabroadcast' ) . '" required="true">';
	$template_html 	= str_replace( '{{input_email}}', $input_email, $template_html );

	echo do_shortcode( $template_html );
?>