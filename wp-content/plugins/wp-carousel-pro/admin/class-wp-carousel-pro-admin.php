<?php
/**
 * The admin-specific of the plugin.
 *
 * @link https://shapedplugin.com
 * @since 3.0.0
 *
 * @package WordPress_Carousel_Pro
 * @subpackage WordPress_Carousel_Pro/admin
 */

/**
 * The class for the admin-specific functionality of the plugin.
 */
class WP_Carousel_Pro_Admin {
	/**
	 * Script and style suffix
	 *
	 * @since 3.0.0
	 * @access protected
	 * @var string
	 */
	protected $suffix;

	/**
	 * The ID of the plugin.
	 *
	 * @since 3.0.0
	 * @access protected
	 * @var string      $plugin_name The ID of this plugin
	 */
	protected $plugin_name;

	/**
	 * The version of the plugin
	 *
	 * @since 3.0.0
	 * @access protected
	 * @var string      $version The current version fo the plugin.
	 */
	protected $version;

	/**
	 * The single instance of the class.
	 *
	 * @var self
	 * @since 3.0.0
	 */
	private static $instance = null;

	/**
	 * Allows for accessing single instance of class. Class should only be constructed once per call.
	 *
	 * @since 3.0.0
	 * @static
	 * @return self Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Initialize the class sets its properties.
	 *
	 * @since 3.0.0
	 * @param string $plugin_name The name of the plugin.
	 * @param string $version The version of the plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->suffix      = defined( 'WP_DEBUG' ) && WP_DEBUG ? '' : '.min';
		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		add_filter( 'attachment_fields_to_edit', array( $this, 'add_attachment_linking_field' ), 10, 2 );
		add_filter( 'attachment_fields_to_save', array( $this, 'update_attachment_linking_field' ), 10, 2 );
	}

	/**
	 * Register the stylesheets for the admin area of the plugin.
	 *
	 * @since  3.0.0
	 * @return void
	 */
	public function enqueue_admin_styles() {			
		wp_enqueue_style( $this->plugin_name . '-admin', WPCAROUSEL_URL . 'admin/css/wp-carousel-pro-admin' . $this->suffix . '.css', array(), $this->version, 'all' );
		$current_screen        = get_current_screen();
		$the_current_post_type = $current_screen->post_type;
		if ( 'sp_wp_carousel' === $the_current_post_type ) {
			wp_enqueue_style( 'font-awesome', WPCAROUSEL_URL . 'public/css/font-awesome.min.css', array(), $this->version, 'all' );
		}
		// Scripts.
		wp_enqueue_script( $this->plugin_name . 'admin', WPCAROUSEL_URL . 'admin/js/wp-carousel-pro-admin' . $this->suffix . '.js', array( 'jquery' ), $this->version, true );
	}

	/**
	 * Add custom field for Images.
	 *
	 * @param array   $fields An array of attachment form fields.
	 * @param WP_Post $post        The WP_Post attachment object.
	 */
	public function add_attachment_linking_field( $fields, $post ) {
		if ( 'image' === substr( $post->post_mime_type, 0, 5 ) ) {

			$meta = wp_get_attachment_metadata( $post->ID );

			$fields['meta_wpcplinking'] = array(
				'label'      => __( 'Image Link', 'wp-carousel-pro' ),
				'input'      => 'text',
				'value'      => isset( $meta['image_meta']['wpcplinking'] ) ? $meta['image_meta']['wpcplinking'] : '',
				'helps'      => __( 'Link the image to the URL. Only for WordPress Carousel Pro', 'wp-carousel-pro' ),
				'error_text' => __( 'Error WP Carousel linking meta.', 'wp-carousel-pro' ),
			);
		}
		return $fields;
	}

	/**
	 * Filters the attachment fields to be saved.
	 *
	 * @param array $post       An array of post data.
	 * @param array $attachment An array of attachment metadata.
	 */
	public function update_attachment_linking_field( $post, $attachment ) {
		if ( isset( $attachment['meta_wpcplinking'] ) ) {
			$linking_url = $attachment['meta_wpcplinking'];
			$meta        = wp_get_attachment_metadata( $post['ID'] );
			if ( $linking_url !== $meta['image_meta']['wpcplinking'] ) {
				$meta['image_meta']['wpcplinking'] = $linking_url;
				wp_update_attachment_metadata( $post['ID'], $meta );
			}
		}
		return $post;
	}

	/**
	 * Change Carousel updated messages.
	 *
	 * @param string $messages The Update messages.
	 * @return statement
	 */
	public function wpcp_carousel_updated_messages( $messages ) {
		global $post, $post_ID;
		$messages['sp_wp_carousel'] = array(
			0  => '', // Unused. Messages start at index 1.
			1  => sprintf( __( 'Carousel updated.', 'wp-carousel-pro' ) ),
			2  => '',
			3  => '',
			4  => __( 'Carousel updated.', 'wp-carousel-pro' ),
			5  => isset( $_GET['revision'] ) ? sprintf( __( 'Carousel restored to revision from %s', 'wp-carousel-pro' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6  => sprintf( __( 'Carousel published.', 'wp-carousel-pro' ) ),
			7  => __( 'Carousel saved.', 'wp-carousel-pro' ),
			8  => sprintf( __( 'Carousel submitted.', 'wp-carousel-pro' ) ),
			9  => sprintf( __( 'Carousel scheduled for: <strong>%1$s</strong>.', 'wp-carousel-pro' ), date_i18n( __( 'M j, Y @ G:i', 'wp-carousel-pro' ), strtotime( $post->post_date ) ) ),
			10 => sprintf( __( 'Carousel draft updated.', 'wp-carousel-pro' ) ),
		);
		return $messages;
	}

	/**
	 * Add carousel admin columns.
	 *
	 * @return statement
	 */
	public function filter_carousel_admin_column() {
		$admin_columns['cb']            = '<input type="checkbox" />';
		$admin_columns['title']         = __( 'Carousel Title', 'wp-carousel-pro' );
		$admin_columns['shortcode']     = __( 'Shortcode', 'wp-carousel-pro' );
		$admin_columns['carousel_type'] = __( 'Carousel Type', 'wp-carousel-pro' );
		$admin_columns['date']          = __( 'Date', 'wp-carousel-pro' );

		return $admin_columns;
	}

	/**
	 * Display admin columns for the carousels.
	 *
	 * @param mix    $column The columns.
	 * @param string $post_id The post ID.
	 * @return void
	 */
	public function display_carousel_admin_fields( $column, $post_id ) {
		$upload_data     = get_post_meta( $post_id, 'sp_wpcp_upload_options', true );
		$carousels_types = isset( $upload_data['wpcp_carousel_type'] ) ? $upload_data['wpcp_carousel_type'] : '';
		switch ( $column ) {
			case 'shortcode':
				$column_field = '<input style="width: 270px; padding: 6px;" type="text" onClick="this.select();" readonly="readonly" value="[sp_wpcarousel id=&quot;' . $post_id . '&quot;]"/>';
				echo $column_field;
				break;
			case 'carousel_type':
				echo ucwords( str_replace( '-', ' ', $carousels_types ) );

		} // end switch.
	}

	/**
	 * Duplicate the carousel
	 *
	 * @return void
	 */
	public function sp_wpcp_duplicate_carousel() {
		global $wpdb;
		if ( ! ( isset( $_GET['post'] ) || isset( $_POST['post'] ) || ( isset( $_REQUEST['action'] ) && 'sp_wpcp_duplicate_carousel' === $_REQUEST['action'] ) ) ) {
			wp_die( esc_html__( 'No shortcode to duplicate has been supplied!', 'wp-carousel-pro' ) );
		}

		/*
		 * Nonce verification
		 */
		if ( ! isset( $_GET['sp_wpcp_duplicate_nonce'] ) || ! wp_verify_nonce( $_GET['sp_wpcp_duplicate_nonce'], basename( __FILE__ ) ) ) {
			return;
		}

		/*
		 * Get the original shortcode id
		 */
		$post_id = ( isset( $_GET['post'] ) ? absint( $_GET['post'] ) : absint( $_POST['post'] ) );

		/*
		 * and all the original shortcode data then
		 */
		$post = get_post( $post_id );

		$current_user    = wp_get_current_user();
		$new_post_author = $current_user->ID;

		/*
		 * if shortcode data exists, create the shortcode duplicate
		 */
		if ( isset( $post ) && null != $post ) {

			// New shortcode data array.
			$args = array(
				'comment_status' => $post->comment_status,
				'ping_status'    => $post->ping_status,
				'post_author'    => $new_post_author,
				'post_content'   => $post->post_content,
				'post_excerpt'   => $post->post_excerpt,
				'post_name'      => $post->post_name,
				'post_parent'    => $post->post_parent,
				'post_password'  => $post->post_password,
				'post_status'    => 'draft',
				'post_title'     => $post->post_title,
				'post_type'      => $post->post_type,
				'to_ping'        => $post->to_ping,
				'menu_order'     => $post->menu_order,
			);

			/*
			 * insert the shortcode by wp_insert_post() function
			 */
			$new_post_id = wp_insert_post( $args );

			/*
			 * get all current post terms ad set them to the new post draft
			 */
			$taxonomies = get_object_taxonomies( $post->post_type ); // returns array of taxonomy names for post type, ex array("category", "post_tag");
			foreach ( $taxonomies as $taxonomy ) {
				$post_terms = wp_get_object_terms( $post_id, $taxonomy, array( 'fields' => 'slugs' ) );
				wp_set_object_terms( $new_post_id, $post_terms, $taxonomy, false );
			}

			/*
			 * duplicate all post meta just in two SQL queries
			 */
			$post_meta_infos = $wpdb->get_results( "SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$post_id" );
			if ( count( $post_meta_infos ) != 0 ) {
				$sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
				foreach ( $post_meta_infos as $meta_info ) {
					$meta_key = $meta_info->meta_key;
					if ( $meta_key == '_wp_old_slug' ) {
						continue;
					}
					$meta_value      = addslashes( $meta_info->meta_value );
					$sql_query_sel[] = "SELECT $new_post_id, '$meta_key', '$meta_value'";
				}
				$sql_query .= implode( ' UNION ALL ', $sql_query_sel );
				$wpdb->query( $sql_query );
			}

			/*
			 * finally, redirect to the edit post screen for the new draft
			 */
			wp_redirect( admin_url( 'edit.php?post_type=' . $post->post_type ) );

			exit;
		} else {
			wp_die( esc_html__( 'Carousel duplication failed, could not find original carousel: ', 'wp-carousel-pro' ) . $post_id );
		}
	}

	/**
	 * Add the duplicate link to action list for post_row_actions
	 *
	 * @param mix    $actions The actions of the link.
	 * @param string $post The post to provide its ID.
	 * @return statement
	 */
	public function sp_wpcp_duplicate_carousel_link( $actions, $post ) {
		if ( current_user_can( 'edit_posts' ) && 'sp_wp_carousel' === $post->post_type ) {
			$actions['duplicate'] = '<a href="' . wp_nonce_url( 'admin.php?action=sp_wpcp_duplicate_carousel&post=' . $post->ID, basename( __FILE__ ), 'sp_wpcp_duplicate_nonce' ) . '" rel="permalink">' . __( 'Duplicate', 'wp-carousel-pro' ) . '</a>';
		}
		return $actions;
	}

	/**
	 * Bottom review notice.
	 *
	 * @param string $text The review notice.
	 * @return string
	 */
	public function sp_wpcp_review_text( $text ) {
		$screen = get_current_screen();
		if ( 'sp_wp_carousel' === get_post_type() || 'sp_wp_carousel_page_wpcp_settings' === $screen->id || 'sp_wp_carousel_page_wpcp_help' === $screen->id ) {
			$url  = 'https://shapedplugin.com/plugin/wordpress-carousel-pro/#reviews';
			$text = sprintf( __( 'If you like <strong>WordPress Carousel Pro</strong>, please leave us a <a href="%s" target="_blank">&#9733;&#9733;&#9733;&#9733;&#9733;</a> rating. Your Review is very important to us as it helps us to grow more. ', 'wp-carousel-pro' ), $url );
		}

		return $text;
	}
}
