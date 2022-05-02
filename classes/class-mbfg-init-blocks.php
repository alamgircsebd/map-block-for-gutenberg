<?php
/**
 * MBFG Blocks Initializer
 *
 * Enqueue CSS/JS of all the blocks.
 *
 * @since   1.0.0
 * @package MBFG
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * FBFG_Init_Blocks.
 *
 * @package MBFG
 */
class FBFG_Init_Blocks {


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

		// Hook: Editor assets.
		add_action( 'enqueue_block_editor_assets', array( $this, 'editor_assets' ) );

		if ( version_compare( get_bloginfo( 'version' ), '5.8', '>=' ) ) {
			add_filter( 'block_categories_all', array( $this, 'register_block_category' ), 10, 2 );
		} else {
			add_filter( 'block_categories', array( $this, 'register_block_category' ), 10, 2 );
		}

		if ( ! is_admin() ) {
			add_action( 'render_block', array( $this, 'render_block' ), 5, 2 );
		}
	}
	/**
	 * Render block.
	 *
	 * @param mixed $block_content The block content.
	 * @param array $block The block data.
	 * @since 1.0.0
	 * @return mixed Returns the new block content.
	 */
	public function render_block( $block_content, $block ) {

		$block_attributes = $block['attrs'];

		if ( isset( $block_attributes['FBFGDisplayConditions'] ) && array_key_exists( 'FBFGDisplayConditions', $block_attributes ) ) {

			switch ( $block_attributes['FBFGDisplayConditions'] ) {

				case 'userstate':
					$block_content = $this->user_state_visibility( $block_attributes, $block_content );
					break;

				case 'userRole':
					$block_content = $this->user_role_visibility( $block_attributes, $block_content );
					break;

				case 'browser':
					$block_content = $this->browser_visibility( $block_attributes, $block_content );
					break;

				case 'os':
					$block_content = $this->os_visibility( $block_attributes, $block_content );
					break;

				default:
					// code...
					break;
			}
		}
		return $block_content;
	}
	/**
	 * User State Visibility.
	 *
	 * @param array $block_attributes The block data.
	 * @param mixed $block_content The block content.
	 *
	 * @since 1.0.0
	 * @return mixed Returns the new block content.
	 */
	public function user_role_visibility( $block_attributes, $block_content ) {
		$user = wp_get_current_user();

		if ( isset( $block_attributes['FBFGUserRole'] ) && array_key_exists( 'FBFGUserRole', $block_attributes ) ) {

			$value = $block_attributes['FBFGUserRole'];

			if ( is_user_logged_in() && in_array( $value, $user->roles, true ) ) {
				return '';
			}
		}
		return $block_content;
	}
	/**
	 * User State Visibility.
	 *
	 * @param array $block_attributes The block data.
	 * @param mixed $block_content The block content.
	 * @since 1.0.0
	 * @return mixed Returns the new block content.
	 */
	public function os_visibility( $block_attributes, $block_content ) {
		if ( ! array_key_exists( 'FBFGSystem', $block_attributes ) ) {
			return $block_content;
		}

		$value = $block_attributes['FBFGSystem'];

		$os = array(
			'iphone'   => '(iPhone)',
			'android'  => '(Android)',
			'windows'  => 'Win16|(Windows 95)|(Win95)|(Windows_95)|(Windows 98)|(Win98)|(Windows NT 5.0)|(Windows 2000)|(Windows NT 5.1)|(Windows XP)|(Windows NT 5.2)|(Windows NT 6.0)|(Windows Vista)|(Windows NT 6.1)|(Windows 7)|(Windows NT 4.0)|(WinNT4.0)|(WinNT)|(Windows NT)|Windows ME',
			'open_bsd' => 'OpenBSD',
			'sun_os'   => 'SunOS',
			'linux'    => '(Linux)|(X11)',
			'mac_os'   => '(Mac_PowerPC)|(Macintosh)',
		);

		if ( preg_match( '@' . $os[ $value ] . '@', $_SERVER['HTTP_USER_AGENT'] ) ) {
			return '';
		}

		return $block_content;
	}
	/**
	 * User State Visibility.
	 *
	 * @param array $block_attributes The block data.
	 * @param mixed $block_content The block content.
	 *
	 * @since 1.0.0
	 * @return mixed Returns the new block content.
	 */
	public function browser_visibility( $block_attributes, $block_content ) {
		if ( ! array_key_exists( 'FBFGrowser', $block_attributes ) ) {
			return $block_content;
		}

		$browsers = array(
			'ie'         => array(
				'MSIE',
				'Trident',
			),
			'firefox'    => 'Firefox',
			'chrome'     => 'Chrome',
			'opera_mini' => 'Opera Mini',
			'opera'      => 'Opera',
			'safari'     => 'Safari',
		);

		$value = $block_attributes['FBFGrowser'];

		$show = false;

		if ( 'ie' === $value ) {
			if ( false !== strpos( $_SERVER['HTTP_USER_AGENT'], $browsers[ $value ][0] ) || false !== strpos( $_SERVER['HTTP_USER_AGENT'], $browsers[ $value ][1] ) ) {
				$show = true;
			}
		} else {
			if ( false !== strpos( $_SERVER['HTTP_USER_AGENT'], $browsers[ $value ] ) ) {
				$show = true;

				// Additional check for Chrome that returns Safari.
				if ( 'safari' === $value || 'firefox' === $value ) {
					if ( false !== strpos( $_SERVER['HTTP_USER_AGENT'], 'Chrome' ) ) {
						$show = false;
					}
				}
			}
		}

		return ( $show ) ? '' : $block_content;
	}
	/**
	 * User State Visibility.
	 *
	 * @param array $block_attributes The block data.
	 * @param mixed $block_content The block content.
	 *
	 * @since 1.0.0
	 * @return mixed Returns the new block content.
	 */
	public function user_state_visibility( $block_attributes, $block_content ) {
		if ( isset( $block_attributes['FBFGLoggedIn'] ) && $block_attributes['FBFGLoggedIn'] && is_user_logged_in() ) {
			return '';
		}

		if ( isset( $block_attributes['FBFGLoggedOut'] ) && $block_attributes['FBFGLoggedOut'] && ! is_user_logged_in() ) {
			return '';
		}

		return $block_content;

	}

	/**
	 * Gutenberg block category for MBFG.
	 *
	 * @param array  $categories Block categories.
	 * @param object $post Post object.
	 * @since 1.0.0
	 */
	public function register_block_category( $categories, $post ) {
		return array_merge(
			$categories,
			array(
				array(
					'slug'  => 'mbfg',
					'title' => __( 'Form Blocks Blocks', 'map-block-for-gutenberg' ),
				),
			)
		);
	}

	/**
	 * Enqueue Gutenberg block assets for backend editor.
	 *
	 * @since 1.0.0
	 */
	public function editor_assets() {
		$mbfg_ajax_nonce = wp_create_nonce( 'mbfg_ajax_nonce' );
		$script_dep_path = FBFG_DIR . 'dist/blocks.asset.php';
		$script_info     = file_exists( $script_dep_path )
			? include $script_dep_path
			: array(
				'dependencies' => array(),
				'version'      => FBFG_VER,
			);
		if ( version_compare( get_bloginfo( 'version' ), '5.8', '<' ) ) {
			$script_dep = array_merge( $script_info['dependencies'], array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-components', 'wp-editor', 'wp-api-fetch' ) );
		} else {
			$script_dep = $script_info['dependencies'];
		}
		// Scripts.
		wp_enqueue_script(
			'mbfg-block-editor-js', // Handle.
			FBFG_URL . 'dist/blocks.js',
			$script_dep, // Dependencies, defined above.
			$script_info['version'], // FBFG_VER.
			true // Enqueue the script in the footer.
		);

		wp_set_script_translations( 'mbfg-block-editor-js', 'map-block-for-gutenberg' );

		// Styles.
		wp_enqueue_style(
			'mbfg-block-editor-css', // Handle.
			FBFG_URL . 'dist/blocks.css', // Block editor CSS.
			array( 'wp-edit-blocks' ), // Dependency to include the CSS after it.
			FBFG_VER
		);

		// Common Editor style.
		wp_enqueue_style(
			'mbfg-block-common-editor-css', // Handle.
			FBFG_URL . 'admin/assets/common-block-editor.css', // Block editor CSS.
			array( 'wp-edit-blocks' ), // Dependency to include the CSS after it.
			FBFG_VER
		);

		wp_enqueue_script( 'mbfg-deactivate-block-js', FBFG_URL . 'admin/assets/blocks-deactivate.js', array( 'wp-blocks' ), FBFG_VER, true );

		$blocks       = array();
		$saved_blocks = FBFG_Admin_Helper::get_admin_settings_option( '_mbfg_blocks' );

		if ( is_array( $saved_blocks ) ) {
			foreach ( $saved_blocks as $slug => $data ) {
				$_slug         = 'mbfg/' . $slug;
				$current_block = FBFG_Config::$block_attributes[ $_slug ];

				if ( isset( $current_block['is_child'] ) && $current_block['is_child'] ) {
					continue;
				}

				if ( isset( $current_block['is_active'] ) && ! $current_block['is_active'] ) {
					continue;
				}

				if ( isset( $saved_blocks[ $slug ] ) ) {
					if ( 'disabled' === $saved_blocks[ $slug ] ) {
						array_push( $blocks, $_slug );
					}
				}
			}
		}

		wp_localize_script(
			'mbfg-deactivate-block-js',
			'mbfg_deactivate_blocks',
			array(
				'deactivated_blocks' => $blocks,
			)
		);

		wp_localize_script(
			'mbfg-block-editor-js',
			'mbfg_blocks_info',
			array(
				'blocks'               => FBFG_Config::get_block_attributes(),
				'category'             => 'mbfg',
				'ajax_url'             => admin_url( 'admin-ajax.php' ),
				'tablet_breakpoint'    => FBFG_TABLET_BREAKPOINT,
				'mobile_breakpoint'    => FBFG_MOBILE_BREAKPOINT,
				'image_sizes'          => FBFG_Helper::get_image_sizes(),
				'post_types'           => FBFG_Helper::get_post_types(),
				'all_taxonomy'         => FBFG_Helper::get_related_taxonomy(),
				'taxonomy_list'        => FBFG_Helper::get_taxonomy_list(),
				'mbfg_ajax_nonce'      => $mbfg_ajax_nonce,
				'mbfg_home_url'        => home_url(),
				'user_role'            => $this->get_user_role(),
				'mbfg_url'             => FBFG_URL,
				'mbfg_mime_type'       => FBFG_Helper::get_mime_type(),
				'mbfg_site_url'        => FBFG_URI,
				'enableConditions'     => apply_filters_deprecated( 'enable_block_condition', array( true ), '1.23.4', 'uag_enable_block_condition' ),
				'enableMasonryGallery' => apply_filters( 'uag_enable_masonry_gallery', true ),
			)
		);

		// To match the editor with frontend.
		// Scripts Dependency.
		FBFG_Scripts_Utils::enqueue_blocks_dependency_both();
		// Style.
		FBFG_Scripts_Utils::enqueue_blocks_styles();
		// RTL Styles.
		FBFG_Scripts_Utils::enqueue_blocks_rtl_styles();
	}

	/**
	 *  Get the User Roles
	 *
	 *  @since 1.0.0
	 */
	public function get_user_role() {

		global $wp_roles;

		$field_options = array();

		$role_lists = $wp_roles->get_names();

		$field_options[0] = array(
			'value' => '',
			'label' => __( 'None', 'map-block-for-gutenberg' ),
		);

		foreach ( $role_lists as $key => $role_list ) {
			$field_options[] = array(
				'value' => $key,
				'label' => $role_list,
			);
		}

		return $field_options;
	}
}

/**
 *  Prepare if class 'FBFG_Init_Blocks' exist.
 *  Kicking this off by calling 'get_instance()' method
 */
FBFG_Init_Blocks::get_instance();
