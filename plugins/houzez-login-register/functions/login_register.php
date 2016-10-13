<?php
/**
 * Created by PhpStorm.
 * User: waqasriaz
 * Date: 16/01/16
 * Time: 6:11 PM
 */

/*-----------------------------------------------------------------------------------*/
// Login
/*-----------------------------------------------------------------------------------*/
add_action( 'wp_ajax_houzez_login', 'houzez_login' );
add_action( 'wp_ajax_nopriv_houzez_login', 'houzez_login' );

if( !function_exists('houzez_login') ) {
    function houzez_login() {

        check_ajax_referer( 'houzez_login_nonce', 'houzez_login_security' );
        $allowed_html = array();

        $allowed_html_array = array('strong' => array());
        $username = wp_kses( $_POST['username'], $allowed_html );
        $pass = wp_kses( $_POST['password'], $allowed_html );

        if( isset( $_POST['remember'] ) ) {
            $remember = wp_kses( $_POST['remember'], $allowed_html );
        } else {
            $remember = '';
        }

        if( empty( $username ) ) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('The username field is empty.', 'houzez') ) );
            wp_die();
        }
        if( empty( $pass ) ) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('The password field is empty.', 'houzez') ) );
            wp_die();
        }
        if( !username_exists( $username ) ) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('Invalid username', 'houzez') ) );
            wp_die();
        }

        wp_clear_auth_cookie();

        $remember = ($remember == 'on') ? true : false;

        $creds = array();
        $creds['user_login'] = $username;
        $creds['user_password'] = $pass;
        $creds['remember'] = $remember;
        $user = wp_signon( $creds, false );

        if ( is_wp_error( $user ) ) {
            echo json_encode( array(
                'success' => false,
                'msg' => sprintf( wp_kses(__('The password you entered for the username <strong>%s</strong> is incorrect.', 'houzez'), $allowed_html_array), $username )
                ) );

            wp_die();
        } else {

            wp_set_current_user($user->ID);
            do_action('set_current_user');
            global $current_user;
            $current_user = wp_get_current_user();

            echo json_encode( array( 'success' => true, 'msg' => esc_html__('Login successful, redirecting...', 'houzez') ) );

        }
        wp_die();
    }
}


/*-----------------------------------------------------------------------------------*/
// Register
/*-----------------------------------------------------------------------------------*/
add_action( 'wp_ajax_nopriv_houzez_register', 'houzez_register' );
add_action( 'wp_ajax_houzez_register', 'houzez_register' );

if( !function_exists('houzez_register') ) {
    function houzez_register() {

        check_ajax_referer('houzez_register_nonce', 'houzez_register_security');

        $allowed_html = array();

        $usermane          = trim( sanitize_text_field( wp_kses( $_POST['username'], $allowed_html ) ));
        $email             = trim( sanitize_text_field( wp_kses( $_POST['useremail'], $allowed_html ) ));
        $term_condition    = wp_kses( $_POST['term_condition'], $allowed_html );
        $enable_password = houzez_option('enable_password');

        $user_role = get_option( 'default_role' );
        $user_role = isset( $_POST['role'] ) ? sanitize_text_field( wp_kses( $_POST['role'], $allowed_html ) ) : $user_role;

        $term_condition = ( $term_condition == 'on') ? true : false;

        if( !$term_condition ) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('You need to agree with terms & conditions.', 'houzez') ) );
            wp_die();
        }

        if( empty( $usermane ) ) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__(' The username field is empty.', 'houzez') ) );
            wp_die();
        }
        if( strlen( $usermane ) < 3 ) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__(' Minimum 3 characters required', 'houzez') ) );
            wp_die();
        }
        if (preg_match("/^[0-9A-Za-z_]+$/", $usermane) == 0) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('Invalid username (do not use special characters or spaces)!', 'houzez') ) );
            wp_die();
        }
        if( empty( $email ) ) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('The email field is empty.', 'houzez') ) );
            wp_die();
        }
        if( username_exists( $usermane ) ) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('This username is already registered.', 'houzez') ) );
            wp_die();
        }
        if( email_exists( $email ) ) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('This email address is already registered.', 'houzez') ) );
            wp_die();
        }

        if( !is_email( $email ) ) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('Invalid email address.', 'houzez') ) );
            wp_die();
        }

        if( $enable_password == 'yes' ){
            $user_pass         = trim( sanitize_text_field(wp_kses( $_POST['register_pass'] ,$allowed_html) ) );
            $user_pass_retype  = trim( sanitize_text_field(wp_kses( $_POST['register_pass_retype'] ,$allowed_html) ) );

            if ($user_pass == '' || $user_pass_retype == '' ) {
                echo json_encode( array( 'success' => false, 'msg' => esc_html__('One of the password field is empty!', 'houzez') ) );
                wp_die();
            }

            if ($user_pass !== $user_pass_retype ){
                echo json_encode( array( 'success' => false, 'msg' => esc_html__('Passwords do not match', 'houzez') ) );
                wp_die();
            }
        }

        if($enable_password == 'yes' ) {
            $user_password = $user_pass;
        } else {
            $user_password = wp_generate_password( $length=12, $include_standard_special_chars=false );
        }
        $user_id = wp_create_user( $usermane, $user_password, $email );

        if ( is_wp_error($user_id) ) {
            echo json_encode( array( 'success' => false, 'msg' => $user_id ) );
            wp_die();
        } else {

            wp_update_user( array( 'ID' => $user_id, 'role' => $user_role ) );

            if( $enable_password =='yes' ) {
                echo json_encode( array( 'success' => true, 'msg' => esc_html__('Your account was created and you can login now!', 'houzez') ) );
            } else {
                echo json_encode( array( 'success' => true, 'msg' => esc_html__('An email with the generated password was sent!', 'houzez') ) );
            }

            houzez_update_profile( $user_id );
            $user_as_agent = houzez_option('user_as_agent');

            if( $user_as_agent == 'yes' ) {
                houzez_register_as_agent ( $usermane, $email, $user_id );
            }
            houzez_wp_new_user_notification( $user_id, $user_password );
        }
        wp_die();

    }
}

/*-----------------------------------------------------------------------------------*/
// New register user notification
/*-----------------------------------------------------------------------------------*/
if( !function_exists('houzez_wp_new_user_notification') ) {

    function houzez_wp_new_user_notification( $user_id, $randonpassword = '' ) {

        $user = new WP_User( $user_id );

        $user_login = stripslashes( $user->user_login );
        $user_email = stripslashes( $user->user_email );

        // Send notification to admin
        $args = array(
            'user_login_register' => $user_login,
            'user_email_register' => $user_email
        );
        houzez_register_email_type( get_option('admin_email'), 'admin_new_user_register', $args );


        // Return if password in empty
        if ( empty( $randonpassword ) ) {
            return;
        }

        // Send notification to registered user
        $args = array(
            'user_login_register'  =>  $user_login,
            'user_email_register'  =>  $user_email,
            'user_pass_register'   => $randonpassword
        );
        houzez_register_email_type( $user_email, 'new_user_register', $args );

    }
}

add_action( 'wp_ajax_nopriv_houzez_reset_password', 'houzez_reset_password' );
add_action( 'wp_ajax_houzez_reset_password', 'houzez_reset_password' );

if( !function_exists('houzez_reset_password') ) {
    function houzez_reset_password() {
        check_ajax_referer('fave_resetpassword_nonce', 'security');

        $allowed_html = array();
        $user_login = wp_kses( $_POST['user_login'], $allowed_html );

        if ( empty( $user_login ) ) {
            echo json_encode(array( 'success' => false, 'msg' => esc_html__('Enter a username or email address.', 'houzez') ) );
            wp_die();
        }

        if ( strpos( $user_login, '@' ) ) {
            $user_data = get_user_by( 'email', trim( $user_login ) );
            if ( empty( $user_data ) ) {
                echo json_encode(array('success' => false, 'msg' => esc_html__('There is no user registered with that email address.', 'houzez')));
                wp_die();
            }
        } else {
            $login = trim( $user_login );
            $user_data = get_user_by('login', $login);

            if ( !$user_data ) {
                echo json_encode(array( 'success' => false, 'msg' => esc_html__('Invalid username', 'houzez') ) );
                wp_die();
            }
        }

        $user_login = $user_data->user_login;
        $user_email = $user_data->user_email;
        $key = get_password_reset_key( $user_data );

        if ( is_wp_error( $key ) ) {
            echo json_encode(array( 'success' => false, 'msg' => $key ) );
            wp_die();
        }



        $message = esc_html__('Someone has requested a password reset for the following account:', 'houzez' ) . "\r\n\r\n";
        $message .= network_home_url( '/' ) . "\r\n\r\n";
        $message .= sprintf(esc_html__('Username: %s', 'houzez'), $user_login) . "\r\n\r\n";
        $message .= esc_html__('If this was a mistake, just ignore this email and nothing will happen.', 'houzez') . "\r\n\r\n";
        $message .= esc_html__('To reset your password, visit the following address:', 'houzez') . "\r\n\r\n";
        $message .= '<' . network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user_login), 'login') . ">\r\n";

        if ( is_multisite() )
            $blogname = $GLOBALS['current_site']->site_name;
        else
            /*
             * The blogname option is escaped with esc_html on the way into the database
             * in sanitize_option we want to reverse this for the plain text arena of emails.
             */
            $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

            $title = sprintf( esc_html__('[%s] Password Reset', 'houzez'), $blogname );

        /**
         * Filter the subject of the password reset email.
         *
         * @since 2.8.0
         * @since 4.4.0 Added the `$user_login` and `$user_data` parameters.
         *
         * @param string  $title      Default email title.
         * @param string  $user_login The username for the user.
         * @param WP_User $user_data  WP_User object.
         */
        $title = apply_filters( 'retrieve_password_title', $title, $user_login, $user_data );

        /**
         * Filter the message body of the password reset mail.
         *
         * @since 2.8.0
         * @since 4.1.0 Added `$user_login` and `$user_data` parameters.
         *
         * @param string  $message    Default mail message.
         * @param string  $key        The activation key.
         * @param string  $user_login The username for the user.
         * @param WP_User $user_data  WP_User object.
         */

        $message = apply_filters( 'retrieve_password_message', $message, $key, $user_login, $user_data );

        if ( $message && !wp_mail( $user_email, wp_specialchars_decode( $title ), $message ) ) {
            echo json_encode(array('success' => false, 'msg' => esc_html__('The email could not be sent.', 'houzez') . "<br />\n" . esc_html__('Possible reason: your host may have disabled the mail() function.', 'houzez')));
            wp_die();
        } else {
            echo json_encode(array('success' => true, 'msg' => esc_html__('Check your email', 'houzez') ));
            wp_die();
        }
        return true;


    }
}
/*-----------------------------------------------------------------------------------*/
// Save Front-end user as agent
/*-----------------------------------------------------------------------------------*/

if( !function_exists('houzez_register_as_agent') ) {

    function houzez_register_as_agent( $username, $email, $user_id ) {
        // Create post object
        $args = array(
            'post_title'    => $username,
            'post_type' => 'houzez_agent',
            'post_status'   => 'publish'
        );

        // Insert the post into the database
        $post_id =  wp_insert_post( $args );
        update_post_meta( $post_id, 'houzez_user_meta_id', $user_id);  // used when agent custom post type updated
        update_user_meta( $user_id, 'fave_author_agent_id', $post_id) ;
        update_post_meta( $post_id, 'fave_agent_email', $email) ;
    }
}

if (!function_exists('houzez_register_email_type')) {
    function houzez_register_email_type( $email, $email_type, $args ) {

        $value_message = houzez_option('houzez_' . $email_type, '');
        $value_subject = houzez_option('houzez_subject_' . $email_type, '');

        if (function_exists('icl_translate')) {
            $value_message = icl_translate('houzez', 'houzez_email_' . $value_message, $value_message);
            $value_subject = icl_translate('houzez', 'houzez_email_subject_' . $value_subject, $value_subject);
        }

        houzez_register_emails_filter_replace( $email, $value_message, $value_subject, $args);
    }
}

if( !function_exists('houzez_register_emails_filter_replace')):
    function  houzez_register_emails_filter_replace( $email, $message, $subject, $args ) {
        $args ['website_url'] = get_option('siteurl');
        $args ['website_name'] = get_option('blogname');
        $args ['user_email'] = $email;
        $user = get_user_by( 'email', $email );
        $args ['username'] = $user->user_login;

        foreach( $args as $key => $val){
            $subject = str_replace( '%'.$key, $val, $subject );
            $message = str_replace( '%'.$key, $val, $message );
        }
        houzez_register_send_emails( $email, $subject, $message );
    }
endif;


if( !function_exists('houzez_register_send_emails') ):
    function houzez_register_send_emails( $user_email, $subject, $message ){
        $headers = 'From: No Reply <noreply@'.$_SERVER['HTTP_HOST'].'>' . "\r\n";

        @wp_mail(
            $user_email,
            $subject,
            $message,
            $headers
        );
    };
endif;