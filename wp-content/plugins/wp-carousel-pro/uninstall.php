<?php
/**
 * Uninstall.php for cleaning plugin database.
 *
 * Trigger the file when plugin is deleted.
 *
 * @see delete_option(), delete_post_meta_key()
 * @since 3.1.0
 * @package WP Carousel Pro
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

/**
 * Delete plugin data function.
 *
 * @return void
 */
function sp_wpcp_delete_plugin_data() {

	// Delete plugin option settings.
	$option_name = 'sp_wpcp_settings';
	delete_option( $option_name );
	delete_site_option( $option_name ); // For site options in Multisite.

	// Delete carousel post type.
	$carousel_posts = get_posts(
		array(
			'numberposts' => -1,
			'post_type'   => 'sp_wp_carousel',
			'post_status' => 'any',
		)
	);
	foreach ( $carousel_posts as $post ) {
		wp_delete_post( $post->ID, true );
	}

	// Delete Carousel post meta.
	delete_post_meta_by_key( 'sp_wpcp_upload_options' );
	delete_post_meta_by_key( 'sp_wpcp_shortcode_options' );
}

	// Load WPCP file.
	require plugin_dir_path( __FILE__ ) . '/wp-carousel-pro.php';
	$wpcp_plugin_data = wpcp_get_option( 'wpcp_delete_all_data', false );

if ( $wpcp_plugin_data ) {
	sp_wpcp_delete_plugin_data();
}
