<?php
// class
class FONNLETTER_Plugin {
	// private $apiurl = 'https://fonnte.com/api/send_message.php';
	private $apiurl = 'https://fonnte.com/api/api-undangan.php';

	private $settings = array( 
		'fonnletter_token', 
		'fonnletter_allow_samenumber', 
		'fonnletter_wanotif',
		'fonnletter_auto_activate_member', 
		'fonnletter_wanotif_message',
		'fonnletter_activation_message',
		'fonnletter_deactivation_message',
		'fonnletter_default_info_message'
	);

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

		foreach( array( 'fonnletter_campaign', 'fonnletter_member', 'fonnletter_message' ) as $post_type ) {
			// manage columns
			add_filter( "manage_{$post_type}_posts_columns", array( $this, "manage_{$post_type}_columns" ) );
			// set values
			add_action( "manage_{$post_type}_posts_custom_column", array( $this, "manage_{$post_type}_custom_columns" ), 10, 2 );
		}

		add_action( 'add_meta_boxes_fonnletter_campaign', array( $this, 'admin_metabox_scripts' ) );
		add_action( 'add_meta_boxes_fonnletter_member', array( $this, 'admin_metabox_scripts' ) );
		add_action( 'add_meta_boxes_fonnletter_message', array( $this, 'admin_metabox_scripts' ) );

		add_action( 'add_meta_boxes', array( $this, 'fonnletter_broadcast_metabox' ) );
		add_action( 'save_post', array( $this, 'fonnletter_broadcast_metabox_save' ), 10, 2 );

		add_action( 'fonnletter_broadcast_campaign_after_fields', array( $this, 'fonnletter_broadcast_shortcode_submit_button' ), 10, 2 );

		add_shortcode( 'fonnletter_campaign', array( $this, 'fonnletter_broadcast_shortcode' ) );

		// webhook
		add_action( 'rest_api_init', array( $this, 'fonnletter_broadcast_rest_api' ) );

		// delete post
		add_action( 'admin_notices', array( $this, 'fonnletter_broadcast_notice' ) );

		// delete member
		add_action( 'wp_trash_post', array( $this, 'delete_member' ) );

		// metabox
		add_action( 'add_meta_boxes', array( $this, 'fonnletter_metabox' ) );
	}

	/**
	 * [delete_member description]
	 * @param  [type] $post_id [description]
	 * @return [type]          [description]
	 */
	public function delete_member( $post_id ) {
		$phone 	= get_post_meta( $post_id, '_fonnletter_member_phone', true );
		$url 	= 'https://fonnte.com/api/del-kontak.php';
		$token 	= get_option( 'fonnletter_token' );

		if ( !empty( $phone ) ) {
			$args = array(
				'headers' => array(
					'Authorization' => $token
				),
				'body' => array(
					'nomer' => $phone
				)
			);

			$response = wp_remote_post( $url, $args );
			// if ( !is_wp_error( $response ) ) {
			// 	$this->write_log( wp_remote_retrieve_body(  $response  ) );
			// }
		}
	}

	/**
	 * [fonnletter_broadcast_notice description]
	 * @return [type] [description]
	 */
	public function fonnletter_broadcast_notice() {
		if ( !is_admin() ) return;
		$class = 'notice notice-error';
		$message = '';
		if ( false === get_option( 'fonnletter_token' ) ) {
			$message = sprintf( __( 'Fonnte fresh install, please setup and add your Fonnte token <a href="%s">here</a>.', 'fonnletter' ), admin_url( 'admin.php?page=fonnletter-plugin-settings' ) );
		} else if ( '' == get_option( 'fonnletter_token' ) ) {
			$message = sprintf( __( 'Fonnte token missing, please add your Fonnte token <a href="%s">here</a>.', 'fonnletter' ), admin_url( 'admin.php?page=fonnletter-plugin-settings' ) );
		}
		if ( $message  ) 
			printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), $message );
	}

	/**
	 * Ajax hooks
	 */
	public function ajax_hooks() {
		add_action( 'wp_ajax_fonnletter_ajax', array( $this, 'fonnletter_broadcast_ajax_request' ) );
		add_action( 'wp_ajax_nopriv_fonnletter_ajax', array( $this, 'fonnletter_broadcast_ajax_request' ) );
	}

	/**
	 * Admin menu
	 */
	public function admin_menu() {
		// menu
		$hook_suffix = add_menu_page( 
			__( 'Fonnletter', 'fonnletter' ), 
			__( 'Fonnletter', 'fonnletter' ),
			'administrator', 
			'fonnletter', 
			false,
			$icon_url = '', 
			81
		);

		// sub menu
		$send = add_submenu_page( 
			'fonnletter', 
			__( 'Broadcast Message', 'fonnletter' ), 
			__( 'Send Message', 'fonnletter' ), 
			'administrator', 
			'fonnletter-send-message', 
			array( $this, 'fonnletter_broadcast_message_screen' )
		);

		$settings = add_submenu_page( 
			'fonnletter', 
			__( 'MS WABroadcast Settings', 'fonnletter' ), 
			__( 'Plugin Settings', 'fonnletter' ), 
			'administrator', 
			'fonnletter-plugin-settings', 
			array( $this, 'fonnletter_broadcast_settings_screen' )
		);

		add_action( "load-{$send}", array( $this, 'admin_scripts') );
		add_action( "load-{$settings}", array( $this, 'admin_scripts') );
	}

	/**
	 * Admin scripts
	 */
	public function admin_scripts() {
		wp_enqueue_style( 'emojionearea', plugins_url( '/assets/js/node_modules/emojionearea/dist/emojionearea.min.css', FONNLETTER_DIR . '/fonnletter' ) );
		wp_enqueue_style( 'admincss', plugins_url( '/assets/css/admin.css', FONNLETTER_DIR . '/fonnletter' ) );

		wp_enqueue_script( 'emojionearea', plugins_url( '/assets/js/node_modules/emojionearea/dist/emojionearea.min.js', FONNLETTER_DIR . '/fonnletter' ), array( 'jquery' ), FONNLETTER_VERSION, true );
		wp_enqueue_script( 'adminjs', plugins_url( '/assets/js/admin.js', FONNLETTER_DIR . '/fonnletter' ), array( 'jquery' ), FONNLETTER_VERSION, true );

		wp_localize_script( 'adminjs', 'fonnletter', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'spinner_url' => admin_url( 'images/spinner.gif' )
		) );
	}

	/**
	 * Admin scripts
	 */
	public function admin_metabox_scripts() {
		wp_enqueue_style( 'admincss', plugins_url( '/assets/css/admin-editor.css', FONNLETTER_DIR . '/fonnletter' ) );

		// wp_enqueue_script( 'formrender', plugins_url( '/assets/js/node_modules/formBuilder/dist/form-render.min.js', FONNLETTER_DIR . '/fonnletter' ), array( 'jquery' ), FONNLETTER_VERSION, true );
		// wp_enqueue_script( 'formbuilder', plugins_url( '/assets/js/node_modules/formBuilder/dist/form-builder.min.js', FONNLETTER_DIR . '/fonnletter' ), array( 'jquery', 'jquery-ui-sortable', 'formrender' ), FONNLETTER_VERSION, true );
		
		wp_enqueue_script( 'adminjs', plugins_url( '/assets/js/admin-editor.js', FONNLETTER_DIR . '/fonnletter' ), array( 'jquery' ), FONNLETTER_VERSION, true );

		// wp_localize_script( 'adminjs', 'fonnletter', array(
		// 	'formdata' => (isset($_GET['post']) &&!empty($_GET['post'])?get_post_meta( $_GET['post'], '_fonnletter_broadcast_form_data',true):''),
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
				'name'			=> __( 'Campaigns', 'fonnletter' ),
				'singular_name' => __( 'Campaign', 'fonnletter' ),
				'add_new'		=> __( 'Add new campaign', 'fonnletter' ),
				'add_new_item'  => __( 'Add New Campaign', 'fonnletter' ),
				'search_items'	=> __( 'Search campaigns', 'fonnletter' ),
				'not_found'		=> __( 'No campaigns found', 'fonnletter' )
			),
			'description' 	=> __( 'Campaign post type', 'fonnletter' ),
			'public' 		=> false,
			'show_ui' 		=> true,
			'show_in_menu' 	=> 'fonnletter',
			'show_in_rest'	=> false,
			'menu_position'	=> 'fonnletter',
			'supports'		=> array( 'title' )
		);
		register_post_type( 'fonnletter_campaign', $args );

		/**
		 * Users
		 */
		$args['labels']	= array(
			'name'			=> __( 'Members', 'fonnletter' ),
			'singular_name' => __( 'Member', 'fonnletter' ),
			'add_new'		=> __( 'Add new member', 'fonnletter' ),
			'add_new_item'  => __( 'Add New Member', 'fonnletter' ),
			'search_items'	=> __( 'Search members', 'fonnletter' ),
			'not_found'		=> __( 'No members found', 'fonnletter' )
		);
		$args['description'] = __( 'Member post type', 'fonnletter' );
		register_post_type( 'fonnletter_member', $args );

		/**
		 * Follow-Up Message
		 */
		$args['labels']	= array(
			'name'			=> __( 'Follow-Up Message', 'fonnletter' ),
			'singular_name' => __( 'Follow-Up Message', 'fonnletter' ),
			'add_new'		=> __( 'Add new message', 'fonnletter' ),
			'add_new_item'  => __( 'Add New Follow-Up Message', 'fonnletter' ),
			'search_items'	=> __( 'Search messages', 'fonnletter' ),
			'not_found'		=> __( 'No messages found', 'fonnletter' )
		);
		$args['supports'] = array( 'title', 'editor' );
		$args['description'] = __( 'Follow-Up message post type', 'fonnletter' );
		register_post_type( 'fonnletter_message', $args );
	}

	/**
	 * Manage admin columns
	 */
	public function manage_fonnletter_campaign_columns( $columns ) {
		unset( $columns['date'] );
		$columns['shortcode'] 		= __( 'Shortcode', 'fonnletter' );
		$columns['member_today'] 	= __( 'Today Registered', 'fonnletter' );
		$columns['member'] 			= __( 'Total Members', 'fonnletter' );
		$columns['date']			= __( 'Date', 'fonnletter' );
		return $columns;
	}

	/**
	 * Manage admin columns
	 */
	public function manage_fonnletter_member_columns( $columns ) {
		unset( $columns['date'] );
		unset( $columns['date'] );
		$columns['title']		= __( 'Name', 'fonnletter' );
		$columns['campaign'] 	= __( 'Campaign', 'fonnletter' );
		$columns['phone'] 		= __( 'Phone/Whatsapp', 'fonnletter' );
		$columns['email'] 		= __( 'Email Address', 'fonnletter' );
		$columns['status'] 		= __( 'Status', 'fonnletter' );
		$columns['date_registered']	= __( 'Date Registered', 'fonnletter' );
		return $columns;
	}

	/**
	 * Set values admin column
	 */
	public function manage_fonnletter_campaign_custom_columns( $column, $post_id ) {
		switch ( $column ) {
			case 'shortcode':
			echo '<input type="text" onClick="this.setSelectionRange(0, this.value.length)" value="[fonnletter_campaign id='.$post_id.']">';
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
	public function manage_fonnletter_member_custom_columns( $column, $post_id ) {
		switch ( $column ) {
			case 'campaign':
			$campaign_id = get_post_meta( $post_id, $key = '_fonnletter_member_campaign_id', true );
			if ( $campaign_id ) {
				printf( '%s [%s]', get_the_title( $campaign_id ), $campaign_id );
			} else {
				echo '-';
			}
			break;
			case 'phone':
			$phone = get_post_meta( $post_id, $key = '_fonnletter_member_phone', true );
			if ( $phone ) 
				echo $phone;
			else 
				echo '-';
			break;
			case 'email':
			$email = get_post_meta( $post_id, $key = '_fonnletter_member_email', true );
			if ( $email ) 
				echo $email;
			else 
				echo '-';
			break;
			case 'status':
			$phone = get_post_meta( $post_id, $key = '_fonnletter_member_status', true );
			if ( $phone == 'inactive' || false == $phone ) 
				echo __( "Not active", 'fonnletter' ) . '<span style="display:inline-block; width:10px;height:10px;border-radius:500px;background-color:red;margin-left:10px"></span>';
			else 
				echo __( "Active", 'fonnletter' ) . '<span style="display:inline-block; width:10px;height:10px;border-radius:500px;background-color:green;margin-left:10px"></span>';
			break;
			case 'date_registered':
				echo get_the_date( 'd-m-Y H:i:s', $post_id );
			break;
		}
	}

	/**
	 * Ajax send
	 */
	public function fonnletter_broadcast_ajax_request() {
		$ajaxresponse 	= array( 'code' => 400, 'message' => '❌ Error', 'results' => '' );
		$action 		= $_REQUEST['request'];

		switch ( $action ) {
			case 'send_message':
			$url 		= $this->apiurl;
			$token 		= get_option( 'fonnletter_token' );
			$numbers 	= $this->get_phone_numbers( $_POST['campaign'], 'active' );
			
			$args 		= array(
				'headers' => array(
					'Authorization' => $token
				),
			);

			$message = $_REQUEST['message'];
			$message = str_replace('{{nama}}', '{nama}', $message );
			$message = str_replace('{{name}}', '{nama}', $message );

			$args['body'] = array(
				'data' 	=> json_encode( $numbers ),
				'text' 	=> $message . "\r\n\r\n" . 'Untuk berhenti berlangganan balas dengan *STOP* atau *UNSUBSCRIBE*.'
			);

			$response = wp_remote_post( $url, $args );
			if ( !is_wp_error( $response ) ) {
				$response = json_decode( wp_remote_retrieve_body( $response ) );
				if ( isset( $response->status ) && $response->status === true ) {
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
			$settings = $this->settings;
			foreach( $settings as $key ) {
				if ( isset( $_POST[$key] ) ) {
					update_option( $key, esc_attr( $_POST[$key] ) );
				} else {
					update_option( $key, false );
				}
			};
			$ajaxresponse['code'] = 200;
			$ajaxresponse['message'] = '✔️ Settings saved';
			$ajaxresponse['results'] = $settings;
			break;
			case 'reset_settings':
			$settings = $this->settings;
			foreach( $settings as $key ) {
				delete_option( $key );
			};
			$ajaxresponse['code'] = 200;
			$ajaxresponse['message'] = '✔️ Settings has been reset';
			$ajaxresponse['results'] = $settings;
			break;
			case 'submit_campaign':
			if ( !wp_verify_nonce( $_REQUEST['fonnletter_submit_campaign_nonce'], 'fonnletter_submit_campaign' ) ) {
				$ajaxresponse['message'] = 'Cheat\'in uh! :(';
			} else {
				$phone 	= isset( $_POST['_fonnletter_input_phone'] ) && !empty( $_POST['_fonnletter_input_phone'] ) ? $_POST['_fonnletter_input_phone'] : false;

			    	// error if phone not present
				if ( false === $phone ) {
					$ajaxresponse['message'] = 'WhatsApp number is required!';
				} else {
			    		// name
					$name 	= isset( $_POST['_fonnletter_input_name'] ) && !empty( $_POST['_fonnletter_input_name'] ) ? $_POST['_fonnletter_input_name'] : '';
			    		// email
					$email 	= isset( $_POST['_fonnletter_input_email'] ) && !empty( $_POST['_fonnletter_input_email'] ) ? $_POST['_fonnletter_input_email'] : '';

					if ( $email && !is_email( $email ) ) {
			    			// invalid email
						$ajaxresponse['message'] = 'Invalid email address!';	
					} else {
			    			// reformat phone
						$phone = preg_replace('/\s+/', '', $phone);
						if ( !preg_match( '/^[1-9]\d{8,}$/', $phone ) ) {
							$ajaxresponse['message'] = 'Phone number must be country code + phone number!';
						} else {

							if ( !$this->is_phone_registered( $phone, $_POST['campaign_id'] ) || ( 'on' == get_option( 'fonnletter_allow_samenumber' ) && $this->is_phone_registered( $phone ) ) ) {

								if ( ! $this->is_phone_registered( $phone, $_POST['campaign_id'] ) ) {
					    				// insert member
									$autoactivate = 'on' == get_option( 'fonnletter_auto_activate_member' ) ? 'active' : 'inactive';
									$member = wp_insert_post( array(
										'post_type' 	=> 'fonnletter_member',
										'post_title'	=> $name,
										'post_status'	=> 'publish',
										'meta_input'	=> array(
											'_fonnletter_member_campaign_id' 	=> $_POST['campaign_id'],
											'_fonnletter_member_status' 		=> $autoactivate,
											'_fonnletter_member_name' 		=> ucfirst( $name ),
											'_fonnletter_member_phone' 		=> $phone,
											'_fonnletter_member_email' 		=> $email
										)
									), true );

					    				// ok
									if ( !is_wp_error( $member ) ) {
					    					// send
										if ( 'on' ==  get_option( 'fonnletter_wanotif' ) && '' != get_option( 'fonnletter_wanotif_message' ) ) {
											$message = get_option( 'fonnletter_wanotif_message' );
						    					// {{name}} {{phone}} {{email}} {{campaign_name}}
											$message = str_replace( '{{name}}', $name, $message );
											$message = str_replace( '{{phone}}', $phone, $message );
											$message = str_replace( '{{email}}', $email, $message );
											$message = str_replace( '{{campaign_name}}', get_the_title($_POST['campaign_id']), $message );

											$phone = array( 
												array(
													'nama' 	=> $name,
													'nomer'	=> $phone
												)
											);
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
	public function fonnletter_broadcast_metabox() {
		add_meta_box( 
			'formbuilder', 
			__( 'Campaign Form Template', 'fonnletter' ), 
			array( $this, 'fonnletter_broadcast_formbuilder_screen' ), 
			'fonnletter_campaign', 
			'normal', 
			'high' 
		);

		add_meta_box( 
			'membereditor', 
			__( ' Member Detail', 'fonnletter' ), 
			array( $this, 'fonnletter_broadcast_membereditor_screen' ), 
			'fonnletter_member', 
			'normal', 
			'high' 
		);
	}

	/**
	 * Save metabox
	 */
	public function fonnletter_broadcast_metabox_save( $post_id, $post ) {
		if ( 'fonnletter_campaign' == $post->post_type ) {
			foreach ($_POST as $key => $value) {
				if ( false !== strpos( $key, '_fonn' ) ) {
					update_post_meta(
						$post_id,
						$key,
						$value
					);
				}
			}
		} else if ( 'fonnletter_member' == $post->post_type ) {
			foreach ($_POST as $key => $value) {
				if ( false !== strpos( $key, '_fonn' ) ) {
					update_post_meta(
						$post_id,
						$key,
						$value
					);
				}
			}
			update_post_meta( $post_id, '_fonnletter_member_name', $post->post_title );
		} else if ( 'fonnletter_message' == $post->post_type ) {
			foreach ($_POST as $key => $value) {
				if ( false !== strpos( $key, '_fonn' ) ) {
					update_post_meta(
						$post_id,
						$key,
						$value
					);
				}
			}
		}
	}

	/**
	 * HTML Screens
	 */
	public function fonnletter_broadcast_message_screen() {
		include( FONNLETTER_DIR . 'views/admin/pages/broadcast-message.php' );
	}

	/**
	 * HTML Screens
	 */
	public function fonnletter_broadcast_settings_screen() {
		include( FONNLETTER_DIR . 'views/admin/pages/plugin-settings.php' );
	}

	/**
	 * HTML Screens
	 */
	public function fonnletter_broadcast_formbuilder_screen( $post ) {
		$template = get_post_meta( $post->ID, '_fonnletter_template', true );
		$template_html = get_post_meta( $post->ID, '_fonnletter_template_custom', true );
		$form_title = get_post_meta( $post->ID, '_fonnletter_form_title', true );
		$buttontext = '' <> get_post_meta( $post->ID, '_fonnletter_submit_buttontext', true ) ? get_post_meta( $post->ID, '_fonnletter_submit_buttontext', true ) : __( 'Subscribe', 'fonnletter' );
		include( FONNLETTER_DIR . 'views/admin/metaboxes/form-builder.php' );
	}

	/**
	 * HTML Screens
	 */
	public function fonnletter_broadcast_membereditor_screen( $post ) {
		include( FONNLETTER_DIR . 'views/admin/metaboxes/member-editor.php' );
	}

	/**
	 * Submit button
	 */
	public function fonnletter_broadcast_shortcode_submit_button( $id, $template ) {
		$buttontext = get_post_meta( $id, '_fonnletter_submit_buttontext', true );
		include( FONNLETTER_DIR . 'views/front/submit-button.php' );
	}

	/**
	 * Shortcode
	 */
	public function fonnletter_broadcast_shortcode( $atts ) {
		$atts = shortcode_atts( array(
			'id' 	=> '',
		), $atts, 'fonnletter_campaign' );

		extract( $atts );

		if ( '' == $id || ( $id != '' && get_post_type( $id ) !== 'fonnletter_campaign' ) ) {
			return __( 'Invalid campaign ID', 'fonnletter' );
		} else {
			$template = get_post_meta( $id, '_fonnletter_template', true );
			$template_html = get_post_meta( $id, '_fonnletter_template_custom', true );
			$form_title = get_post_meta( $id, '_fonnletter_form_title', true );
			ob_start();
			include( FONNLETTER_DIR . 'views/front/campaign-shortcode.php' );
			if ( $template == 'default' ) {
				wp_enqueue_style( 'fonnletter-campaigncss', plugins_url( '/assets/css/front.css', FONNLETTER_DIR . '/fonnletter' ) );
			}
			wp_enqueue_script( 'fonnletter-campaignjs', plugins_url( '/assets/js/front.js', FONNLETTER_DIR . '/fonnletter' ), array( 'jquery' ), FONNLETTER_VERSION, true );
			wp_localize_script( 'fonnletter-campaignjs', 'fonnletter', array(
				'ajax_url' 		=> admin_url( 'admin-ajax.php' ),
				'spinner_url' 	=> plugins_url( '/assets/images/loader.svg', FONNLETTER_DIR . '/fonnletter' )
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

	public function write_log($log) {
		if ( defined( 'WP_DEBUG' ) && true === WP_DEBUG ) {
			if (is_array($log) || is_object($log)) {
				error_log(print_r($log, true));
			} else {
				error_log($log);
			}
		}
	}

	/**
	 * Check registered
	 */
	public function is_phone_registered( $phone, $campaign_id = null ) {
		$args = array(
			'post_type' 		=> 'fonnletter_member',
			'posts_per_page' 	=> -1,
			'meta_query' 		=> array(
				array(
					'key' 		=> '_fonnletter_member_phone',
					'value'		=> $phone,
					'compare'	=> '='
				)
			)
		);

		if ( $campaign_id != null ) {
			$args['posts_per_page'] = 1;
			$args['meta_query']['relation'] = 'AND';
			$args['meta_query'][] = array(
				'key' 		=> '_fonnletter_member_campaign_id',
				'value'		=> $campaign_id,
				'compare'	=> '='
			);
		}

		$q = new WP_Query( $args );

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
			'post_type' 		=> 'fonnletter_member',
			'posts_per_page' 	=> -1,
		);
		if ( $campaign_id != null ) {
			$args['meta_query'] = array(
				array(
					'key' 		=> '_fonnletter_member_campaign_id',
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
	public function get_phone_numbers( $campaign_id = null, $status = '' ) {
		$numbers = array();
		$args = array(
			'post_type' 		=> 'fonnletter_member',
			'posts_per_page' 	=> -1,
		);

		if ( in_array( $status, array( 'active', 'inactive' ) ) ) {
			$args['meta_query']['relation'] = 'AND';
			$args['meta_query'][] = array(
				'key' 		=> '_fonnletter_member_status',
				'value'		=> $status,
				'compare'	=> '='
			);
		}

		if ( $campaign_id != null && $campaign_id != 'all' ) {
			$args['meta_query'][] = array(
				'key' 		=> '_fonnletter_member_campaign_id',
				'value'		=> $campaign_id,
				'compare'	=> '='
			);
		}

		$q = new WP_Query( $args );

		if ( $q->have_posts() ) {
			while ( $q->have_posts() ) : $q->the_post();
				if ( '' <> get_post_meta( get_the_ID(), '_fonnletter_member_phone', true ) ) {
					$numbers[] = array(
						'nomer' => get_post_meta( get_the_ID(), '_fonnletter_member_phone', true ),
						'nama'	=> get_post_meta( get_the_ID(), '_fonnletter_member_name', true )
					);
				}
			endwhile;
		}
		wp_reset_postdata();

		return $this->remove_duplicate_numbers( $numbers );
	}

	/**
	 * [remove_duplicate_numbers description]
	 * @param  array  $data [description]
	 * @return [type]       [description]
	 */
	public function remove_duplicate_numbers( $numbers = array() ) {
		$exist_number 	= array();
		$unique_numbers = array();
		foreach( $numbers as $data ) {
			if ( !isset( $exist_number[$data['nomer']] ) ) {
				$unique_numbers[] = $data;
				$exist_number[$data['nomer']] = 1;
			}
		}
		return $unique_numbers;
	}

	/**
	 * Send message
	 */
	public function send_message( $phones = array(), $text = '' ) {
		$url 		= $this->apiurl;
		$token 		= get_option( 'fonnletter_token' );

		$args 		= array(
			'headers' => array(
				'Authorization' => $token
			),
		);

		$args['body'] = array(
			'data' 	=> json_encode( $phones ),
			'text' 	=> $text
		);

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

	/**
	 * Rest api
	 */
	public function fonnletter_broadcast_rest_api() {
		register_rest_route( 'fonnletter/v1', '/webhook', array(
			'methods' 	=> 'POST',
			'callback' 	=> array( $this, 'fonnletter_broadcast_rest_api_handler' )
		) );

		// wget --delete-after "YOURDOMAIN/wp-json/fonnletter/v1/followup"
		register_rest_route( 'fonnletter/v1', '/followup', array(
			'methods' 	=> 'GET',
			'callback' 	=> array( $this, 'fonnletter_followup_rest_api_handler' )
		) );
	}

	/**
	 * [fonnletter_followup_rest_api_handler description]
	 * @return [type] [description]
	 */
	public function fonnletter_followup_rest_api_handler() {
		$members = get_posts( array(
			'post_type' 		=> 'fonnletter_member',
			'post_status' 		=> 'publish',
			'numberposts'		=> -1
		) );
		wp_reset_query(); wp_reset_postdata();
		
		if ( !empty( $members ) ) {
			$day = 60 * 60 * 24;
			$current_time = current_time( 'timestamp' );
			
			foreach ( $members as $member ) {
				$time_reg = strtotime( get_the_time( 'd-m-Y H:i:s', $member->ID ) );
				$dsr = (int) ( ($current_time - $time_reg) / $day );

				$get_messages = get_posts( array(
					'post_type' 	=> 'fonnletter_message',
					'post_status' 	=> 'publish',
					'meta_key'		=> '_fonnletter_day_to_send',
					'meta_value'	=> $dsr
				) );
				wp_reset_query(); wp_reset_postdata();

				if ( !empty( $get_messages ) ) {
					foreach( $get_messages as $message ) {
						$name = get_the_title( $member->ID );
						$phone = get_post_meta( $member->ID, '_fonnletter_member_phone', true );
						$email = get_post_meta( $member->ID, '_fonnletter_member_email', true );
						$campaign_id = get_post_meta( $member->ID, '_fonnletter_member_campaign_id', true );

						$pesan = $this->formatted_notification( $message->post_content, array(
							'name' 			=> $name,
							'phone' 		=> $phone,
							'email' 		=> $email,
							'campaign_id'	=> $campaign_id
						) );

						// send
						$this->send_message( array( array(
							'nama' 	=> $name,
							'nomer'	=> $phone
						) ), $pesan );
					}
				}
			}
		}
	}

	/**
	 * [fonnletter_broadcast_rest_api_handler description]
	 * @return [type] [description]
	 */
	public function fonnletter_broadcast_rest_api_handler() {
		header( 'Access-Control-Allow-Headers: Content-Type');
		header( 'Content-Type: text/html; charset=UTF-8');
		extract( $_POST );

		if ( !$this->is_phone_registered( $phone ) ) {
			echo __( 'Nomor Anda belum terdaftar', 'fonnletter' );
		} else {
			$message = trim( strtolower( $message ) );
			$message = preg_replace( '/\s+/', '', $message );
			preg_match( '/(ya|stop|unsubscribe)\s?(\[(\d+)\])?$/', $message, $matches );

			if ( 'info' === $message ) {
				$c = array();
				$q = new WP_Query( array(
					'post_type' => 'fonnletter_member',
					'post_per_page' => -1,
					'meta_query' => array(
						'relation' => 'AND',
						array(
							'key' => '_fonnletter_member_phone',
							'value' => $phone
						),
					)
				) );
				if ( $q->have_posts() ) {
					$i=1;while ( $q->have_posts() ) {
						$q->the_post();
						$id = get_post_meta( get_the_ID(), '_fonnletter_member_campaign_id', true );
						$status = get_post_meta( get_the_ID(), '_fonnletter_member_status', true );
						echo sprintf( '%s. [%s] %s, status: %s', $i++, $id, get_the_title(), $status ) . "\r\n";
					}
					wp_reset_postdata();
				} else {
					echo __( 'Channels not found', 'fonnletter' );
				}
			} else if ( !empty( $matches ) ) {
				$message = $matches[1];
				$campaign_id = isset( $matches[3] ) ? $matches[3] : false;
				$notif = '';
				$activate = array();
				if ( $message == 'ya' ) {
					if ( $campaign_id !== false ) {
						$activate = $this->activate_member( $phone, true, $campaign_id );
					} else {
						$activate = $this->activate_member( $phone, true, true );
					}
					$notif = get_option( 'fonnletter_activation_message' );
				} else if ( in_array( $message, array( 'stop', 'unsubscribe' ) ) ) {
					if ( $campaign_id !== false ) {
						$activate = $this->activate_member( $phone, false, $campaign_id );
					} else {
						$activate = $this->activate_member( $phone, false, true );
					}
					$notif = get_option( 'fonnletter_deactivation_message' );
				}
				if ( !empty( $activate ) && '' <> $notif ) {
					echo $this->formatted_notification( $notif, $activate );
				} else {
					echo __( 'Channel ID is invalid', 'fonnletter' );
				}
			} else {
				if ( '' <> get_option( 'fonnletter_default_info_message' ) ) {
					echo get_option( 'fonnletter_default_info_message' );
				}
			}
		}
	}	

	/**
	 * Get member by phone
	 */
	public function get_member_id_by_phone( $phone, $get_all = false ) {
		$id = array();
		$args = array(
			'post_type' 		=> 'fonnletter_member',
			'meta_query' 		=> array(
				array(
					'key' 		=> '_fonnletter_member_phone',
					'value'		=> $phone,
					'compare'	=> '='
				)
			)
		);
		

		if ( is_numeric( $get_all ) ) {
			$args['posts_per_page'] = 1;
			$args['meta_query']['relation'] = 'AND';
			$args['meta_query'][] = array(
				array(
					'key' 		=> '_fonnletter_member_campaign_id',
					'value'		=> $get_all,
					'compare'	=> '='
				)
			);
		} else if ( true === $get_all ) {
			$args['posts_per_page'] = -1;
		} else {
			$args['posts_per_page'] = 1;
		}

		$q = new WP_Query( $args );

		if ( $q->have_posts() ) {
			while ( $q->have_posts() ) {
				$q->the_post();
				$id[] = get_the_ID();
			}
		}; wp_reset_postdata();
		return $id;
	}

	/**
	 * Activate member
	 */
	public function activate_member( $phone, $activate = true, $all = false ) {
		$data = array();
		if ( !$this->is_phone_registered( $phone ) ) {
			return false;
		}
		$ids = $this->get_member_id_by_phone( $phone, $all );
		if ( !empty( $ids ) ) {
			foreach( $ids as $id ) {
				if ( $activate ) {
					update_post_meta( $id, '_fonnletter_member_status', 'active' );	
				} else {
					update_post_meta( $id, '_fonnletter_member_status', 'inactive' );	
				}
				$data[] = array(
					'id' 			=> $id,
					'name' 			=> get_post_meta( $id, '_fonnletter_member_name', true ),
					'phone' 		=> get_post_meta( $id, '_fonnletter_member_phone', true ),
					'email' 		=> get_post_meta( $id, '_fonnletter_member_email', true ),
					'status' 		=> get_post_meta( $id, '_fonnletter_member_status', true ),
					'campaign_id' 	=> get_post_meta( $id, '_fonnletter_member_campaign_id', true )
				);
			}
			return $data[0];
		}
		return $data;
	}

	/**
	 * Formatted message
	 */
	public function formatted_notification( $message = '', $data = array() ) {
		if ( $message && empty( $data ) ) {
			return $message;
		}

		$message = str_replace( '{{name}}', $data['name'], $message );
		$message = str_replace( '{{phone}}', $data['phone'], $message );
		$message = str_replace( '{{email}}', $data['email'], $message );
		$message = str_replace( '{{campaign_name}}', get_the_title($data['campaign_id']), $message );

		return $message;
	}

	/**
	 * [fonnletter_metabox description]
	 * @return [type] [description]
	 */
	public function fonnletter_metabox() {
		add_meta_box( 'fonnletter_follow_up', 'Jadwal', array( $this, 'fonnletter_schedule_message_screen' ), 'fonnletter_message', 'normal', 'high' );
	}

	/**
	 * [fonnletter_schedule_message_screen description]
	 * @param  [type] $post_id [description]
	 * @return [type]          [description]
	 */
	public function fonnletter_schedule_message_screen( $post ) {
		include( FONNLETTER_DIR . 'views/admin/metaboxes/message-editor.php' );
	}

	/**
	 * Manage admin columns
	 */
	public function manage_fonnletter_message_columns( $columns ) {
		unset( $columns['date'] );
		$columns['title']		= __( 'Judul', 'fonnletter' );
		$columns['day_send_to'] = __( 'Dikirim pada hari ke', 'fonnletter' );
		return $columns;
	}

	/**
	 * Set values admin column
	 */
	public function manage_fonnletter_message_custom_columns( $column, $post_id ) {
		switch ( $column ) {
			case 'day_send_to':
			echo get_post_meta( $post_id, '_fonnletter_day_to_send', true );
			break;
		}
	}

}