<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class PABR_Form {

    public static function init() {
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue' ) );
    }

    public static function enqueue() {
        wp_register_style(
            'pabr-form',
            PABR_PLUGIN_URL . 'assets/css/pabr-form.css',
            array(),
            PABR_VERSION
        );
        wp_register_script(
            'pabr-form',
            PABR_PLUGIN_URL . 'assets/js/pabr-form.js',
            array( 'jquery' ),
            PABR_VERSION,
            true
        );
        wp_localize_script( 'pabr-form', 'pabrData', array(
            'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
            'nonce'     => wp_create_nonce( 'pabr_nonce' ),
            'pluginUrl' => PABR_PLUGIN_URL,
            'i18n'      => array(
                'required'  => __( 'Please fill in all required fields.', 'prestige-academy-reg' ),
                'submitting'=> __( 'Submitting…', 'prestige-academy-reg' ),
                'submit'    => __( 'Submit Registration', 'prestige-academy-reg' ),
                'success'   => __( 'Registration submitted successfully!', 'prestige-academy-reg' ),
                'typeOther' => __( 'Type your company name…', 'prestige-academy-reg' ),
            ),
        ) );
    }

    public static function render( $atts = array() ) {
        wp_enqueue_style( 'pabr-form' );
        wp_enqueue_script( 'pabr-form' );

        ob_start();
        include PABR_PLUGIN_DIR . 'templates/form.php';
        return ob_get_clean();
    }
}

PABR_Form::init();
