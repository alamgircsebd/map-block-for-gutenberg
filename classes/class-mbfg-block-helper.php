<?php
/**
 * MBFG Block Helper.
 *
 * @package MBFG
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'FBFG_Block_Helper' ) ) {

	/**
	 * Class FBFG_Block_Helper.
	 */
	class FBFG_Block_Helper {

		/**
		 * Get Condition block CSS.
		 *
		 * @since 1.0.0
		 */
		public static function get_condition_block_css() {

			return '@media (min-width: 1025px){body .mbfg-hide-desktop.mbfg-google-map__wrap,body .mbfg-hide-desktop{display:none}}@media (min-width: 768px) and (max-width: 1024px){body .mbfg-hide-tab.mbfg-google-map__wrap,body .mbfg-hide-tab{display:none}}@media (max-width: 767px){body .mbfg-hide-mob.mbfg-google-map__wrap,body .mbfg-hide-mob{display:none}}';
		}
	}
}
