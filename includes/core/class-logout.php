<?php
namespace um\core;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'Logout' ) ) {
    class Logout {

        function __construct() {

            add_action('template_redirect', array(&$this, 'logout_page'), 10000 );

        }

        /***
         ***	@Logout via logout page
         ***/
        function logout_page() {

            $language_code 		= '';
            $current_page_ID    = get_the_ID();
            $logout_page_id 	= UM()->config()->permalinks['logout'];
            $trid 				= 0;

            if ( is_home() /*|| is_front_page()*/ ) {
                return;
            }

	        if ( UM()->external_integrations()->is_wpml_active() ) {
		        global $sitepress;
		        $default_lang = $sitepress->get_default_language();
		        $language_code = $sitepress->get_current_language();

		        if ( function_exists( 'icl_object_id' ) ) {
			        $trid = icl_object_id( $current_page_ID, 'page', true, $default_lang );
		        } else {
			        $trid = wpml_object_id_filter( $current_page_ID, 'page', true, $default_lang );
		        }

		        if ( $language_code == $default_lang ) {
			        $language_code = '';
		        }
	        }

            if ( um_is_core_page( 'logout' ) || ( $trid > 0 && $trid == $logout_page_id )  ) {

                if ( is_user_logged_in() ) {

                    if ( isset( $_REQUEST['redirect_to'] ) && $_REQUEST['redirect_to'] !== '' ) {
                        wp_logout();
                        session_unset();
                        exit( wp_redirect( $_REQUEST['redirect_to'] ) );
                    } else if ( um_user('after_logout') == 'redirect_home' ) {
                        wp_logout();
                        session_unset();
                        exit( wp_redirect( home_url( $language_code ) ) );
                    } else {
                        wp_logout();
                        session_unset();
                        exit( wp_redirect( um_user('logout_redirect_url') ) );

                    }

                } else {
                    exit( wp_redirect( home_url( $language_code ) ) );
                }

            }

        }

    }
}