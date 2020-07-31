<?php
/**
* LoginPresss_Custom_Password
*
* Description: Enable Custom Password for Register User.
*
* @package LoginPress
* @since 1.0.22
*/

if ( ! class_exists( 'LoginPresss_Custom_Password' ) ) :
  /**
  * LoginPress Custom Passwords class
  *
  * @since 1.0.22
  * @version 1.4.0
  */
  class LoginPresss_Custom_Password {

    /* * * * * * * * * *
    * Class constructor
    * * * * * * * * * */
    public function __construct() {

      $this->_hooks();
    }

    public function _hooks() {

      add_action( 'register_form',                  array( $this, 'loginpress_reg_password_fields' ) );
      add_filter( 'registration_errors',            array( $this, 'loginpress_reg_pass_errors' ), 10, 3 );
      add_filter( 'random_password',                array( $this, 'loginpress_set_password' ) );
      add_action( 'register_new_user',              array( $this, 'update_default_password_nag' ) );
      add_filter( 'wp_new_user_notification_email', array( $this, 'loginpress_new_user_email_notification' ) );

      if ( ! function_exists('wp_new_user_notification') ) :
        /**
         * Email login credentials to a newly-registered user.
         *
         * A new user registration notification is also sent to admin email.
        */
        function wp_new_user_notification( $user_id, $notify = '' ) {

          $prevent_from = apply_filters( 'loginpress_prevent_new_user_notification_email', array() );

          if ( ! in_array( 'prevent_admin', $prevent_from ) ) {
              LoginPresss_Custom_Password::loginpress_new_user_reg_notification_to_admin( $user_id );
          }

          if ( ! in_array( 'prevent_user', $prevent_from ) ) {
              LoginPresss_Custom_Password::loginpress_new_user_reg_notification_to_user( $user_id );
          }
        }
      endif;

    }

    /**
     * Custom Password Fields on Registration Form.
     *
     * @since   1.0.22
     * @access  public
     * @return  string html.
     */
    public function loginpress_reg_password_fields() {
      ?>
      <p class="loginpress-reg-pass-wrap">
        <label for="loginpress-reg-pass"><?php _e( 'Password', 'loginpress' ); ?></label>
        <input autocomplete="off" name="loginpress-reg-pass" id="loginpress-reg-pass" class="input" size="20" value="" type="password" />
      </p>
      <p class="loginpress-reg-pass-2-wrap">
        <label for="loginpress-reg-pass-2"><?php _e( 'Confirm Password', 'loginpress' ); ?></label>
        <input autocomplete="off" name="loginpress-reg-pass-2" id="loginpress-reg-pass-2" class="input" size="20" value="" type="password" />
      </p>
      <?php
    }

    /**
    * Handles password field errors for registration form.
    *
    * @since 1.0.22
    * @access public
    *
    * @param Object $errors WP_Error
    * @param Object $sanitized_user_login user login.
    * @param Object $user_email user email.
    * @return WP_Error object.
    */
    public function loginpress_reg_pass_errors( $errors, $sanitized_user_login, $user_email ) {

      // Ensure passwords aren't empty.
      if ( empty( $_POST['loginpress-reg-pass'] ) || empty( $_POST['loginpress-reg-pass-2'] ) ) {
        $errors->add( 'empty_password', __( '<strong>ERROR</strong>: Please enter your password twice.', 'loginpress' ) );

      // Ensure passwords are matched.
      } elseif ( $_POST['loginpress-reg-pass'] != $_POST['loginpress-reg-pass-2'] ) {
        $errors->add( 'password_mismatch', __( '<strong>ERROR</strong>: Please enter the same password in the end password fields.', 'loginpress' ) );

      // Password Set? assign password to a user_pass
      } else {
        $_POST['user_pass'] = $_POST['loginpress-reg-pass'];
      }

      return $errors;
    }

    /**
    * Let's set the user password.
    *
    * @since 1.0.22
    * @access public
    * @param string $password Auto-generated password passed in from filter.
    * @return string Password Choose by User.
    */
    public function loginpress_set_password( $password ) {

      // Make sure password field isn't empty.
      if ( ! empty( $_POST['user_pass'] ) ) {
        $password = $_POST['user_pass'];
      }

      return $password;
    }

    /**
    * Sets the value of default password nag.
    *
    * @since 1.0.22
    * @access public
    * @param int $user_id.
    */
    public function update_default_password_nag( $user_id ) {

      // False => User not using WordPress default password.
      update_user_meta( $user_id, 'default_password_nag', false );
    }

    /**
     * Filter the new user email notification.
     *
     * @since 1.4.0
     *
     * @param array $email The new user email notification parameters.
     * @return array The new user email notification parameters.
     */
    function loginpress_new_user_email_notification( $email ) {

    		$email['message'] .= "\r\n" . __( 'If you have already set your own password, you may disregard this email and use the password you have already set.', 'loginpress' );

    	return $email;
    }

    public static function loginpress_new_user_reg_notification_to_admin( $user_id ) {

    	global $wpdb;
    	$user = get_userdata( $user_id );

    	// The blogname option is escaped with esc_html on the way into the database in sanitize_option
    	// we want to reverse this for the plain text arena of emails.
    	$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

    	$message  = sprintf(__('New user registration on your site %s:'), $blogname) . "\r\n\r\n";
    	$message .= sprintf(__('Username: %s'), $user->user_login) . "\r\n\r\n";
    	$message .= sprintf(__('E-mail: %s'), $user->user_email) . "\r\n";

      wp_mail( get_option( 'admin_email' ), sprintf( __('[%s] New User Registration' ), $blogname ), $message );
    }


    public static function loginpress_new_user_reg_notification_to_user( $user_id ) {

    	global $wpdb;
    	$user = get_userdata( $user_id );

      // The blogname option is escaped with esc_html on the way into the database in sanitize_option
    	// we want to reverse this for the plain text arena of emails.
    	$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

    	// Generate something random for a password reset key.
    	$key = wp_generate_password( 20, false );

    	/** This action is documented in wp-login.php */
    	do_action( 'retrieve_password_key', $user->user_login, $key );

    	// Now insert the key, hashed, into the DB.
    	if ( empty( $wp_hasher ) ) {
    		require_once ABSPATH . WPINC . '/class-phpass.php';
    		$wp_hasher = new PasswordHash( 8, true );
    	}
    	$hashed = time() . ':' . $wp_hasher->HashPassword( $key );
    	$wpdb->update( $wpdb->users, array( 'user_activation_key' => $hashed ), array( 'user_login' => $user->user_login ) );

    	$message = sprintf( __('Username: %s'), $user->user_login ) . "\r\n\r\n";
    	$message .= __( 'To set your password, visit the following address:' ) . "\r\n\r\n";
    	$message .= '<' . network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user->user_login), 'login') . ">\r\n\r\n";

    	$message .= wp_login_url() . "\r\n\r\n";

      $message .= __( 'If you have already set your own password, you may disregard this email and use the password you have already set.', 'loginpress' ) . "\r\n";

    	wp_mail( $user->user_email, sprintf(__('[%s] Login Details'), $blogname), $message );
    }

  } // End Of Class.

endif;
