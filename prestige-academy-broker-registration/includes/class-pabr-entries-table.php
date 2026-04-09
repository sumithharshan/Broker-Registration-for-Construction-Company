<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class PABR_Entries_Table {

    public static function init() {
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue' ) );
        add_action( 'wp_ajax_pabr_fe_export',        array( __CLASS__, 'export_csv' ) );
        add_action( 'wp_ajax_nopriv_pabr_fe_export', array( __CLASS__, 'export_csv' ) );
    }

    public static function enqueue() {
        global $post;
        /* Only load assets on pages that contain the shortcode */
        if ( ! is_a( $post, 'WP_Post' ) || ! has_shortcode( $post->post_content, 'prestige_entries_table' ) ) {
            return;
        }

        wp_enqueue_style( 'pabr-fe-table',
            PABR_PLUGIN_URL . 'assets/css/pabr-fe-table.css',
            array(), PABR_VERSION );

        wp_enqueue_style( 'datatables-css',
            'https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css',
            array(), '1.13.6' );

        wp_enqueue_script( 'datatables-js',
            'https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js',
            array( 'jquery' ), '1.13.6', true );

        wp_enqueue_script( 'datatables-buttons',
            'https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js',
            array( 'datatables-js' ), '2.4.2', true );

        wp_enqueue_script( 'jszip',
            'https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js',
            array(), '3.10.1', true );

        wp_enqueue_script( 'datatables-html5',
            'https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js',
            array( 'datatables-buttons', 'jszip' ), '2.4.2', true );

        wp_enqueue_style( 'datatables-buttons-css',
            'https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css',
            array(), '2.4.2' );

        wp_enqueue_script( 'pabr-fe-table-js',
            PABR_PLUGIN_URL . 'assets/js/pabr-fe-table.js',
            array( 'datatables-html5' ), PABR_VERSION, true );

        wp_localize_script( 'pabr-fe-table-js', 'pabrFeData', array(
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'pabr_nonce' ),
        ) );
    }

    public static function render( $atts ) {
        $atts = shortcode_atts( array(
            'per_page' => 25,
        ), $atts, 'prestige_entries_table' );

        $entries = PABR_DB::get_all();

        ob_start();
        include PABR_PLUGIN_DIR . 'templates/fe-table.php';
        return ob_get_clean();
    }

    public static function export_csv() {
        check_ajax_referer( 'pabr_nonce', 'nonce' );

        $entries  = PABR_DB::get_all();
        $filename = 'registrations-' . date( 'Y-m-d' ) . '.csv';

        header( 'Content-Type: text/csv; charset=utf-8' );
        header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
        header( 'Pragma: no-cache' );
        header( 'Expires: 0' );

        $out = fopen( 'php://output', 'w' );
        fputs( $out, "\xEF\xBB\xBF" );

        fputcsv( $out, array(
            'Entry Key', 'Name', 'Email', 'Phone',
            'Option', 'Company', 'Guest Count', 'Message', 'Submitted At',
        ) );

        foreach ( $entries as $row ) {
            fputcsv( $out, array(
                $row['entry_key'],
                $row['name'],
                $row['email'],
                $row['phone'],
                $row['option_type'],
                $row['company'],
                $row['guest_count'],
                $row['message'],
                $row['submitted_at'],
            ) );
        }

        fclose( $out );
        exit;
    }
}

PABR_Entries_Table::init();
