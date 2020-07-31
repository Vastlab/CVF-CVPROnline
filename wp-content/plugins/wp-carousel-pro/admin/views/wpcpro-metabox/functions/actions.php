<?php if ( ! defined( 'ABSPATH' ) ) {
	die; } // Cannot access directly.


if ( ! function_exists( 'wpcp_get_option' ) ) {
	/**
	 * The wpcp_get_option function.
	 *
	 * @param string $option The option unique ID.
	 * @param mixed  $default The default value for the option.
	 * @return statement
	 */
	function wpcp_get_option( $option = '', $default = null ) {
		$options = get_option( 'sp_wpcp_settings' );
		return ( isset( $options[ $option ] ) ) ? $options[ $option ] : $default;
	}
}


/**
 * Populate the taxonomy name list to he select option.
 *
 * @return void
 */
function wpcp_get_taxonomies() {
	extract( $_REQUEST );
	$taxonomy_names = get_object_taxonomies( array( 'post_type' => $wpcp_post_type ), 'names' );
	foreach ( $taxonomy_names as $key => $label ) {
		echo '<option value="' . $label . '">' . $label . '</option>';
	}
	die( 0 );
}
add_action( 'wp_ajax_wpcp_get_taxonomies', 'wpcp_get_taxonomies' );

/**
 * Populate the taxonomy terms list to the select option.
 *
 * @return void
 */
function wpcp_get_terms() {
	extract( $_REQUEST );
	$terms = get_terms( $wpcp_post_taxonomy );
	foreach ( $terms as $key => $value ) {
		echo '<option value="' . $value->term_id . '">' . $value->name . '</option>';
	}
	die( 0 );
}
add_action( 'wp_ajax_wpcp_get_terms', 'wpcp_get_terms' );

/**
 * Get specific post to the select box.
 *
 * @return void
 */
function wpcp_get_posts() {
	extract( $_REQUEST );
	$all_posts = get_posts(
		array(
			'post_type'      => $wpcp_post_type,
			'posts_per_page' => -1,
		)
	);
	if ( ! is_wp_error( $all_posts ) && ! empty( $all_posts ) ) { 
		foreach ( $all_posts as $key => $post_obj ) {
			echo '<option value="' . $post_obj->ID . '">' . $post_obj->post_title . '</option>';
		}
	}
	wp_reset_postdata();
	die( 0 );
}
add_action( 'wp_ajax_wpcp_get_posts', 'wpcp_get_posts' );

/**
 *
 * Get icons from admin ajax
 *
 * @since 1.0.0
 * @version 1.0.0
 */
if ( ! function_exists( 'spf_get_icons' ) ) {
	function spf_get_icons() {

		if ( ! empty( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], 'spf_icon_nonce' ) ) {

			ob_start();

			SP_WPCP::include_plugin_file( 'fields/icon/default-icons.php' );

			$icon_lists = apply_filters( 'spf_field_icon_add_icons', spf_get_default_icons() );

			if ( ! empty( $icon_lists ) ) {

				foreach ( $icon_lists as $list ) {

					echo ( count( $icon_lists ) >= 2 ) ? '<div class="spf-icon-title">' . $list['title'] . '</div>' : '';

					foreach ( $list['icons'] as $icon ) {
						echo '<a class="spf-icon-tooltip" data-spf-icon="' . $icon . '" title="' . $icon . '"><span class="spf-icon spf-selector"><i class="' . $icon . '"></i></span></a>';
					}
				}
			} else {

						echo '<div class="spf-text-error">' . esc_html__( 'No data provided by developer', 'wp-carousel-pro' ) . '</div>';

			}

			wp_send_json_success(
				array(
					'success' => true,
					'content' => ob_get_clean(),
				)
			);

		} else {

			wp_send_json_error(
				array(
					'success' => false,
					'error'   => esc_html__( 'Error while saving.', 'wp-carousel-pro' ),
					'debug'   => $_REQUEST,
				)
			);

		}

	}
	add_action( 'wp_ajax_spf-get-icons', 'spf_get_icons' );
}

/**
 *
 * Export
 *
 * @since 1.0.0
 * @version 1.0.0
 */
if ( ! function_exists( 'spf_export' ) ) {
	function spf_export() {

		if ( ! empty( $_GET['export'] ) && ! empty( $_GET['nonce'] ) && wp_verify_nonce( $_GET['nonce'], 'spf_backup_nonce' ) ) {

			header( 'Content-Type: application/json' );
			header( 'Content-disposition: attachment; filename=backup-' . gmdate( 'd-m-Y' ) . '.json' );
			header( 'Content-Transfer-Encoding: binary' );
			header( 'Pragma: no-cache' );
			header( 'Expires: 0' );

			echo json_encode( get_option( wp_unslash( $_GET['export'] ) ) );

		}

		die();
	}
	add_action( 'wp_ajax_spf-export', 'spf_export' );
}


/**
 *
 * Import Ajax
 *
 * @since 1.0.0
 * @version 1.0.0
 */
if ( ! function_exists( 'spf_import_ajax' ) ) {
	function spf_import_ajax() {

		if ( ! empty( $_POST['import_data'] ) && ! empty( $_POST['unique'] ) && ! empty( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], 'spf_backup_nonce' ) ) {

			// $import_data = unserialize( stripslashes( trim( $_POST['import_data'] ) ) );
			$import_data = json_decode( wp_unslash( trim( $_POST['import_data'] ) ), true );

			if ( is_array( $import_data ) ) {

				update_option( wp_unslash( $_POST['unique'] ), wp_unslash( $import_data ) );
				wp_send_json_success( array( 'success' => true ) );

			}
		}

		wp_send_json_error(
			array(
				'success' => false,
				'error'   => esc_html__( 'Error while saving.', 'wp-carousel-pro' ),
				'debug'   => $_REQUEST,
			)
		);

	}
	add_action( 'wp_ajax_spf-import', 'spf_import_ajax' );
}

/**
 *
 * Reset Ajax
 *
 * @since 1.0.0
 * @version 1.0.0
 */
if ( ! function_exists( 'spf_reset_ajax' ) ) {
	function spf_reset_ajax() {

		if ( ! empty( $_POST['unique'] ) && ! empty( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], 'spf_backup_nonce' ) ) {
			delete_option( wp_unslash( $_POST['unique'] ) );
			wp_send_json_success( array( 'success' => true ) );
		}

		wp_send_json_error(
			array(
				'success' => false,
				'error'   => esc_html__( 'Error while saving.', 'wp-carousel-pro' ),
				'debug'   => $_REQUEST,
			)
		);
	}
	add_action( 'wp_ajax_spf-reset', 'spf_reset_ajax' );
}

/**
 *
 * Set icons for wp dialog
 *
 * @since 1.0.0
 * @version 1.0.0
 */
if ( ! function_exists( 'spf_set_icons' ) ) {
	function spf_set_icons() {
		global $post_type;
		if ( 'sp_wp_carousel' === $post_type ) {
			?>
			<div id="spf-modal-icon" class="spf-modal spf-modal-icon">
				<div class="spf-modal-table">
				<div class="spf-modal-table-cell">
					<div class="spf-modal-overlay"></div>
					<div class="spf-modal-inner">
					<div class="spf-modal-title">
						<?php esc_html_e( 'Add Icon', 'wp-carousel-pro' ); ?>
						<div class="spf-modal-close spf-icon-close"></div>
					</div>
					<div class="spf-modal-header spf-text-center">
						<input type="text" placeholder="<?php esc_html_e( 'Search a Icon...', 'wp-carousel-pro' ); ?>" class="spf-icon-search" />
					</div>
					<div class="spf-modal-content">
						<div class="spf-modal-loading"><div class="spf-loading"></div></div>
						<div class="spf-modal-load"></div>
					</div>
					</div>
				</div>
				</div>
			</div>
			<?php
		}

	}
	add_action( 'admin_footer', 'spf_set_icons' );
	add_action( 'customize_controls_print_footer_scripts', 'spf_set_icons' );
}
