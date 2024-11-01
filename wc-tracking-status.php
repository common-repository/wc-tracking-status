<?php
/**
 * Plugin Name: WC Tracking Status
 * Plugin URI:  https://gitlab.com/diurvan/wc-tracking-status.git
 * Description: Update and show aditional states for tracking orders from woocommerce.
 * Author:      diurvan Consultores
 * Author URI:  https://diurvanconsultores.com
 * Version: 2.0.3
 * Requires at least: 5.5
 * Tested up to: 6.2.2
 * Text Domain: diu-wc-tracking-status
 * Domain Path: /languages/
 */

defined( 'ABSPATH' ) or die( '¡Sin trampas!' );

if ( version_compare( PHP_VERSION, '5.5.0', '>=' ) ) {
	/*
    * functions.php
    *
    */
    require_once( __DIR__ . '/includes/admin/track-config.php');
    require_once( __DIR__ . '/includes/front/track-view.php');

	add_filter( 'all_plugins', 'diu_wc_tracking_status' );

	function diu_wc_tracking_status( $all_plugins ) {   
		if ( isset( $all_plugins['wc-tracking-status/wc-tracking-status.php'] ) ) {
			$all_plugins['wc-tracking-status/wc-tracking-status.php']['Description'] .= 'These are my favorite plugins: <a href="https://diurvanconsultores.com/portafolio/plugin-dni-ruc-checkout/">DNI/RUC CHECKOUT SUNAT</a>,  <a href="https://diurvanconsultores.com/producto/plugin-envio-por-comuna-chile-woocommerce/">LISTA LOCALIDADES CHILE WOOCOMMERCE</a>,  <a href="https://diurvanconsultores.com/portafolio/plugin-wc-tracking-shipping/">WC TRACKING SHIPPING</a> y  <a href="https://diurvanconsultores.com/portafolio/plugin-exportar-productos-de-woocommerce-a-pdf/">EXPORT PRODUCTS PDF</a>.             .Please visit on <a href="https://facebook.com/diurvanconsultores">Facebook</a> and <a href="https://www.youtube.com/channel/UCu29w3t1XwSfIp80avLgrcQ">YouTube</a>';
		}
		return $all_plugins;
	}

	return;
}

add_action('plugins_loaded', 'diu_wc_tracking_status_set_language');
function diu_wc_tracking_status_set_language(){
	$path_languages = basename(dirname(__FILE__)).'/languages/';
 	load_plugin_textdomain('diu-wc-tracking-status', false, $path_languages );
}

/**
 * Display a warning message and deactivate the plugin if the user is using an incompatible version of PHP.
 */
function diu_validate_php_version() {
	echo '<div class="error fade">',
		'<p><strong>','WC Tracking Status requires PHP 5.5 or later!', '</strong></p>',
		'<p>', 'Please upgrade your server to the latest version of PHP – contact your web host if you are unsure about how to do this.', '</p>',
		'</div>';

	deactivate_plugins( plugin_basename( __FILE__ ) );
}
add_action( 'admin_notices', 'diu_validate_php_version' );