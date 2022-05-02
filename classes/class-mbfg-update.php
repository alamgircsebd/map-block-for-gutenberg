<?php
/**
 * Update Compatibility
 *
 * @package MBFG
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'FBFG_Update' ) ) :

	/**
	 * MBFG Update initial setup
	 *
	 * @since 1.0.0
	 */
	class FBFG_Update {

		/**
		 * Class instance.
		 *
		 * @access private
		 * @var $instance Class instance.
		 */
		private static $instance;

		/**
		 * Initiator
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 *  Constructor
		 */
		public function __construct() {
			add_action( 'admin_init', array( $this, 'init' ) );
		}

		/**
		 * Init
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function init() {
			// Get auto saved version number.
			$saved_version = get_option( 'mbfg-version', false );

			// Update auto saved version number.
			if ( ! $saved_version ) {

				// Fresh install updation.
				$this->fresh_install_update_asset_generation_option();

				// Update current version.
				update_option( 'mbfg-version', FBFG_VER );
				return;
			}

			do_action( 'mbfg_update_before' );

			// If equals then return.
			if ( version_compare( $saved_version, FBFG_VER, '=' ) ) {
				return;
			}

			/* Create activated blocks stylesheet */
			FBFG_Admin_Helper::create_specific_stylesheet();

			// Update asset version number.
			update_option( '__mbfg_asset_version', time() );

			// Update auto saved version number.
			update_option( 'mbfg-version', FBFG_VER );

			do_action( 'mbfg_update_after' );
		}

		/**
		 * Update asset generation option if it is not exist.
		 *
		 * @since 1.22.4
		 * @return void
		 */
		public function fresh_install_update_asset_generation_option() {
			if ( FBFG_Helper::is_mbfg_dir_has_write_permissions() ) {
				update_option( '_mbfg_allow_file_generation', 'enabled' );
			}
		}
	}

	/**
	 * Kicking this off by calling 'get_instance()' method
	 */
	FBFG_Update::get_instance();

endif;
