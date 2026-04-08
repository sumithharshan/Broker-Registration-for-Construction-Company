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

register_activation_hook( __FILE__, array( 'PABR_DB', 'create_table' ) );

add_shortcode( 'prestige_registration_form', array( 'PABR_Form', 'render' ) );
