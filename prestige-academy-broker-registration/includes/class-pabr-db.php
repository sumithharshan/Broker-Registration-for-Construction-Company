<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class PABR_DB {

    public static function create_table() {
        global $wpdb;
        $table   = $wpdb->prefix . PABR_TABLE;
        $charset = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE IF NOT EXISTS {$table} (
            id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            entry_key   VARCHAR(8)   NOT NULL UNIQUE,
            name        VARCHAR(255) DEFAULT '',
            email       VARCHAR(255) DEFAULT '',
            phone       VARCHAR(50)  DEFAULT '',
            option_type VARCHAR(20)  NOT NULL,
            company     VARCHAR(255) NOT NULL,
            guest_count TINYINT UNSIGNED NOT NULL DEFAULT 1,
            message     TEXT,
            ip_address  VARCHAR(45)  DEFAULT '',
            browser     VARCHAR(512) DEFAULT '',
            submitted_at DATETIME NOT NULL,
            PRIMARY KEY (id),
            KEY idx_submitted (submitted_at),
            KEY idx_entry_key (entry_key)
        ) {$charset};";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql );
    }

    public static function insert( array $data ) {
        global $wpdb;
        $table = $wpdb->prefix . PABR_TABLE;
        $wpdb->insert(
            $table,
            array(
                'entry_key'    => $data['entry_key'],
                'name'         => sanitize_text_field( $data['name'] ),
                'email'        => sanitize_text_field( $data['email'] ),
                'phone'        => sanitize_text_field( $data['phone'] ),
                'option_type'  => sanitize_text_field( $data['option_type'] ),
                'company'      => sanitize_text_field( $data['company'] ),
                'guest_count'  => absint( $data['guest_count'] ),
                'message'      => sanitize_textarea_field( $data['message'] ),
                'ip_address'   => sanitize_text_field( $data['ip_address'] ),
                'browser'      => sanitize_text_field( $data['browser'] ),
                'submitted_at' => current_time( 'mysql' ),
            ),
            array( '%s','%s','%s','%s','%s','%s','%d','%s','%s','%s','%s' )
        );
        return $wpdb->insert_id;
    }

    public static function get_all() {
        global $wpdb;
        $table = $wpdb->prefix . PABR_TABLE;
        return $wpdb->get_results( "SELECT * FROM {$table} ORDER BY submitted_at DESC", ARRAY_A );
    }

    public static function delete( $id ) {
        global $wpdb;
        $table = $wpdb->prefix . PABR_TABLE;
        return $wpdb->delete( $table, array( 'id' => absint( $id ) ), array( '%d' ) );
    }

    public static function get_stats() {
        global $wpdb;
        $table = $wpdb->prefix . PABR_TABLE;
        return $wpdb->get_row(
            "SELECT COUNT(*) AS total_entries, COALESCE(SUM(guest_count), 0) AS total_guests FROM {$table}",
            ARRAY_A
        );
    }
}
