<?php
/**
 * Scripts
 *
 * @package     MYC
 * @subpackage  Functions
 * @copyright   Copyright (c) 2017, Daniel Powney
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Load Scripts
 *
 * Enqueues the required scripts.
 *
 * @since 0.1
 * @return void
 */
function myc_load_scripts() {

	$js_dir = MYC_PLUGIN_URL . 'assets/js/';

	// Use minified libraries if SCRIPT_DEBUG is turned off
	$suffix = ''; //( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-effects-slide');

	wp_register_script( 'myc-script', $js_dir . 'frontend' . $suffix . '.js', array( 'jquery', 'jquery-ui-core', 'jquery-effects-slide' ), MYC_VERSION );
	wp_enqueue_script( 'myc-script' );

	$general_settings = (array) get_option( 'myc_general_settings' );
	$overlay_settings = (array) get_option( 'myc_overlay_settings' );

	$session_id = null;
	if ( isset( $_COOKIE['myc_session_id'] ) && strlen( $_COOKIE['myc_session_id'] ) > 0 ) {
		$session_id = $_COOKIE['myc_session_id'];
	} else {
		$session_id = md5( uniqid( 'myc-' ) ); // do not set cookie here as headers have already been set
	}

	$myc_script_vars = array(
			'enable_welcome_event' 	=> apply_filters( 'myc_script_enable_welcome_event', $general_settings['enable_welcome_event'] ),
			'messaging_platform' 	=> apply_filters( 'myc_script_messaging_platform', $general_settings['messaging_platform'] ),
			'messages' 				=> array(
					'internal_error' 		=> __( 'An internal error occured', 'my-chatbot' ),
					'input_unknown' 		=> __( 'I\'m sorry I do not understand.', 'my-chatbot' )
			),
			'session_id' 			=> apply_filters( 'myc_script_session_id', $session_id ),
			'show_time' 			=> apply_filters( 'myc_script_show_time', $general_settings['show_time'] ),
			'keep_conversation_history' 			=> apply_filters( 'myc_script_keep_conversation_history', $general_settings['keep_conversation_history'] ),
			'show_loading' 			=> apply_filters( 'myc_script_show_loading', $general_settings['show_loading'] ),
			'response_delay' 		=> apply_filters( 'myc_script_response_delay', $general_settings['response_delay'] ),
			'overlay_default_open'		=> $overlay_settings['overlay_default_open'],
			'language' 				=> apply_filters( 'myc_language', $general_settings['language'] ),
			'response_bubble_alignment' => apply_filters( 'myc_response_bubble_alignment', $general_settings['response_bubble_alignment'] ),
			'wpApiSettings' 		=> array(
				'root' => esc_url_raw( rest_url() ),
	    		'nonce' => wp_create_nonce( 'wp_rest' )
			)
	);

	wp_localize_script( 'myc-script', 'myc_script_vars', apply_filters( 'myc_script_vars', $myc_script_vars ) );

}
add_action( 'wp_enqueue_scripts', 'myc_load_scripts' );


/**
 * Register Styles
 *
 * Checks the styles option and hooks the required filter.
 *
 * @since 0.1
 * @return void
*/
function myc_register_styles() {

	$general_settings = (array) get_option( 'myc_general_settings' );
	$overlay_settings = (array) get_option( 'myc_overlay_settings' );

	$disable_css_styles = isset( $general_settings['disable_css_styles'] ) ? $general_settings['disable_css_styles'] : false;

	if ( $disable_css_styles ) {
		return;
	}

	$css_dir = MYC_PLUGIN_URL . 'assets/css/';

	// Use minified libraries if SCRIPT_DEBUG is turned off
	$suffix = ''; //( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

	wp_register_style( 'myc-style', $css_dir . 'frontend' . $suffix . '.css', array(), MYC_VERSION, 'all' );

	$custom_css = '
		.myc-conversation-area .myc-icon-loading-dot {
			color: ' . $general_settings['loading_dots_color'] . ';
		}
		.myc-conversation-response {
			background-color: ' . $general_settings['response_background_color'] . ';
			color: ' . $general_settings['response_font_color'] . ';
		}
		.myc-conversation-request {
			background-color: ' . $general_settings['request_background_color'] . ';
			color: ' . $general_settings['request_font_color'] . ';
		}
		.myc-content-overlay-header {
			background-color: ' . $overlay_settings['overlay_header_background_color'] . ';
			color: ' . $overlay_settings['overlay_header_font_color'] . ';
		}
		.myc-conversation-bubble {
			opacity: ' . $general_settings['non_current_opacity'] . ';
    		filter: alpha(opacity=' . 100 * intval( $general_settings['non_current_opacity'] ) . ' ); /* For IE8 and earlier */
		}
		.myc-is-active {
			opacity: 1.0;
    		filter: alpha(opacity=100); /* For IE8 and earlier */
		}
		.myc-conversation-area, .myc-text {
			font-size: ' . $general_settings['conversation_font_size'] . '%;
		}
	';

	if ( $general_settings['response_bubble_alignment'] === 'left' ) {
		
		$custom_css .= '
			.myc-conversation-bubble-container-response {
				text-align: left;
			}
			.myc-conversation-response:before {
				position: absolute;
			    left: -5px;
			    background-color: ' . $general_settings['response_background_color'] . ';
			    content: " ";
			    display: block;
			    top: 30%;
			    height: 10px;
			    width: 10px;
			    transform: rotate(45deg);
			    -webkit-transform: rotate(45deg);
			    -moz-transform: rotate(45deg);
			    -ms-transform: rotate(45deg);
			}
    	';

	} else { // resonse alignment right
		
		$custom_css .= '
			.myc-conversation-bubble-container-response {
				text-align: right;
			}
			.myc-conversation-response:after {
				position: absolute;
			    right: -5px;
			    background-color: ' . $general_settings['response_background_color'] . ';
			    content: " ";
			    display: block;
			    top: 30%;
			    height: 10px;
			    width: 10px;
			    transform: rotate(45deg);
			    -webkit-transform: rotate(45deg);
			    -moz-transform: rotate(45deg);
			    -ms-transform: rotate(45deg);
			}
		';

	}

	if ( $general_settings['request_bubble_alignment'] === 'right') {
		
		$custom_css .= '
			.myc-conversation-bubble-container-request {
				text-align: right;
			}
			.myc-conversation-request:before {
				position: absolute;
			    right: -5px;
			    background-color: ' . $general_settings['request_background_color'] . ';
			    content: " ";
			    display: block;
			    top: 30%;
			    height: 10px;
			    width: 10px;
			    transform: rotate(45deg);
			    -webkit-transform: rotate(45deg);
			    -moz-transform: rotate(45deg);
			    -ms-transform: rotate(45deg);
			}
		';

	} else { // request alignment left
		
		$custom_css .= '
			.myc-conversation-bubble-container-request {
				text-align: left;
			}
			.myc-conversation-request:before {
				position: absolute;
			    left: -5px;
			    background-color: ' . $general_settings['request_background_color'] . ';
			    content: " ";
			    display: block;
			    top: 30%;
			    height: 10px;
			    width: 10px;
			    transform: rotate(45deg);
			    -webkit-transform: rotate(45deg);
			    -moz-transform: rotate(45deg);
			    -ms-transform: rotate(45deg);
			}
		';
	}

	wp_add_inline_style( 'myc-style', apply_filters( 'myc_custom_css', $custom_css ) );
	wp_enqueue_style( 'myc-style' );
}
add_action( 'wp_enqueue_scripts', 'myc_register_styles' );


/**
 * Load Admin Scripts
 *
 * Enqueues the required admin scripts.
 *
 * @since 0.1
 * @return void
 */
function myc_load_admin_scripts() {

	$js_dir = MYC_PLUGIN_URL . 'assets/js/';
	$css_dir = MYC_PLUGIN_URL . 'assets/css/';

	// Use minified libraries if SCRIPT_DEBUG is turned off
	$suffix = ''; //( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-effects-slide');
	
	wp_register_script( 'myc-admin-script', $js_dir . 'admin' . $suffix . '.js', array( 'jquery', 'jquery-ui-core', 'jquery-effects-slide' ), MYC_VERSION );
	wp_enqueue_script( 'myc-admin-script' );

	wp_enqueue_style( 'myc-admin-style', $css_dir . 'admin' . $suffix . '.css' );

	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_script( 'wp-color-picker' );

}
add_action( 'admin_enqueue_scripts', 'myc_load_admin_scripts' );



/**
 *
 */
function myc_get_language( $language ) {

	if ( $language == '' ) {

		$locale = get_locale();

		$language_map = array(
				'pt_BR' 	=> 'pt-BR',
				'zh_HK'		=> 'zh-HK',
				'zh_CN'		=> 'zh-CN',
				'zh_TW'		=> 'zh-TW',
				''			=> 'en',
				'en_AU'		=> 'en-AU',
				'en_CA'		=> 'en-CA',
				'en_GB'		=> 'en-GB',
				'en_IN'		=> 'en-IN',
				'en_US'		=> 'en-US',
				'nl_NL'		=> 'nl',
				'fr_FR'		=> 'fr',
				'fr_CA'		=> 'fr-CA',
				'de_DE'		=> 'de',
				'it_IT'		=> 'it',
				'ja'		=> 'ja',
				'ko_KR'		=> 'ko',
				'pt_PT'		=> 'pt',
				'ru_RU'		=> 'ru',
				'es'		=> 'es',
				'es_ES'		=> 'es',
				'es_VE'		=> 'es-419',
				'es_MX'		=> 'es-419',
				'es_CR'		=> 'es-419',
				'es_GT'		=> 'es-419',
				'es_CL'		=> 'es-419',
				'es_PE'		=> 'es-419',
				'es_AR'		=> 'es-419',
				'es_CO'		=> 'es-419',
				'uk'		=> 'uk',
		);

		return $language_map[$locale];
	}

	return $language;
}
add_filter( 'myc_language', 'myc_get_language' );
