<?php
// class
class MS_WA_Broadcast {
	private $apiurl = 'https://fonnte.com/api/send_message.php';

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->init_hooks();
		$this->ajax_hooks();
	}

	/**
	 * Hooks
	 */
	public function init_hooks() {
		// action
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'init', array( $this, 'register_post_type' ) );

		foreach( array( 'ms_wa_campaign', 'ms_wa_member' ) as $post_type ) {
			// manage columns
			add_filter( "manage_{$post_type}_posts_columns", array( $this, "manage_{$post_type}_columns" ) );
			// set values
			add_action( "manage_{$post_type}_posts_custom_column", array( $this, "manage_{$post_type}_custom_columns" ), 10, 2 );
		}

		add_action( 'add_meta_boxes_ms_wa_campaign', array( $this, 'admin_metabox_scripts' ) );
		add_action( 'add_meta_boxes_ms_wa_member', array( $this, 'admin_metabox_scripts' ) );
		add_action( 'add_meta_boxes', array( $this, 'ms_wabroadcast_metabox' ) );
		add_action( 'save_post', array( $this, 'ms_wabroadcast_metabox_save' ), 10, 2 );

		add_action( 'ms_wabroadcast_campaign_after_fields', array( $this, 'ms_wabroadcast_shortcode_submit_button' ), 10, 2 );

		add_shortcode( 'ms_wa_campaign', array( $this, 'ms_wabroadcast_shortcode' ) );
	}

	/**
	 * Ajax hooks
	 */
	function ajax_hooks() {
		add_action( 'wp_ajax_ms_wa_ajax', array( $this, 'ms_wabroadcast_ajax_request' ) );
		add_action( 'wp_ajax_nopriv_ms_wa_ajax', array( $this, 'ms_wabroadcast_ajax_request' ) );
	}

	/**
	 * Admin menu
	 */
	public function admin_menu() {
		// menu
		$hook_suffix = add_menu_page( 
			__( 'MS Whatsapp Broadcast', 'ms-wabroadcast' ), 
			__( 'MS Whatsapp Broadcast', 'ms-wabroadcast' ),
			'administrator', 
			'ms-wabroadcast', 
			false,
			$icon_url = '', 
			81
		);

		// sub menu
		$send = add_submenu_page( 
			'ms-wabroadcast', 
			__( 'Broadcast Message', 'ms-wabroadcast' ), 
			__( 'Send Message', 'ms-wabroadcast' ), 
			'administrator', 
			'mswa-send-message', 
			array( $this, 'ms_wabroadcast_message_screen' )
		);

		$settings = add_submenu_page( 
			'ms-wabroadcast', 
			__( 'MS WABroadcast Settings', 'ms-wabroadcast' ), 
			__( 'Plugin Settings', 'ms-wabroadcast' ), 
			'administrator', 
			'mswa-plugin-settings', 
			array( $this, 'ms_wabroadcast_settings_screen' )
		);

		add_action( "load-{$send}", array( $this, 'admin_scripts') );
		add_action( "load-{$settings}", array( $this, 'admin_scripts') );
	}

	/**
	 * Admin scripts
	 */
	public function admin_scripts() {
		wp_enqueue_style( 'emojionearea', plugins_url( '/assets/js/node_modules/emojionearea/dist/emojionearea.min.css', MS_WABRDOADCAST_DIR . '/ms-wabroadcast' ) );
		wp_enqueue_style( 'admincss', plugins_url( '/assets/css/admin.css', MS_WABRDOADCAST_DIR . '/ms-wabroadcast' ) );

		wp_enqueue_script( 'emojionearea', plugins_url( '/assets/js/node_modules/emojionearea/dist/emojionearea.min.js', MS_WABRDOADCAST_DIR . '/ms-wabroadcast' ), array( 'jquery' ), MS_WABRDOADCAST_VERSION, true );
		wp_enqueue_script( 'adminjs', plugins_url( '/assets/js/admin.js', MS_WABRDOADCAST_DIR . '/ms-wabroadcast' ), array( 'jquery' ), MS_WABRDOADCAST_VERSION, true );

		wp_localize_script( 'adminjs', 'mswa', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'spinner_url' => admin_url( 'images/spinner.gif' )
		) );
	}

	/**
	 * Admin scripts
	 */
	public function admin_metabox_scripts() {
		wp_enqueue_style( 'admincss', plugins_url( '/assets/css/admin-editor.css', MS_WABRDOADCAST_DIR . '/ms-wabroadcast' ) );

		// wp_enqueue_script( 'formrender', plugins_url( '/assets/js/node_modules/formBuilder/dist/form-render.min.js', MS_WABRDOADCAST_DIR . '/ms-wabroadcast' ), array( 'jquery' ), MS_WABRDOADCAST_VERSION, true );
		// wp_enqueue_script( 'formbuilder', plugins_url( '/assets/js/node_modules/formBuilder/dist/form-builder.min.js', MS_WABRDOADCAST_DIR . '/ms-wabroadcast' ), array( 'jquery', 'jquery-ui-sortable', 'formrender' ), MS_WABRDOADCAST_VERSION, true );
		
		wp_enqueue_script( 'adminjs', plugins_url( '/assets/js/admin-editor.js', MS_WABRDOADCAST_DIR . '/ms-wabroadcast' ), array( 'jquery' ), MS_WABRDOADCAST_VERSION, true );

		// wp_localize_script( 'adminjs', 'mswa', array(
		// 	'formdata' => (isset($_GET['post']) &&!empty($_GET['post'])?get_post_meta( $_GET['post'], '_ms_wabroadcast_form_data',true):''),
		// ) );
	}

	/**
	 * Register post types
	 */
	public function register_post_type() {
		/**
		 * Campaign
		 */
		$args = array(
			'labels' 		=> array(
				'name'			=> __( 'Campaigns', 'ms-wabroadcast' ),
				'singular_name' => __( 'Campaign', 'ms-wabroadcast' ),
				'add_new'		=> __( 'Add new campaign', 'ms-wabroadcast' ),
				'search_items'	=> __( 'Search campaigns', 'ms-wabroadcast' ),
				'not_found'		=> __( 'No campaigns found', 'ms-wabroadcast' )
			),
			'description' 	=> __( 'Campaign post type', 'ms-wabroadcast' ),
			'public' 		=> false,
			'show_ui' 		=> true,
			'show_in_menu' 	=> 'ms-wabroadcast',
			'show_in_rest'	=> true,
			'menu_position'	=> 'ms-wabroadcast',
			'supports'		=> array( 'title' )
		);
		register_post_type( 'ms_wa_campaign', $args );

		/**
		 * Users
		 */
		$args['labels']			= array(
			'name'			=> __( 'Members', 'ms-wabroadcast' ),
			'singular_name' => __( 'Member', 'ms-wabroadcast' ),
			'add_new'		=> __( 'Add new member', 'ms-wabroadcast' ),
			'search_items'	=> __( 'Search members', 'ms-wabroadcast' ),
			'not_found'		=> __( 'No members found', 'ms-wabroadcast' )
		);
		$args['description'] 	= __( 'Member post type', 'ms-wabroadcast' );
		register_post_type( 'ms_wa_member', $args );
	}

	/**
	 * Manage admin columns
	 */
	function manage_ms_wa_campaign_columns( $columns ) {
		unset( $columns['date'] );
		$columns['shortcode'] 		= __( 'Shortcode', 'ms-wabroadcast' );
		$columns['member_today'] 	= __( 'Today Registered', 'ms-wabroadcast' );
		$columns['member'] 			= __( 'Total Members', 'ms-wabroadcast' );
		$columns['date']			= __( 'Date', 'ms-wabroadcast' );
		return $columns;
	}

	/**
	 * Manage admin columns
	 */
	function manage_ms_wa_member_columns( $columns ) {
		unset( $columns['date'] );
		$columns['title']		= __( 'Name', 'ms-wabroadcast' );
		$columns['campaign'] 	= __( 'Campaign', 'ms-wabroadcast' );
		$columns['phone'] 		= __( 'Phone/Whatsapp', 'ms-wabroadcast' );
		$columns['email'] 		= __( 'Email Address', 'ms-wabroadcast' );
		$columns['date']		= __( 'Date Registered', 'ms-wabroadcast' );
		return $columns;
	}

	/**
	 * Set values admin column
	 */
	function manage_ms_wa_campaign_custom_columns( $column, $post_id ) {
		switch ( $column ) {
			case 'shortcode':
				echo '<input type="text" onClick="this.setSelectionRange(0, this.value.length)" value="[ms_wa_campaign id='.$post_id.']">';
				break;
			case 'member':
				echo $this->get_total_members( $post_id );
				break;
			case 'member_today':
				echo $this->get_total_members( $post_id, true );
				break;
		}
	}

	/**
	 * Set values admin column
	 */
	function manage_ms_wa_member_custom_columns( $column, $post_id ) {
		switch ( $column ) {
			case 'campaign':
				$campaign_id = get_post_meta( $post_id, $key = '_mswa_member_campaign_id', true );
				if ( $campaign_id ) {
					printf( '%s [%s]', get_the_title( $campaign_id ), $campaign_id );
				} else {
					echo '-';
				}
				break;
			case 'phone':
				$phone = get_post_meta( $post_id, $key = '_mswa_member_phone', true );
				if ( $phone ) 
					echo $phone;
				else 
					echo '-';
				break;
			case 'phone':
				$phone = get_post_meta( $post_id, $key = '_mswa_member_phone', true );
				if ( $phone ) 
					echo $phone;
				else 
					echo '-';
				break;
			case 'email':
				$email = get_post_meta( $post_id, $key = '_mswa_member_email', true );
				if ( $email ) 
					echo $email;
				else 
					echo '-';
				break;
			case 'date':
				echo get_the_date( 'c', $post_id );
				break;
		}
	}

	/**
	 * Ajax send
	 */
	function ms_wabroadcast_ajax_request() {
		$ajaxresponse 	= array( 'code' => 400, 'message' => '❌ Error', 'results' => '' );
		$action 		= $_REQUEST['request'];

		switch ( $action ) {
			case 'send_message':
				$url 		= $this->apiurl;
				$token 		= get_option( 'mswa_token' );
				$numbers 	= $this->get_phone_numbers( $_POST['campaign'] );
			
				$args 		= array(
					'headers' => array(
						'Authorization' => $token
					),
				);

				$args['body'] = array(
					'type' 	=> 'text',
					'text' 	=> $_REQUEST['message']
				);

				if ( !empty( $numbers ) ) {
					$args['body']['phone'] = implode(',', $numbers);
				}
				
				$response = wp_remote_post( $url, $args );
				if ( !is_wp_error( $response ) ) {
					$response = json_decode( wp_remote_retrieve_body( $response ) );
					if ( $response->status === true ) {
						$ajaxresponse['code'] = 200;
						$ajaxresponse['message'] = '✔️ Success';
						$ajaxresponse['results'] = $response;
					} else {
						$ajaxresponse['message'] = '❗ Failed';
						$ajaxresponse['results'] = $response;
					}
				} else {
					$ajaxresponse['message'] = '❌ Connection error';
				}

				break;
			case 'save_settings':
				$settings = array( 'mswa_token', 'mswa_allow_samenumber', 'mswa_wanotif', 'mswa_wanotif_message' );
				foreach( $settings as $key ) {
					if ( isset( $_POST[$key] ) ) {
						update_option( $key, esc_attr( $_POST[$key] ) );
					} else {
						update_option( $key, false );
					}
				};
				$ajaxresponse['code'] = 200;
				$ajaxresponse['message'] = '✔️ Settings saved';
				$ajaxresponse['results'] = $response;
				break;
			case 'submit_campaign':
				if ( !wp_verify_nonce( $_REQUEST['mswa_submit_campaign_nonce'], 'mswa_submit_campaign' ) ) {
					$ajaxresponse['message'] = 'Cheat\'in uh! :(';
			    } else {
			    	$phone 	= isset( $_POST['_mswa_input_phone'] ) && !empty( $_POST['_mswa_input_phone'] ) ? $_POST['_mswa_input_phone'] : false;

			    	// error if phone not present
			    	if ( false === $phone ) {
			    		$ajaxresponse['message'] = 'WhatsApp number is required!';
			    	} else {
			    		// name
			    		$name 	= isset( $_POST['_mswa_input_name'] ) && !empty( $_POST['_mswa_input_name'] ) ? $_POST['_mswa_input_name'] : '';
			    		// email
			    		$email 	= isset( $_POST['_mswa_input_email'] ) && !empty( $_POST['_mswa_input_email'] ) ? $_POST['_mswa_input_email'] : '';

			    		if ( $email && !is_email( $email ) ) {
			    			// invalid email
			    			$ajaxresponse['message'] = 'Invalid email address!';	
			    		} else {
			    			// reformat phone
			    			$phone = preg_replace('/\s+/', '', $phone);
			    			if ( !preg_match( '/^[1-9]\d{8,}$/', $phone ) ) {
			    				$ajaxresponse['message'] = 'Phone number must be country code + phone number!';
			    			} else {

			    				if ( !$this->is_phone_registered( $phone ) || ( 'on' == get_option( 'mswa_allow_samenumber' ) && $this->is_phone_registered( $phone ) ) ) {
				    				// insert member
				    				$member = wp_insert_post( array(
				    					'post_type' 	=> 'ms_wa_member',
				    					'post_title'	=> $name,
				    					'post_status'	=> 'publish',
				    					'meta_input'	=> array(
				    						'_mswa_member_campaign_id' 	=> $_POST['campaign_id'],
				    						'_mswa_member_name' 		=> ucfirst( $name ),
				    						'_mswa_member_phone' 		=> $phone,
				    						'_mswa_member_email' 		=> $email
				    					)
				    				), true );

				    				// ok
				    				if ( !is_wp_error( $member ) ) {
				    					// send
					    				if ( 'on' ==  get_option( 'mswa_wanotif' ) && '' != get_option( 'mswa_wanotif_message' ) ) {
					    					$message = get_option( 'mswa_wanotif_message' );
					    					// {{name}} {{phone}} {{email}} {{campaign_name}}
					    					$message = str_replace( '{{name}}', $name, $message );
					    					$message = str_replace( '{{phone}}', $phone, $message );
					    					$message = str_replace( '{{email}}', $email, $message );
					    					$message = str_replace( '{{campaign_name}}', get_the_title($_POST['campaign_id']), $message );
					    					$this->send_message( $phone, $message );
					    				}

				    					$ajaxresponse['code'] = 200;
				    					$ajaxresponse['message'] = '✔️ Success';
				    				} else {
				    					$ajaxresponse['message'] = $member->get_error_message();
				    				}
				    			} else {
				    				// Already registered
				    				$ajaxresponse['message'] = 'Phone number already registered!';
				    			}
			    			}
			    		}
			    	}
				}
				break;
		}

		wp_send_json( $ajaxresponse );
	}

	/**
	 * Metabox
	 */
	public function ms_wabroadcast_metabox() {
		add_meta_box( 
			'formbuilder', 
			__( 'Campaign Form Template', 'ms-wabroadcast' ), 
			array( $this, 'ms_wabroadcast_formbuilder_screen' ), 
			'ms_wa_campaign', 
			'advanced', 
			'high' 
		);

		add_meta_box( 
			'membereditor', 
			__( ' Member Detail', 'ms-wabroadcast' ), 
			array( $this, 'ms_wabroadcast_membereditor_screen' ), 
			'ms_wa_member', 
			'advanced', 
			'high' 
		);
	}

	/**
	 * Save metabox
	 */
	public function ms_wabroadcast_metabox_save( $post_id, $post ) {
		if ( 'ms_wa_campaign' == $post->post_type ) {
	        foreach ($_POST as $key => $value) {
		    	if ( false !== strpos( $key, '_mswa' ) ) {
			        update_post_meta(
			            $post_id,
			            $key,
			            $value
			        );
			    }
		    }
	    } else if ( 'ms_wa_member' == $post->post_type ) {
	    	foreach ($_POST as $key => $value) {
		    	if ( false !== strpos( $key, '_mswa' ) ) {
			        update_post_meta(
			            $post_id,
			            $key,
			            $value
			        );
			    }
		    }
		    update_post_meta( $post_id, '_mswa_member_name', $post->post_title );
	    }
	}

	/**
	 * HTML Screens
	 */
	public function ms_wabroadcast_message_screen() {
		include( MS_WABRDOADCAST_DIR . 'views/admin/pages/broadcast-message.php' );
	}

	/**
	 * HTML Screens
	 */
	public function ms_wabroadcast_settings_screen() {
		include( MS_WABRDOADCAST_DIR . 'views/admin/pages/plugin-settings.php' );
	}

	/**
	 * HTML Screens
	 */
	public function ms_wabroadcast_formbuilder_screen( $post ) {
		$template = get_post_meta( $post->ID, '_mswa_template', true );
		$template_html = get_post_meta( $post->ID, '_mswa_template_custom', true );
		$form_title = get_post_meta( $post->ID, '_mswa_form_title', true );
		$buttontext = '' <> get_post_meta( $post->ID, '_mswa_submit_buttontext', true ) ? get_post_meta( $post->ID, '_mswa_submit_buttontext', true ) : __( 'Subscribe', 'ms-wabroadcast' );
		include( MS_WABRDOADCAST_DIR . 'views/admin/metaboxes/form-builder.php' );
	}

	/**
	 * HTML Screens
	 */
	public function ms_wabroadcast_membereditor_screen( $post ) {
		include( MS_WABRDOADCAST_DIR . 'views/admin/metaboxes/member-editor.php' );
	}

	/**
	 * Submit button
	 */
	public function ms_wabroadcast_shortcode_submit_button( $id, $template ) {
		$buttontext = get_post_meta( $id, '_mswa_submit_buttontext', true );
		include( MS_WABRDOADCAST_DIR . 'views/front/submit-button.php' );
	}

	/**
	 * Shortcode
	 */
	public function ms_wabroadcast_shortcode( $atts ) {
		$atts = shortcode_atts( array(
	        'id' 	=> '',
    	), $atts, 'ms_wa_campaign' );
    	
    	extract( $atts );

    	if ( '' == $id || ( $id != '' && get_post_type( $id ) !== 'ms_wa_campaign' ) ) {
    		return __( 'Invalid campaign ID', 'ms-wabroadcast' );
    	} else {
    		$template = get_post_meta( $id, '_mswa_template', true );
			$template_html = get_post_meta( $id, '_mswa_template_custom', true );
			$form_title = get_post_meta( $id, '_mswa_form_title', true );
			ob_start();
			include( MS_WABRDOADCAST_DIR . 'views/front/campaign-shortcode.php' );
			if ( $template == 'default' ) {
				wp_enqueue_style( 'mswa-campaigncss', plugins_url( '/assets/css/front.css', MS_WABRDOADCAST_DIR . '/ms-wabroadcast' ) );
			}
			wp_enqueue_script( 'mswa-campaignjs', plugins_url( '/assets/js/front.js', MS_WABRDOADCAST_DIR . '/ms-wabroadcast' ), array( 'jquery' ), MS_WABRDOADCAST_VERSION, true );
			wp_localize_script( 'mswa-campaignjs', 'mswa', array(
				'ajax_url' 		=> admin_url( 'admin-ajax.php' ),
				'spinner_url' 	=> plugins_url( '/assets/images/loader.svg', MS_WABRDOADCAST_DIR . '/ms-wabroadcast' )
			) );
			return ob_get_clean();
		}
	}

	/**
	 * Helper
	 */
	public function get_campaign_template( $id = null ) {
		if ( $id == null ) {
			return;
		}
	}

	/**
	 * Check registered
	 */
	public function is_phone_registered( $phone ) {
		$q = new WP_Query( array(
			'post_type' 		=> 'ms_wa_member',
			'posts_per_page' 	=> 1,
			'meta_query' 		=> array(
				array(
					'key' 		=> '_mswa_member_phone',
					'value'		=> $phone,
					'compare'	=> '='
				)
			)
		) );
		if ( $q->have_posts() ) {
			wp_reset_postdata();
			return true;
		} else {
			wp_reset_postdata();
			return false;
		}
	}

	/** 
	 * Check member inside
	 */
	public function get_total_members( $campaign_id = null, $today = false ) {
		$args = array(
			'post_type' 		=> 'ms_wa_member',
			'posts_per_page' 	=> -1,
		);
		if ( $campaign_id != null ) {
			$args['meta_query'] = array(
				array(
					'key' 		=> '_mswa_member_campaign_id',
					'value'		=> $campaign_id,
					'compare'	=> '='
				)
			);
		}
		if ( $today ) {
			$today = getdate();
			$args['date_query'] = array(
				array(
		            'year'  => $today['year'],
		            'month' => $today['mon'],
		            'day'   => $today['mday'],
		        )
		      	// array(
		       	//  	'after' => $today,
		       	//  	'inclusive' => true,
		       	//  )
		    );
		}
		$q = new WP_Query( $args );
		wp_reset_postdata();
		return $q->found_posts;
	}

	/** 
	 * Get phone numbers
	 */
	public function get_phone_numbers( $campaign_id = null ) {
		$numbers = array();
		$args = array(
			'post_type' 		=> 'ms_wa_member',
			'posts_per_page' 	=> -1,
		);
		if ( $campaign_id != null && $campaign_id != 'all' ) {
			$args['meta_query'] = array(
				array(
					'key' 		=> '_mswa_member_campaign_id',
					'value'		=> $campaign_id,
					'compare'	=> '='
				)
			);
		}
		$q = new WP_Query( $args );
		if ( $q->have_posts() ) {
			while ( $q->have_posts() ) : $q->the_post();
				if ( '' <> get_post_meta( get_the_ID(), '_mswa_member_phone', true ) ) {
					$numbers[] = get_post_meta( get_the_ID(), '_mswa_member_phone', true );
				}
			endwhile;
		}
		wp_reset_postdata();
	  	return $numbers;
	}

	/**
	 * Send message
	 */
	public function send_message( $phones = '', $text = '' ) {
		$url 		= $this->apiurl;
		$token 		= get_option( 'mswa_token' );
		$numbers 	= $this->get_phone_numbers( $_POST['campaign'] );

		$args 		= array(
			'headers' => array(
				'Authorization' => $token
			),
		);

		$args['body'] = array(
			'type' 	=> 'text',
			'text' 	=> $text
		);

		if ( $phones && !is_array( $phones ) ) {
			$phones = (array) $phones;
		}

		if ( !empty( $phones ) ) {
			$args['body']['phone'] = implode(',', $phones);
		}

		$response = wp_remote_post( $url, $args );
		if ( !is_wp_error( $response ) ) {
			$response = json_decode( wp_remote_retrieve_body( $response ) );
			if ( $response->status === true ) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
}

// ok
$GLOBALS['ms-wabroadcast'] = new MS_WA_Broadcast();