<?php 

/**
 * Plugin Name: Wp Clone Any Post Type
 * Plugin URI: https://wordpress.org/plugins/wp-clone-any-post-type/
 * Description: This plugin creates to make an exact number of copy of the selected post, page and custom post types. It has features to easy to configure for enabling/disabling specific post/page/custom post type.
 * Version: 1.0
 * Author: Galaxy Weblinks
 * Author URI: https://www.galaxyweblinks.com 
 * Text Domain: wp-clone-any-post-type
 * License: GPL2
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( !class_exists( 'GwlCloneAnyPostType' ) ) :
 
class GwlCloneAnyPostType {
	
	public function __construct() {
		register_activation_hook(__FILE__, array( $this, 'wcapt_clone_any_post_activate'));
		add_action( 'admin_notices', array( &$this, 'wcapt_anypst_clone_admin_notice__success' ));
        add_action( 'admin_menu', array( &$this, 'wcapt_clonepg_setting_opt'));
        add_action( 'admin_init', array( &$this, 'wcapt_reg_clone_pgplugin_settings') );
        add_action( 'admin_footer-edit.php', array( &$this, 'wp_clone_any_post_bulk_selection' ));
        add_action( 'admin_notices', array( &$this, 'wcapt_any_post_admin_notice' ));
        add_filter( 'post_row_actions', array( &$this, 'wcapt_clone_actions_url' ), 10, 2);
        add_filter( 'page_row_actions', array( &$this, 'wcapt_clone_actions_url' ), 10, 2);
        add_action( 'wp_loaded', array( &$this, 'wcapt_cloningpg_redirect_url_callback_function' ));            
        add_action( 'load-edit.php', array( &$this, 'wcapt_bulk_clonepg_callback_fun' ));
        add_action( 'admin_enqueue_scripts', array( &$this, 'wp_clone_any_post_enqueue_scripts_and_styles' ));
        add_action( 'wp_ajax_wcapt_wapty', array( $this, 'wcapt_any_post_clone_create' ));
        add_action( 'wp_ajax_nopriv_wcapt_wapty', array( $this, 'wcapt_any_post_clone_create') );
	}

	/* ADD TOKEN WHEN PLUGIN ACTIVATE */
	/*****************************/

	function wcapt_clone_any_post_activate(){
		update_option('wp_any_posts_clone_notice','enabled');
	}

	/* DISPLAY MESSAGE WHEN PLUGIN ACTIVATE AND REFER TO SETTING PAGE */
	function wcapt_anypst_clone_admin_notice__success() {
		if(get_option('wp_any_posts_clone_notice') == 'enabled'){
	    	?>
		
	    <div class="notice notice-success is-dismissible">
	    	<p><?php _e( 'Thank you for choosing <strong>WP clone any post type.</strong> <br/>If you are enjoy using it, kindly leave us a review on <a class="button button-primary" href="https://wordpress.org/plugins/wp-clone-any-post-type/#reviews" target="_blank">wordpress.org</a>', 'wp-clone-any-post-type' ); ?></p>
	        <p><?php _e( 'To view clone setting please ', 'wp-clone-any-post-type' ); ?><a class="button button-primary" href="<?php echo admin_url('admin.php?page=wcapt_clone_settings'); ?>"><?php _e( 'click here', 'wp-clone-any-post-type' ); ?></a></p>
	    </div>
	    <?php 
		delete_option('wp_any_posts_clone_notice');
		}
	}	
		
	/*
	*	Register and display our settings page
	*/
    function wcapt_clonepg_setting_opt(){
        add_menu_page( 'clone settings',
         __( 'Clone settings', 'wp-clone-any-post-type' ),
          'manage_options',
          'wcapt_clone_settings', 
          array( &$this, 'wcapt_clone_set_call_opt'),'dashicons-category' );
	}

    /**
	* Register Setting options
	*/
    function wcapt_reg_clone_pgplugin_settings(){
        register_setting( 'gwl-clone-posts-page-options-group', 'wcapt_clone_post_types' );
    }
        
    /*Include our page cloner settings page*/

	function wcapt_clone_set_call_opt(){      
        include_once( plugin_dir_path( __FILE__ ) . 'includes/wp-clone-any-post-type-settings.php' );	
	}

	//Add the custom Bulk Action to the select menus

	function wp_clone_any_post_enqueue_scripts_and_styles() {
		wp_enqueue_style( 'wp-clone-any-pty', plugin_dir_url( __FILE__ ) . 'includes/css/wp-clone-any-post-type-style.css' );
		wp_enqueue_script( 'wp-clone-any-ptype-js', plugins_url( 'includes/js/wp-clone-any-post-type-main.js', __FILE__ ),array( 'jquery' ) );
		wp_localize_script( 'wp-clone-any-ptype-js', 'wpclone_ajax_object',
	        array( 
	            'ajaxurl' => admin_url( 'admin-ajax.php' ),
	        )
	    );
	}

	function wp_clone_any_post_bulk_selection() {
		global $post_type;		
		$get_opt_val = get_option('wcapt_clone_post_types');
            if (in_array($post_type, $get_opt_val))
            { ?>
                <script type="text/javascript">
					jQuery(function () {
						jQuery('<option>').val('clone').text('<?php _e('Clone')?>').appendTo("select[name='action']");
						jQuery('<option>').val('clone').text('<?php _e('Clone')?>').appendTo("select[name='action2']");
					});
				</script>                 
        <?php }				
	}
	

	/* Bulk action for clone page, post callback */
        
	function wcapt_bulk_clonepg_callback_fun() {
		global $typenow;
		$post_type = $typenow;

		// check and get action value
		$wp_list_table = _get_list_table('WP_Posts_List_Table'); 
		$action = $wp_list_table->current_action();
		$allowed_actions = array("clone");
		if ( ! in_array( $action, $allowed_actions )) {
			return;
		}
		
		// Tests the current request carries a valid nonce
		check_admin_referer('bulk-posts');
		
		if ( isset( $_REQUEST['post'] )) {
			$post_ids = array_map( 'intval', $_REQUEST['post'] );
		}
		
		if ( empty( $post_ids )) {
			return;
		}
		
		$sendback = remove_query_arg( array( 'cloned', 'untrashed', 'deleted', 'ids' ), wp_get_referer() );
		if ( ! $sendback ) {
			$sendback = admin_url( "edit.php?post_type=$post_type" );
		}
		
		$pagenum = $wp_list_table->get_pagenum();
		$sendback = add_query_arg( 'paged', $pagenum, $sendback );
		
		switch ( $action ) {
			case 'clone':
				
				$cloned = 0;
				foreach ( $post_ids as $post_id ) {

					if ( !current_user_can('edit_post', $post_id) ) {
						wp_die( __('You are not allowed to create clone of this post.') );
					}
					
					if ( ! $this->wcapt_any_post_clone_create( $post_id )) {
						wp_die( __('Error cloning post.') );
					}
	
					$cloned++;
				}
				
				$sendback = add_query_arg( array( 'cloned' => $cloned, 'ids' => join(',', $post_ids) ), $sendback );
				break;
			
			default: 
				return;
		}
		
		$sendback = remove_query_arg( array( 'action', 'action2', 'tags_input', 'post_author', 
			'comment_status', 'ping_status', '_status',  'post', 'bulk_edit', 'post_view'), $sendback );
		
		wp_redirect($sendback);
		exit();
	}
	
	
	/**
	 * Display an admin notice when clone page, post or post types.
	 */
	function wcapt_any_post_admin_notice() {
		global $pagenow;
		
		if ($pagenow == 'edit.php' && ! isset($_GET['trashed'] )) {
			$cloned = 0;
			if ( isset( $_REQUEST['cloned'] ) && (int) $_REQUEST['cloned'] ) {
				$cloned = (int) $_REQUEST['cloned'];
			} elseif ( isset($_GET['cloned']) && (int) $_GET['cloned'] ) {
				$cloned = (int) $_GET['cloned'];
			}
			if ($cloned) {
				$message = sprintf( _n( 'Cloned Successfully.', '%s posts cloned.', $cloned ), number_format_i18n( $cloned ) );
				echo "<div class=\"updated\"><p>{$message}</p></div>";
			}
		}
	}

	function wcapt_clone_actions_url( $actions, $post ) {
		global $post_type;
		/*echo "<pre>";
		print_r($actions);
		echo "</pre>";*/

		$url = remove_query_arg( array( 'cloned', 'untrashed', 'deleted', 'ids' ), "" );
		if ( ! $url ) {
			$url = admin_url( "?post_type=$post_type" );
		}
		$url = remove_query_arg( array( 'action', 'action2', 'tags_input', 'post_author', 
			'comment_status', 'ping_status', '_status',  'post', 'bulk_edit', 'post_view'), $url );
		$url = add_query_arg( array( 'action' => 'clone-single', 'post' => $post->ID, 'redirect' => $_SERVER['REQUEST_URI'] ), $url );                
            $get_opt_val = get_option('wcapt_clone_post_types');
            if (in_array($post_type, $get_opt_val))
            {
                $actions['clone'] =  '<a id="Btwcapt" data-wp_clone_pty_id="'.$post->ID.'" href=\''.$url.'\'>'.__('Clone').'</a>'.'<input style="width:60px !important;" type="number" value="1" min="1" id="wp_clone_any_item_no'.$post->ID.'" name="wp_clone_any_item_no">';
            }                
		return $actions;
	}

	function wcapt_cloningpg_redirect_url_callback_function() {
		global $post_type;

		if ( ! isset($_GET['action']) || $_GET['action'] !== "clone-single") {
			return;
		}

		$post_id = (int) $_GET['post'];

		if ( !current_user_can('edit_post', $post_id )) {
			wp_die( __('You are not allowed to clone this post.') );
		}

		if ( !$this->wcapt_any_post_clone_create( $post_id )) {
			wp_die( __('Error cloning post.') );
		}

		$sendback = remove_query_arg( array( 'cloned', 'untrashed', 'deleted', 'ids' ), $_GET['redirect'] );
		if ( ! $sendback ) {
			$sendback = admin_url( "edit.php?post_type=$post_type" );
		}
		
		$sendback = add_query_arg( array( 'cloned' => 1 ), $sendback );
		$sendback = remove_query_arg( array( 'action', 'action2', 'tags_input', 'post_author', 
			'comment_status', 'ping_status', '_status',  'post', 'bulk_edit', 'post_view'), $sendback );
		wp_redirect($sendback);
		exit();
	}

	function wcapt_any_post_clone_create($id) {
		//$id = $_REQUEST
		if($_REQUEST['postid'] && $_REQUEST['copies']){			
			$pst = get_post( $_REQUEST['postid']);					
			$copy_required = absint( $_REQUEST['copies'] ) ? $_REQUEST['copies']: 1 ;			

			for ( $J = 1; $J <= $copy_required; $J++ ){
				if($copy_required > 1){
					$incr=$J;
				}
				$clonedpost = array(
					'post_name'				=> $pst->post_name.__("-".$incr."copy-").mt_rand(9,999),
					'post_type'				=> $pst->post_type,
					'ping_status'			=> $pst->ping_status,
					'post_parent'			=> $pst->post_parent,
					'menu_order'			=> $pst->menu_order,
					'post_password'			=> $pst->post_password,
					'post_excerpt'			=> $pst->post_excerpt,
					'comment_status'		=> $pst->comment_status,
					'post_status'    		=> 'draft',
					'post_title'			=> $pst->post_title . __("- (".$incr."copy)"),
					'post_content'			=> $pst->post_content,
					'post_author'			=> $pst->post_author,
					'to_ping'				=> $pst->to_ping,
					'pinged'				=> $pst->pinged,
					'post_content_filtered' => $pst->post_content_filtered,
					'post_category'			=> $pst->post_category,
					'tags_input'			=> $pst->tags_input,
					'tax_input'				=> $pst->tax_input,
					'page_template'			=> $pst->page_template		
				);
			$postid = wp_insert_post($clonedpost);
			$pst_format = get_post_format( $id );
			set_post_format($postid, $pst_format);	

			}
			wp_redirect( $_SERVER['HTTP_REFERER'] );
			die();
		}
		else{
			$pst = get_post( $id );
			if ($pst == null) return false;

			$clonedpost = array(
				'post_name'				=> $pst->post_name.__("-copy-").mt_rand(9,999),
				'post_type'				=> $pst->post_type,
				'ping_status'			=> $pst->ping_status,
				'post_parent'			=> $pst->post_parent,
				'menu_order'			=> $pst->menu_order,
				'post_password'			=> $pst->post_password,
				'post_excerpt'			=> $pst->post_excerpt,
				'comment_status'		=> $pst->comment_status,
				'post_status'    		=> 'draft',
				'post_title'			=> $pst->post_title . __('- (copy)'),
				'post_content'			=> $pst->post_content,
				'post_author'			=> $pst->post_author,
				'to_ping'				=> $pst->to_ping,
				'pinged'				=> $pst->pinged,
				'post_content_filtered' => $pst->post_content_filtered,
				'post_category'			=> $pst->post_category,
				'tags_input'			=> $pst->tags_input,
				'tax_input'				=> $pst->tax_input,
				'page_template'			=> $pst->page_template
			);
			$postid = wp_insert_post($clonedpost);
			$pst_format = get_post_format( $id );
			set_post_format($postid, $pst_format);

			return true;
		}   
	}	
}

new GwlCloneAnyPostType();

endif;
