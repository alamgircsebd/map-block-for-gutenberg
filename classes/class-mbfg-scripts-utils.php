<?php
/**
 * MBFG Scripts Utils.
 *
 * @package MBFG
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class FBFG_Scripts_Utils.
 */
final class FBFG_Scripts_Utils {

	/**
	 * Enqueue Gutenberg block assets for both frontend + backend.
	 *
	 * @since 1.0.0
	 */
	public static function enqueue_blocks_dependency_both() {
		$blocks       = FBFG_Config::get_block_attributes();
		$saved_blocks = FBFG_Admin_Helper::get_admin_settings_option( '_mbfg_blocks', array() );
		$block_assets = FBFG_Config::get_block_assets();

		foreach ( $blocks as $slug => $value ) {
			$_slug = str_replace( 'mbfg/', '', $slug );

			if ( ! ( isset( $saved_blocks[ $_slug ] ) && 'disabled' === $saved_blocks[ $_slug ] ) ) {
				$js_assets  = ( isset( $blocks[ $slug ]['js_assets'] ) ) ? $blocks[ $slug ]['js_assets'] : array();
				$css_assets = ( isset( $blocks[ $slug ]['css_assets'] ) ) ? $blocks[ $slug ]['css_assets'] : array();

				foreach ( $js_assets as $asset_handle => $val ) {
					// Scripts.
					wp_register_script(
						$val, // Handle.
						$block_assets[ $val ]['src'],
						$block_assets[ $val ]['dep'],
						FBFG_VER,
						true
					);

					$skip_editor = isset( $block_assets[ $val ]['skipEditor'] ) ? $block_assets[ $val ]['skipEditor'] : false;

					if ( is_admin() && false === $skip_editor ) {
						wp_enqueue_script( $val );
					}
				}

				foreach ( $css_assets as $asset_handle => $val ) {
					// Styles.
					wp_register_style(
						$val, // Handle.
						$block_assets[ $val ]['src'],
						$block_assets[ $val ]['dep'],
						FBFG_VER
					);

					if ( is_admin() ) {
						wp_enqueue_style( $val );
					}
				}
			}
		}

		$mbfg_masonry_ajax_nonce = wp_create_nonce( 'mbfg_masonry_ajax_nonce' );
		wp_localize_script(
			'mbfg-post-js',
			'mbfg_data',
			array(
				'ajax_url'                => admin_url( 'admin-ajax.php' ),
				'mbfg_masonry_ajax_nonce' => $mbfg_masonry_ajax_nonce,
			)
		);
	}

	/**
	 * Enqueue block styles.
	 *
	 * @since 1.0.0
	 */
	public static function enqueue_blocks_styles() {
		$wp_upload_dir = FBFG_Helper::get_mbfg_upload_dir_path();

		if ( file_exists( $wp_upload_dir . 'custom-style-blocks.css' ) ) {

			$wp_upload_url = FBFG_Helper::get_mbfg_upload_url_path();

			wp_enqueue_style(
				'mbfg-block-css', // Handle.
				$wp_upload_url . 'custom-style-blocks.css', // Block style CSS.
				array(),
				FBFG_VER
			);
		} else {
			wp_enqueue_style(
				'mbfg-block-css', // Handle.
				FBFG_URL . 'dist/style-blocks.css', // Block style CSS.
				array(),
				FBFG_VER
			);
		}
	}

	/**
	 * Enqueue block rtl styles.
	 *
	 * @since 1.0.0
	 */
	public static function enqueue_blocks_rtl_styles() {
		if ( is_rtl() ) {
			wp_enqueue_style(
				'mbfg-style-rtl', // Handle.
				FBFG_URL . 'assets/css/style-blocks.rtl.css', // RTL style CSS.
				array(),
				FBFG_VER
			);
		}
	}

	/**
	 * Returns an array of paths for the CSS and JS assets
	 * of the current post.
	 *
	 * @param  var $type    Gets the CSS\JS type.
	 * @param  var $post_id Post ID.
	 * @since 1.0.0
	 * @return array
	 */
	public static function get_asset_info( $type, $post_id ) {
		$uploads_dir = FBFG_Helper::get_upload_dir();
		$file_name   = get_post_meta( $post_id, '_mbfg_' . $type . '_file_name', true );
		$path        = $type;
		$url         = $type . '_url';

		$info = array(
			$path => '',
			$url  => '',
		);

		if ( ! empty( $file_name ) ) {
			$info[ $path ] = $uploads_dir['path'] . $file_name;
			$info[ $url ]  = $uploads_dir['url'] . $file_name;
		}

		return $info;
	}
}
