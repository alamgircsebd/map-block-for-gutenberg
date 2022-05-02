<?php
/**
 * Plugin Name: Map Block for Gutenberg
 * Plugin URI: https://github.com/alamgircsebd/map-block-for-gutenberg
 * Author: Alamgir
 * Author URI: https://github.com/alamgircsebd/
 * Version: 1.0.0
 * Description: Easy to use Google Map with gutenberg editor.
 * Text Domain: map-block-for-gutenberg
 *
 * @package MBFG
 */

define( 'FBFG_FILE', __FILE__ );
define( 'FBFG_ROOT', dirname( plugin_basename( FBFG_FILE ) ) );
define( 'FBFG_PLUGIN_NAME', 'Map Block for Gutenberg' );
define( 'FBFG_PLUGIN_SHORT_NAME', 'MBFG' );

if ( ! version_compare( PHP_VERSION, '5.6', '>=' ) ) {
	add_action( 'admin_notices', 'mbfg_fail_php_version' );
} elseif ( ! version_compare( get_bloginfo( 'version' ), '4.7', '>=' ) ) {
	add_action( 'admin_notices', 'mbfg_fail_wp_version' );
} else {
	require_once 'classes/class-mbfg-loader.php';
}

/**
 * Map Block for Gutenberg admin notice for minimum PHP version.
 *
 * Warning when the site doesn't have the minimum required PHP version.
 *
 * @since 1.0.0
 *
 * @return void
 */
function mbfg_fail_php_version() {
	/* translators: %s: PHP version */
	$message      = sprintf( esc_html__( 'Map Block for Gutenberg requires PHP version %s+, plugin is currently NOT RUNNING.', 'map-block-for-gutenberg' ), '5.6' );
	$html_message = sprintf( '<div class="error">%s</div>', wpautop( $message ) );
	echo wp_kses_post( $html_message );
}


/**
 * Map Block for Gutenberg admin notice for minimum WordPress version.
 *
 * Warning when the site doesn't have the minimum required WordPress version.
 *
 * @since 1.0.0
 *
 * @return void
 */
function mbfg_fail_wp_version() {
	/* translators: %s: WordPress version */
	$message      = sprintf( esc_html__( 'Map Block for Gutenberg requires WordPress version %s+. Because you are using an earlier version, the plugin is currently NOT RUNNING.', 'map-block-for-gutenberg' ), '4.7' );
	$html_message = sprintf( '<div class="error">%s</div>', wpautop( $message ) );
	echo wp_kses_post( $html_message );
}
