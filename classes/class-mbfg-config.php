<?php
/**
 * MBFG Config.
 *
 * @package MBFG
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'FBFG_Config' ) ) {

	/**
	 * Class FBFG_Config.
	 */
	class FBFG_Config {

		/**
		 * Block Attributes
		 *
		 * @var block_attributes
		 */
		public static $block_attributes = null;

		/**
		 * Block Assets
		 *
		 * @var block_attributes
		 */
		public static $block_assets = null;


		/**
		 * Block Assets
		 *
		 * @since 1.0.0
		 * @var block_attributes
		 */
		public static $block_assets_css = null;

		/**
		 * Get Widget List.
		 *
		 * @since 1.0.0
		 *
		 * @return array The Widget List.
		 */
		public static function get_block_attributes() {
			if ( null === self::$block_attributes ) {
				self::$block_attributes = array(
					'mbfg/google-map'             => array(
						'slug'        => '',
						'title'       => __( 'Google Map', 'map-block-for-gutenberg' ),
						'description' => __( 'This block allows you to place a Google Map Location.', 'map-block-for-gutenberg' ),
						'default'     => true,
						'extension'   => false,
						'attributes'  => array(
							'block_id' => '',
							'height'   => '300',
							'language' => 'en',
						),
					),
				);
			}
			return self::$block_attributes;
		}

		/**
		 * Get Block Assets.
		 *
		 * @since 1.0.0
		 *
		 * @return array The Asset List.
		 */
		public static function get_block_assets() {
			$blocks      = FBFG_Admin_Helper::get_block_options();
			$post_js_dep = ( ( false === $blocks['mbfg/post-carousel']['is_activate'] ) ? array( 'jquery' ) : array( 'jquery', 'mbfg-slick-js' ) );

			if ( null === self::$block_assets ) {
				self::$block_assets = array(
					'mbfg-forms-js'          => array(
						'src' => FBFG_URL . 'assets/js/forms.js',
						'dep' => array( 'jquery' ),
					),
				);
			}
			return self::$block_assets;
		}

		/**
		 * Get Block Assets.
		 *
		 * @since 1.0.0
		 *
		 * @return array The Asset List.
		 */
		public static function get_block_assets_css() {
			if ( null === self::$block_assets_css ) {
				self::$block_assets_css = array(
					'mbfg/forms'                  => array(
						'name' => 'forms',
					),
					'mbfg/google-map'             => array(
						'name' => 'google-map',
					),
				);
			}
			return self::$block_assets_css;
		}
	}
}

