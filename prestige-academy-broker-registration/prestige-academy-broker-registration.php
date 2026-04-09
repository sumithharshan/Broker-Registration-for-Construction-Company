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
add_shortcode( 'prestige_registration_stats', 'pabr_stats_shortcode' );

function pabr_stats_shortcode( $atts ) {
    $atts = shortcode_atts( array(
        'show' => 'both',   // 'entries', 'guests', or 'both'
        'label_entries' => 'Total Registrations',
        'label_guests'  => 'Total Guests',
    ), $atts, 'prestige_registration_stats' );

    $stats = PABR_DB::get_stats();
    $entries = isset( $stats['total_entries'] ) ? (int) $stats['total_entries'] : 0;
    $guests  = isset( $stats['total_guests'] )  ? (int) $stats['total_guests']  : 0;

    $show = sanitize_text_field( $atts['show'] );

    ob_start();
    ?>
    <div class="pabr-stats-wrap">
        <?php if ( $show === 'both' || $show === 'entries' ) : ?>
        <div class="pabr-stat-box">
            <span class="pabr-stat-number"><?php echo esc_html( $entries ); ?></span>
            <span class="pabr-stat-label"><?php echo esc_html( $atts['label_entries'] ); ?></span>
        </div>
        <?php endif; ?>
        <?php if ( $show === 'both' || $show === 'guests' ) : ?>
        <div class="pabr-stat-box">
            <span class="pabr-stat-number"><?php echo esc_html( $guests ); ?></span>
            <span class="pabr-stat-label"><?php echo esc_html( $atts['label_guests'] ); ?></span>
        </div>
        <?php endif; ?>
    </div>
    <style>
    .pabr-stats-wrap{display:flex;gap:20px;flex-wrap:wrap;}
    .pabr-stat-box{display:flex;flex-direction:column;align-items:center;justify-content:center;background:#1e1e1e;border:1px solid #3a3a3a;border-radius:8px;padding:24px 36px;min-width:140px;text-align:center;}
    .pabr-stat-number{font-size:42px;font-weight:700;color:#fff;line-height:1;}
    .pabr-stat-label{font-size:13px;color:#aaa;margin-top:8px;text-transform:uppercase;letter-spacing:.5px;}
    </style>
    <?php
    return ob_get_clean();
}
