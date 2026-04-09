<?php
/**
 * Plugin Name: Prestige Academy Broker Registration
 * Description: Captures broker registration details for Prestige Academy events.
 * Version: 1.0.0
 * Author: Sumith Harshan
 * Text Domain: prestige-academy-reg
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'PABR_VERSION', '1.0.0' );
define( 'PABR_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'PABR_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'PABR_TABLE', 'pabr_registrations' );

require_once PABR_PLUGIN_DIR . 'includes/class-pabr-db.php';
require_once PABR_PLUGIN_DIR . 'includes/class-pabr-form.php';
require_once PABR_PLUGIN_DIR . 'includes/class-pabr-ajax.php';

if ( is_admin() ) {
    require_once PABR_PLUGIN_DIR . 'admin/class-pabr-admin.php';
}

register_activation_hook( __FILE__, 'pabr_activate' );
register_deactivation_hook( __FILE__, 'pabr_deactivate' );

function pabr_activate() {
    PABR_DB::create_table();
    // Grant editors access to the registrations page.
    $editor = get_role( 'editor' );
    if ( $editor ) {
        $editor->add_cap( 'pabr_view_registrations' );
    }
    $admin = get_role( 'administrator' );
    if ( $admin ) {
        $admin->add_cap( 'pabr_view_registrations' );
    }
}

function pabr_deactivate() {
    // Remove the custom capability on deactivation.
    foreach ( array( 'administrator', 'editor' ) as $role_name ) {
        $role = get_role( $role_name );
        if ( $role ) {
            $role->remove_cap( 'pabr_view_registrations' );
        }
    }
}

add_shortcode( 'prestige_registration_form', array( 'PABR_Form', 'render' ) );
