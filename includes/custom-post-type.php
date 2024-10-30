<?php 

////////////////////////////
// SET UP POST TYPE
////////////////////////////

//REGISTER CPT
function fca_lpc_register_post_type() {
	
	$labels = array(
		'name' => _x('Landing Pages','landing-page-cat'),
		'singular_name' => _x('Landing Page','landing-page-cat'),
		'add_new' => _x('Add New','landing-page-cat'),
		'all_items' => _x('All Landing Pages','landing-page-cat'),
		'add_new_item' => _x('Add New Landing Page','landing-page-cat'),
		'edit_item' => _x('Edit Landing Page','landing-page-cat'),
		'new_item' => _x('New Landing Page','landing-page-cat'),
		'view_item' => _x('View Landing Page','landing-page-cat'),
		'search_items' => _x('Search Landing Pages','landing-page-cat'),
		'not_found' => _x('Landing Page not found','landing-page-cat'),
		'not_found_in_trash' => _x('No Landing Pages found in trash','landing-page-cat'),
		'parent_item_colon' => _x('Parent Landing Page:','landing-page-cat'),
		'menu_name' => _x('Landing Pages','landing-page-cat')
	);
		
	$args = array(
		'labels' => $labels,
		'description' => "",
		'public' => false,
		'exclude_from_search' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'show_in_nav_menus' => false,
		'show_in_menu' => true,
		'show_in_admin_bar' => true,
		'menu_position' => 101,
		'menu_icon' => FCA_LPC_PLUGINS_URL . '/icons/icon.png',
		'capability_type' => 'post',
        'capabilities' => array(
            'edit_post' => 'manage_options',
            'read_post' => 'manage_options',
            'delete_post' => 'manage_options',
            'edit_posts' => 'manage_options',
            'edit_others_posts' => 'manage_options',
            'publish_posts' => 'manage_options',
            'read_private_posts' => 'manage_options'
        ),
		'hierarchical' => false,
		'supports' => array('title','thumbnail'),
		'has_archive' => false,
		'rewrite' => false,
		'query_var' => true,
		'can_export' => true
	);
	
	register_post_type( 'landingpage', $args );
}
add_action ( 'init', 'fca_lpc_register_post_type' );


function fca_lpc_post_type_url( $url, $post ) {
	if ( get_post_type( $post ) === 'landingpage' ) {
		return home_url( '/?landingpage=' . $post->ID );
	}
	return $url;
}
add_filter( 'post_type_link', 'fca_lpc_post_type_url', 10, 2 );

function fca_lpc_add_plugin_action_links( $links ) {
	
	$support_url = FCA_LPC_PLUGIN_PACKAGE === 'Free' ? 'https://wordpress.org/support/plugin/landing-page-cat' : 'https://fatcatapps.com/support';
	
	$new_links = array(
		'support' => "<a target='_blank' href='$support_url' >" . __('Support', 'quiz-cat' ) . '</a>'
	);
	
	$links = array_merge( $new_links, $links );

	return $links;
	
}
add_filter( 'plugin_action_links_' . FCA_LPC_PLUGINS_BASENAME, 'fca_lpc_add_plugin_action_links' );

//CHANGE CUSTOM 'UPDATED' MESSAGES FOR OUR CPT
function fca_lpc_post_updated_messages( $messages ){
	
	$post = get_post();
	$preview_url = get_site_url() . '?landingpage=' . $post->ID;
	$messages['landingpage'] = array(
		0  => '', // Unused. Messages start at index 1.
		1  => sprintf( __( 'Landing Page updated. %sView Preview%s','landing-page-cat'), "<a href='$preview_url' target='_blank'>", '</a>' ),
		2  => sprintf( __( 'Landing Page updated. %sView Preview%s','landing-page-cat'), "<a href='$preview_url' target='_blank'>", '</a>' ),
		3  => __( 'Landing Page deleted.','landing-page-cat'),
		4  => sprintf( __( 'Landing Page updated.  %sView Preview%s.','landing-page-cat'), "<a href='$preview_url' target='_blank'>", '</a>' ),
		/* translators: %s: date and time of the revision */
		5  => isset( $_GET['revision'] ) ? sprintf( __( 'Landing Page restored to revision from %s','landing-page-cat'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		6  => __( 'Landing Page published.' ,'landing-page-cat'),
		7  => __( 'Landing Page saved.' ,'landing-page-cat'),
		8  => __( 'Landing Page submitted.' ,'landing-page-cat'),
		9  => sprintf(
			__( 'Landing Page scheduled for: <strong>%1$s</strong>.','landing-page-cat'),
			// translators: Publish box date format, see http://php.net/date
			date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) )
		),
		10 => __( 'Landing Page draft updated.' ,'landing-page-cat'),
	);

	return $messages;
}
add_filter('post_updated_messages', 'fca_lpc_post_updated_messages' );


//Customize CPT table columns
function fca_lpc_add_new_post_table_columns($columns) {
	$new_columns = array();
	$new_columns['cb'] = '<input type="checkbox" />';
	$new_columns['title'] = _x('Title', 'column name', 'landing-page-cat');
	$new_columns['state'] = __('Current state', 'landing-page-cat');
	$new_columns['date'] = _x('Date', 'column name', 'landing-page-cat');
 
	return $new_columns;
}
add_filter('manage_edit-landingpage_columns', 'fca_lpc_add_new_post_table_columns', 10, 1 );

function fca_lpc_manage_post_table_columns($column_name, $id) {

	$meta = get_post_meta ( $id, 'fca_lpc', true );
	if ( $meta['deploy_mode'] !== 'disabled' ){
		$behavior = 'Enabled';
	} else {
		$behavior = 'Disabled';
	}
	switch ($column_name) {
		case 'state':
			echo $behavior;
				break;
	 
		default:
		break;
	} // end switch
}
add_action('manage_landingpage_posts_custom_column', 'fca_lpc_manage_post_table_columns', 10, 2);

function fca_lpc_remove_screen_options_tab ( $show_screen, $screen ) {
	if ( $screen->id == 'landingpage' ) {
		return false;
	}
	return $show_screen;
}	
add_filter('screen_options_show_screen', 'fca_lpc_remove_screen_options_tab', 10, 2);
