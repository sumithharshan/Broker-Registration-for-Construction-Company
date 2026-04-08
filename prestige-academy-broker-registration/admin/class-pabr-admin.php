<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class PABR_Admin {

    public static function init() {
        add_action( 'admin_menu',            array( __CLASS__, 'add_menu' ) );
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue' ) );
        add_action( 'wp_ajax_pabr_export_excel', array( __CLASS__, 'export_excel' ) );
    }

    public static function add_menu() {
        add_menu_page(
            __( 'Academy Registrations', 'prestige-academy-reg' ),
            __( 'Academy Reg.', 'prestige-academy-reg' ),
            'manage_options',
            'pabr-registrations',
            array( __CLASS__, 'render_page' ),
            'dashicons-groups',
            30
        );
    }

    public static function enqueue( $hook ) {
        if ( 'toplevel_page_pabr-registrations' !== $hook ) return;

        wp_enqueue_style( 'pabr-admin',
            PABR_PLUGIN_URL . 'assets/css/pabr-admin.css',
            array(), PABR_VERSION );

        /* DataTables */
        wp_enqueue_style( 'datatables-css',
            'https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css',
            array(), '1.13.6' );
        wp_enqueue_script( 'datatables-js',
            'https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js',
            array( 'jquery' ), '1.13.6', true );

        /* DataTables Buttons + Excel (SheetJS) */
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

        wp_enqueue_script( 'pabr-admin-js',
            PABR_PLUGIN_URL . 'assets/js/pabr-admin.js',
            array( 'datatables-html5' ), PABR_VERSION, true );

        wp_localize_script( 'pabr-admin-js', 'pabrAdminData', array(
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'pabr_nonce' ),
        ) );
    }

    public static function render_page() {
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Forbidden' );
        $entries = PABR_DB::get_all();
        include PABR_PLUGIN_DIR . 'admin/views/entries.php';
    }

    /**
     * Export all entries as Excel (CSV with BOM for Excel compatibility).
     * Pure PHP – no external library needed for basic XLSX-compatible CSV.
     */
    public static function export_excel() {
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Forbidden' );
        check_ajax_referer( 'pabr_nonce', 'nonce' );

        $entries = PABR_DB::get_all();

        $filename = 'pabr-registrations-' . date( 'Y-m-d' ) . '.csv';
        header( 'Content-Type: text/csv; charset=utf-8' );
        header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
        header( 'Pragma: no-cache' );
        header( 'Expires: 0' );

        $out = fopen( 'php://output', 'w' );
        /* UTF-8 BOM so Excel opens it correctly */
        fputs( $out, "\xEF\xBB\xBF" );

        fputcsv( $out, array(
            'ID', 'Entry Key', 'Name', 'Email', 'Phone',
            'Option', 'Company', 'Guest Count', 'Message',
            'IP Address', 'Browser', 'Submitted At',
        ) );

        foreach ( $entries as $row ) {
            fputcsv( $out, array(
                $row['id'],
                $row['entry_key'],
                $row['name'],
                $row['email'],
                $row['phone'],
                $row['option_type'],
                $row['company'],
                $row['guest_count'],
                $row['message'],
                $row['ip_address'],
                $row['browser'],
                $row['submitted_at'],
            ) );
        }
        fclose( $out );
        exit;
    }

    public static function parse_browser( $ua ) {
        if ( empty( $ua ) ) return '';
        if ( strpos( $ua, 'Edg' ) !== false )     return 'Edge';
        if ( strpos( $ua, 'OPR' ) !== false )     return 'Opera';
        if ( strpos( $ua, 'Chrome' ) !== false )  return 'Chrome';
        if ( strpos( $ua, 'Firefox' ) !== false ) return 'Firefox';
        if ( strpos( $ua, 'Safari' ) !== false )  return 'Safari';
        return 'Other';
    }
}

PABR_Admin::init();
