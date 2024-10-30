<?php 


function fca_lpc_admin_notices() {
	if( current_user_can('manage_options') ) {
		
		fca_lpc_admin_backward_compatibility_notice();
		
		fca_lpc_admin_review_notice();
		
		if ( FCA_LPC_PLUGIN_PACKAGE !== 'Free' ){
			fca_lpc_admin_licence_notice();
		}
	}

}
add_action( 'admin_notices', 'fca_lpc_admin_notices' );	

function fca_lpc_admin_review_notice() {
	
	$nonceVerified = empty( $_GET['fca_lpc_review_nonce'] ) ? false : wp_verify_nonce( $_GET['fca_lpc_review_nonce'], 'fca_lpc_review_nonce' );
	
	if ( !empty( $_GET['fca_lpc_review_nonce'] ) && $nonceVerified == false ) {

		wp_die( 'Nonce not verified. Please log in and try the action again.' );

	}
	
	if ( isSet( $_GET['fca_lpc_leave_review'] ) && $nonceVerified ) {

		$review_url = 'https://wordpress.org/support/plugin/landing-page-cat/reviews/?filter=5';
		update_option( 'fca_lpc_show_review_notice', false );
		wp_redirect($review_url);
		exit;

	}

	$show_review_option = get_option( 'fca_lpc_show_review_notice', 'not-set' );

	if ( $show_review_option === 'not-set' && !wp_next_scheduled( 'fca_lpc_schedule_review_notice' )  ) {
		wp_schedule_single_event( time() + 30 * DAY_IN_SECONDS, 'fca_lpc_schedule_review_notice' );
	}

	if ( isSet( $_GET['fca_lpc_postpone_review_notice'] ) && $nonceVerified ) {
		$show_review_option = false;
		update_option( 'fca_lpc_show_review_notice', $show_review_option );
		wp_schedule_single_event( time() + 30 * DAY_IN_SECONDS, 'fca_lpc_schedule_review_notice' );
	}

	if ( isSet( $_GET['fca_lpc_forever_dismiss_notice'] ) && $nonceVerified ) {
		$show_review_option = false;
		update_option( 'fca_lpc_show_review_notice', $show_review_option );
	}
	
	$fca_lpc_review_nonce = wp_create_nonce( 'fca_lpc_review_nonce' );
	$review_url = esc_url( add_query_arg( array( 'fca_lpc_leave_review' => 1, 'fca_lpc_review_nonce' => $fca_lpc_review_nonce ) ) );
	$postpone_url = esc_url( add_query_arg( array( 'fca_lpc_postpone_review_notice' => 1, 'fca_lpc_review_nonce' => $fca_lpc_review_nonce ) ) );
	$forever_dismiss_url = esc_url( add_query_arg( array( 'fca_lpc_forever_dismiss_notice' => 1, 'fca_lpc_review_nonce' => $fca_lpc_review_nonce ) ) );

	if ( $show_review_option && $show_review_option !== 'not-set' ){

		$plugin_name = 'landing-page-cat';

		echo '<div id="fca-pc-setup-notice" class="notice notice-success is-dismissible" style="padding-bottom: 8px; padding-top: 8px;">';
			echo '<img height="64" width="64" style="float:left;margin-right:12px;" src="' . FCA_LPC_PLUGINS_URL . '/icons/icon-128x128.png' . '">';
			echo '<p>' . __( "Hi! You've been using Landing Page Cat for a while now, so who better to ask for a review than you? Would you please mind leaving us one? It really helps us a lot!", $plugin_name ) . '</p>';
			echo "<a href='$review_url' class='button button-primary' style='margin-top: 2px;'>" . __( 'Leave review', $plugin_name) . "</a> ";
			echo "<a style='position: relative; top: 10px; left: 7px;' href='$postpone_url' >" . __( 'Maybe later', $plugin_name) . "</a> ";
			echo "<a style='position: relative; top: 10px; left: 16px;' href='$forever_dismiss_url' >" . __( 'No thank you', $plugin_name) . "</a> ";
			echo '<br style="clear:both">';
		echo '</div>';

	}
}

function fca_lpc_admin_backward_compatibility_notice() {
	if ( isset ( $_GET['fca_lpc_run_upgrade'] ) ) {
		//RUN ACTIVATION HOOK WHICH CONTAINS BACKWARD COMPATIBILITY STUFF
		$posts_updated = fca_lpc_set_bg_image_file_paths();
		
		echo '<div class="notice notice-info">';
			echo "<p>$posts_updated " . __('Landing Pages Updated.', 'landing-page-cat' ) . '</p>';
		echo '</div>';	
	} else if ( get_option( 'fca_lpc_meta_version' ) !== '1.2.0' ) {
		echo '<div class="notice notice-info">';
			echo '<img height="120" width="120" style="float:left;" src="' . FCA_LPC_PLUGINS_URL . '/icons/icon-128x128.png' . '">';
			echo '<p><strong>' . __('Landing Page Cat 1.2 Update', 'landing-page-cat' ) . '</strong></p>';
			echo '<p>' . __("Landing Page Cat needs to update any previous landing pages for compatibility with the new version.", 'landing-page-cat' );
			echo '<p>' . __("This should only take a moment.", 'landing-page-cat' );
			echo "<p><a class='button button-primary' href='" . esc_url( add_query_arg( 'fca_lpc_run_upgrade', 'true' ) ) . "'>" . __('OK', 'landing-page-cat' ) . '</a></p>';
		echo '</div>';
	}	
}

function fca_lpc_admin_licence_notice() {
	$license  = get_option( 'fca_lpc_license_key' );
	$status	  = get_option( 'fca_lpc_license_status' );
	$licensing_page = admin_url( 'edit.php?post_type=landingpage&page=fca_lpc_license_page' );
	$current_page = empty( $_GET['page'] ) ? '' : $_GET['page'];
		
	if ( $status === 'expired' ) {
		echo '<div class="error">';
			echo '<p>' . __('Your License Key for Landing Page Cat Premium has expired.', 'landing-page-cat' ) . " <a href='https://fatcatapps.com/checkout/?edd_license_key=$license' target='_blank'>" . __('Click here to renew', 'landing-page-cat') . '</a>.</p>';
		echo '</div>';		
	}
	
	if ( $status === 'invalid' && $current_page !== 'fca_lpc_license_page' ) {
		echo '<div class="error">';
			echo '<p>' . __('You have not yet entered your license key for Landing Page Cat Premium.', 'landing-page-cat' ) . " <a href='$licensing_page'>" . __('Click here and activate to receive updates & support', 'landing-page-cat') . '</a>.</p>';
		echo '</div>';		
	}

	if ( isset( $_GET['sl_activation'] ) && ! empty( $_GET['message'] ) ) {
		switch( $_GET['sl_activation'] ) {
			case 'false':
				$message = urldecode( $_GET['message'] );
				?>
				<div class="error">
					<p><?php echo $message; ?></p>
				</div>
				<?php
				break;
			case 'true':
			default:
				// Developers can put a custom success message here for when activation is successful if they way.
				break;
		}
	}
	
}

function fca_lpc_enable_review_notice(){
	update_option( 'fca_lpc_show_review_notice', true );
	wp_clear_scheduled_hook( 'fca_lpc_schedule_review_notice' );
}

add_action ( 'fca_lpc_schedule_review_notice', 'fca_lpc_enable_review_notice' );

