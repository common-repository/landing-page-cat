<?php
/*
	Plugin Name: Landing Page Cat Free
	Plugin URI: https://fatcatapps.com/landing-page-cat
	Description: Provides an easy way to create landing pages
	Text Domain: landing-page-cat
	Domain Path: /languages
	Author: Fatcat Apps
	Author URI: https://fatcatapps.com/
	License: GPLv2
	Version: 1.7.6
*/

// BASIC SECURITY
defined( 'ABSPATH' ) or die( 'Unauthorized Access!' );



if ( !defined('FCA_LPC_PLUGIN_DIR') ) {
	
	//DEFINE SOME USEFUL CONSTANTS
	define( 'FCA_LPC_PLUGIN_VER', '1.7.6' );
	define( 'FCA_LPC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
	define( 'FCA_LPC_PLUGINS_URL', plugins_url( '', __FILE__ ) );
	define( 'FCA_LPC_PLUGIN_FILE', __FILE__ );
	define( 'FCA_LPC_PLUGIN_PACKAGE', 'Free' ); //DONT CHANGE THIS, IT WONT ADD FEATURES, ONLY BREAKS UPDATER AND LICENSE

	define( 'FCA_LPC_PLUGINS_BASENAME', plugin_basename(__FILE__) );
		
	//LOAD CORE
	include_once( FCA_LPC_PLUGIN_DIR . '/includes/functions.php' );
	include_once( FCA_LPC_PLUGIN_DIR . '/includes/api.php' );
	
	//LOAD MODULES
	include_once( FCA_LPC_PLUGIN_DIR . '/includes/notices.php' );
	include_once( FCA_LPC_PLUGIN_DIR . '/includes/subscribers.php' );
	include_once( FCA_LPC_PLUGIN_DIR . '/includes/landing/landing.php' );
	include_once( FCA_LPC_PLUGIN_DIR . '/includes/editor/editor.php' );
	include_once( FCA_LPC_PLUGIN_DIR . '/includes/custom-post-type.php' );
	if ( file_exists ( FCA_LPC_PLUGIN_DIR . '/includes/editor/editor-premium.php' ) ) {
		include_once( FCA_LPC_PLUGIN_DIR . '/includes/editor/editor-premium.php' );
	}
	if ( file_exists ( FCA_LPC_PLUGIN_DIR . '/includes/landing/landing-premium.php' ) ) {
		include_once( FCA_LPC_PLUGIN_DIR . '/includes/landing/landing-premium.php' );
	}
	if ( file_exists ( FCA_LPC_PLUGIN_DIR . '/includes/editor/sidebar.php' ) ) {
		include_once( FCA_LPC_PLUGIN_DIR . '/includes/editor/sidebar.php' );
	}
	if ( file_exists ( FCA_LPC_PLUGIN_DIR . '/includes/upgrade.php' ) ) {
		include_once( FCA_LPC_PLUGIN_DIR . '/includes/upgrade.php' );
	}	
	if ( file_exists ( FCA_LPC_PLUGIN_DIR . '/includes/licensing/licensing.php' ) ) {
		include_once( FCA_LPC_PLUGIN_DIR . '/includes/licensing/licensing.php' );
	}
	
	function fca_lpc_activation() {		
		fca_lpc_set_bg_image_file_paths();
	}
	register_activation_hook( FCA_LPC_PLUGIN_FILE, 'fca_lpc_activation' );

	
	//DEACTIVATION SURVEY
	function fca_lpc_admin_deactivation_survey( $hook ) {
		if ( $hook === 'plugins.php' ) {
			
			ob_start(); ?>
			
			<div id="fca-deactivate" style="position: fixed; left: 232px; top: 191px; border: 1px solid #979797; background-color: white; z-index: 9999; padding: 12px; max-width: 669px;">
				<h3 style="font-size: 14px; border-bottom: 1px solid #979797; padding-bottom: 8px; margin-top: 0;"><?php _e( 'Sorry to see you go', 'landing-page-cat' ) ?></h3>
				<p><?php _e( 'Hi, this is David, the creator of Landing Page Cat. Thanks so much for giving my plugin a try. I’m sorry that you didn’t love it.', 'landing-page-cat' ) ?>
				</p>
				<p><?php _e( 'I have a quick question that I hope you’ll answer to help us make Landing Page Cat better: what made you deactivate?', 'landing-page-cat' ) ?>
				</p>
				<p><?php _e( 'You can leave me a message below. I’d really appreciate it.', 'landing-page-cat' ) ?>
				</p>
				<p><b><?php _e( 'If you\'re upgrading to Landing Page Cat Premium and have questions or need help, click <a href=' . 'https://fatcatapps.com/article-categories/gen-getting-started/' . ' target="_blank">here</a></b>', 'landing-page-cat' ) ?>
				</p>

				<p><textarea style='width: 100%;' id='fca-lpc-deactivate-textarea' placeholder='<?php _e( 'What made you deactivate?', 'landing-page-cat' ) ?>'></textarea></p>
				
				<div style='float: right;' id='fca-deactivate-nav'>
					<button style='margin-right: 5px;' type='button' class='button button-secondary' id='fca-lpc-deactivate-skip'><?php _e( 'Skip', 'landing-page-cat' ) ?></button>
					<button type='button' class='button button-primary' id='fca-lpc-deactivate-send'><?php _e( 'Send Feedback', 'landing-page-cat' ) ?></button>
				</div>
			
			</div>
			
			<?php
				
			$html = ob_get_clean();
			
			$data = array(
				'html' => $html,
				'nonce' => wp_create_nonce( 'fca_lpc_uninstall_nonce' ),
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
			);
						
			wp_enqueue_script('fca_lpc_deactivation_js', FCA_LPC_PLUGINS_URL . '/includes/deactivation.min.js', false, FCA_LPC_PLUGIN_VER, true );
			wp_localize_script( 'fca_lpc_deactivation_js', "fca_lpc", $data );
		}
		
		
	}	
	add_action( 'admin_enqueue_scripts', 'fca_lpc_admin_deactivation_survey' );
	

}

