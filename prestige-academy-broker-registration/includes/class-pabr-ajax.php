<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class PABR_Ajax {

    public static function init() {
        add_action( 'wp_ajax_pabr_search_company',        array( __CLASS__, 'search_company' ) );
        add_action( 'wp_ajax_nopriv_pabr_search_company', array( __CLASS__, 'search_company' ) );
        add_action( 'wp_ajax_pabr_submit',                array( __CLASS__, 'submit' ) );
        add_action( 'wp_ajax_nopriv_pabr_submit',         array( __CLASS__, 'submit' ) );
    }

    /**
     * Server-side company autocomplete — returns max 20 matches.
     * Only triggered after 3+ chars to keep response fast.
     */
    public static function search_company() {
        // Accept nonce from GET for autocomplete requests
        $nonce = isset( $_GET['nonce'] ) ? sanitize_text_field( wp_unslash( $_GET['nonce'] ) ) : '';
        if ( ! wp_verify_nonce( $nonce, 'pabr_nonce' ) ) {
            wp_send_json_error( array( 'message' => 'Invalid request.' ) );
        }

        $term = isset( $_GET['term'] ) ? strtoupper( trim( sanitize_text_field( wp_unslash( $_GET['term'] ) ) ) ) : '';

        if ( strlen( $term ) < 3 ) {
            wp_send_json_success( array() );
        }

        $companies = get_transient( 'pabr_companies_list' );
        if ( false === $companies ) {
            $file = PABR_PLUGIN_DIR . 'data/companies.json';
            if ( ! file_exists( $file ) ) {
                wp_send_json_success( array() );
            }
            $companies = json_decode( file_get_contents( $file ), true );
            if ( ! is_array( $companies ) ) {
                wp_send_json_success( array() );
            }
            set_transient( 'pabr_companies_list', $companies, DAY_IN_SECONDS );
        }

        $matches = array();
        $limit   = 20;
        foreach ( $companies as $name ) {
            if ( false !== strpos( strtoupper( $name ), $term ) ) {
                $matches[] = $name;
                if ( count( $matches ) >= $limit ) break;
            }
        }

        wp_send_json_success( $matches );
    }

    /**
     * Handle registration form submission.
     */
    public static function submit() {
        check_ajax_referer( 'pabr_nonce', 'nonce' );

        $name        = isset( $_POST['pabr_name'] )    ? sanitize_text_field( wp_unslash( $_POST['pabr_name'] ) )    : '';
        $email       = isset( $_POST['pabr_email'] )   ? sanitize_email( wp_unslash( $_POST['pabr_email'] ) )        : '';
        $phone       = isset( $_POST['pabr_phone'] )   ? sanitize_text_field( wp_unslash( $_POST['pabr_phone'] ) )   : '';
        $option_type = isset( $_POST['pabr_option'] )  ? sanitize_text_field( wp_unslash( $_POST['pabr_option'] ) )  : '';
        $company     = isset( $_POST['pabr_company'] ) ? sanitize_text_field( wp_unslash( $_POST['pabr_company'] ) ) : '';
        $guest_count = isset( $_POST['pabr_guests'] )  ? absint( $_POST['pabr_guests'] )                            : 1;
        $message     = isset( $_POST['pabr_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['pabr_message'] ) ) : '';
        $browser     = isset( $_POST['pabr_browser'] ) ? sanitize_text_field( wp_unslash( $_POST['pabr_browser'] ) ) : '';

        if ( empty( $name ) || empty( $phone ) || empty( $option_type ) || empty( $company ) || $guest_count < 1 ) {
            wp_send_json_error( array( 'message' => __( 'Please fill in all required fields.', 'prestige-academy-reg' ) ) );
        }

        $allowed_options = array( 'Broker', 'Individual', 'Client' );
        if ( ! in_array( $option_type, $allowed_options, true ) ) {
            wp_send_json_error( array( 'message' => __( 'Invalid option selected.', 'prestige-academy-reg' ) ) );
        }

        $ip = self::get_ip();

        // Generate a unique 8-char key (A-Z + 0-9), verified against the DB.
        do {
            $entry_key = self::generate_entry_key( 8 );
        } while ( self::entry_key_exists( $entry_key ) );

        $id = PABR_DB::insert( array(
            'entry_key'   => $entry_key,
            'name'        => $name,
            'email'       => $email,
            'phone'       => $phone,
            'option_type' => $option_type,
            'company'     => $company,
            'guest_count' => $guest_count,
            'message'     => $message,
            'ip_address'  => $ip,
            'browser'     => $browser,
        ) );

        if ( ! $id ) {
            wp_send_json_error( array( 'message' => __( 'Could not save registration. Please try again.', 'prestige-academy-reg' ) ) );
        }

        wp_send_json_success( array(
            'message'   => __( 'Registration submitted successfully!', 'prestige-academy-reg' ),
            'entry_key' => $entry_key,
        ) );
    }

    private static function generate_entry_key( $length = 8 ) {
        $chars  = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $max    = strlen( $chars ) - 1;
        $result = '';
        for ( $i = 0; $i < $length; $i++ ) {
            $result .= $chars[ random_int( 0, $max ) ];
        }
        return $result;
    }

    private static function entry_key_exists( $key ) {
        global $wpdb;
        $table = $wpdb->prefix . PABR_TABLE;
        return (bool) $wpdb->get_var(
            $wpdb->prepare( "SELECT COUNT(*) FROM {$table} WHERE entry_key = %s", $key )
        );
    }

    private static function get_ip() {
        $keys = array(
            'HTTP_CF_CONNECTING_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_REAL_IP',
            'REMOTE_ADDR',
        );
        foreach ( $keys as $key ) {
            if ( ! empty( $_SERVER[ $key ] ) ) {
                $ip = trim( explode( ',', sanitize_text_field( wp_unslash( $_SERVER[ $key ] ) ) )[0] );
                if ( filter_var( $ip, FILTER_VALIDATE_IP ) ) {
                    return $ip;
                }
            }
        }
        return '';
    }
}

PABR_Ajax::init();
