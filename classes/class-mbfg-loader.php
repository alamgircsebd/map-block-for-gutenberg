<?php
/**
 * MBFG Loader.
 *
 * @package MBFG
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'FBFG_Loader' ) ) {

	/**
	 * Class FBFG_Loader.
	 */
	final class FBFG_Loader {

		/**
		 * Member Variable
		 *
		 * @var instance
		 */
		private static $instance;

		/**
		 *  Initiator
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Constructor
		 */
		public function __construct() {
			// Activation hook.
			register_activation_hook( FBFG_FILE, array( $this, 'activation_reset' ) );

			// deActivation hook.
			register_deactivation_hook( FBFG_FILE, array( $this, 'deactivation_reset' ) );

			if ( ! $this->is_gutenberg_active() ) {
				/* TO DO */
				add_action( 'admin_notices', array( $this, 'mbfg_fails_to_load' ) );
				return;
			}

			$this->define_constants();

			$this->loader();

			add_action( 'plugins_loaded', array( $this, 'load_plugin' ) );
		}

		/**
		 * Defines all constants
		 *
		 * @since 1.0.0
		 */
		public function define_constants() {
			define( 'FBFG_BASE', plugin_basename( FBFG_FILE ) );
			define( 'FBFG_DIR', plugin_dir_path( FBFG_FILE ) );
			define( 'FBFG_URL', plugins_url( '/', FBFG_FILE ) );
			define( 'FBFG_VER', '1.0.0' );
			define( 'FBFG_MODULES_DIR', FBFG_DIR . 'modules/' );
			define( 'FBFG_MODULES_URL', FBFG_URL . 'modules/' );
			define( 'FBFG_SLUG', 'mbfg' );
			define( 'FBFG_URI', trailingslashit( 'https://github.com/alamgircsebd/map-block-for-gutenberg/' ) );

			if ( ! defined( 'FBFG_TABLET_BREAKPOINT' ) ) {
				define( 'FBFG_TABLET_BREAKPOINT', '976' );
			}
			if ( ! defined( 'FBFG_MOBILE_BREAKPOINT' ) ) {
				define( 'FBFG_MOBILE_BREAKPOINT', '767' );
			}

			define( 'FBFG_ASSET_VER', get_option( '__mbfg_asset_version', FBFG_VER ) );
		}

		/**
		 * Loads Other files.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public function loader() {
			require_once FBFG_DIR . 'classes/class-mbfg-admin-helper.php';
			require_once FBFG_DIR . 'classes/class-mbfg-helper.php';
			require_once FBFG_DIR . 'classes/class-mbfg-scripts-utils.php';
			require_once FBFG_DIR . 'classes/class-mbfg-filesystem.php';
			require_once FBFG_DIR . 'classes/class-mbfg-update.php';
		}

		/**
		 * Loads plugin files.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public function load_plugin() {
			$this->load_textdomain();

			require_once FBFG_DIR . 'blocks-config/blocks-config.php';
			require_once FBFG_DIR . 'classes/class-mbfg-post-assets.php';
			require_once FBFG_DIR . 'classes/class-mbfg-front-assets.php';
			require_once FBFG_DIR . 'classes/class-mbfg-init-blocks.php';
			require_once FBFG_DIR . 'classes/class-mbfg-rest-api.php';

			if ( 'twentyseventeen' === get_template() ) {
				require_once FBFG_DIR . 'classes/class-mbfg-twenty-seventeen-compatibility.php';
			}
			add_filter( 'rest_pre_dispatch', array( $this, 'rest_pre_dispatch' ), 10, 3 );
		}

		/**
		 * Fix REST API issue with blocks registered via PHP register_block_type.
		 *
		 * @since 1.25.2
		 *
		 * @param mixed  $result  Response to replace the requested version with.
		 * @param object $server  Server instance.
		 * @param object $request Request used to generate the response.
		 *
		 * @return array Returns updated results.
		 */
		public function rest_pre_dispatch( $result, $server, $request ) {
			if ( strpos( $request->get_route(), '/wp/v2/block-renderer' ) !== false && isset( $request['attributes'] ) ) {

				$attributes = $request['attributes'];

				if ( isset( $attributes['FBFGUserRole'] ) ) {
					unset( $attributes['FBFGUserRole'] );
				}

				if ( isset( $attributes['FBFGrowser'] ) ) {
					unset( $attributes['FBFGrowser'] );
				}

				if ( isset( $attributes['FBFGSystem'] ) ) {
					unset( $attributes['FBFGSystem'] );
				}

				if ( isset( $attributes['FBFGDisplayConditions'] ) ) {
					unset( $attributes['FBFGDisplayConditions'] );
				}

				if ( isset( $attributes['FBFGHideDesktop'] ) ) {
					unset( $attributes['FBFGHideDesktop'] );
				}

				if ( isset( $attributes['FBFGHideMob'] ) ) {
					unset( $attributes['FBFGHideMob'] );
				}

				if ( isset( $attributes['FBFGHideTab'] ) ) {
					unset( $attributes['FBFGHideTab'] );
				}

				if ( isset( $attributes['FBFGLoggedIn'] ) ) {
					unset( $attributes['FBFGLoggedIn'] );
				}

				if ( isset( $attributes['FBFGLoggedOut'] ) ) {
					unset( $attributes['FBFGLoggedOut'] );
				}

					$request['attributes'] = $attributes;

			}

			return $result;
		}

		/**
		 * Check if Gutenberg is active
		 *
		 * @since 1.0.0
		 *
		 * @return boolean
		 */
		public function is_gutenberg_active() {
			return function_exists( 'register_block_type' );
		}

		/**
		 * Load Ultimate Gutenberg Text Domain.
		 * This will load the translation textdomain depending on the file priorities.
		 *      1. Global Languages /wp-content/languages/map-block-for-gutenberg/ folder
		 *      2. Local directory /wp-content/plugins/map-block-for-gutenberg/languages/ folder
		 *
		 * @since  1.0.0
		 * @return void
		 */
		public function load_textdomain() {
			/**
			 * Filters the languages directory path to use for AffiliateWP.
			 *
			 * @param string $lang_dir The languages directory path.
			 */
			$lang_dir = apply_filters( 'mbfg_languages_directory', FBFG_ROOT . '/languages/' );

			load_plugin_textdomain( 'map-block-for-gutenberg', false, $lang_dir );
		}

		/**
		 * Fires admin notice when Gutenberg is not installed and activated.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public function mbfg_fails_to_load() {
			if ( ! current_user_can( 'install_plugins' ) ) {
				return;
			}

			$class = 'notice notice-error';
			/* translators: %s: html tags */
			$message = sprintf( __( 'The %1$sMap Block for Gutenberg%2$s plugin requires %1$sGutenberg%2$s plugin installed & activated.', 'map-block-for-gutenberg' ), '<strong>', '</strong>' );

			$action_url   = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=gutenberg' ), 'install-plugin_gutenberg' );
			$button_label = __( 'Install Gutenberg', 'map-block-for-gutenberg' );

			$button = '<p><a href="' . $action_url . '" class="button-primary">' . $button_label . '</a></p><p></p>';

			printf( '<div class="%1$s"><p>%2$s</p>%3$s</div>', esc_attr( $class ), wp_kses_post( $message ), wp_kses_post( $button ) );
		}

		/**
		 * Activation Reset
		 */
		public function activation_reset() {
			update_option( '__mbfg_do_redirect', true );
			update_option( '__mbfg_asset_version', time() );
		}

		/**
		 * Deactivation Reset
		 */
		public function deactivation_reset() {
			update_option( '__mbfg_do_redirect', false );
		}
	}

	/**
	 *  Prepare if class 'FBFG_Loader' exist.
	 *  Kicking this off by calling 'get_instance()' method
	 */
	FBFG_Loader::get_instance();
}
