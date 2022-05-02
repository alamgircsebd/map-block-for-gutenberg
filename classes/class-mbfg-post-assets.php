<?php
/**
 * MBFG Post Base.
 *
 * @package MBFG
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class FBFG_Post_Assets.
 */
class FBFG_Post_Assets {

	/**
	 * Current Block List
	 *
	 * @since 1.0.0
	 * @var current_block_list
	 */
	public $current_block_list = array();

	/**
	 * MBFG Block Flag
	 *
	 * @since 1.0.0
	 * @var mbfg_flag
	 */
	public $mbfg_flag = false;

	/**
	 * MBFG FAQ Layout Flag
	 *
	 * @since 1.0.0
	 * @var mbfg_faq_layout
	 */
	public $mbfg_faq_layout = false;

	/**
	 * MBFG File Generation Flag
	 *
	 * @since 1.0.0
	 * @var file_generation
	 */
	public $file_generation = 'disabled';

	/**
	 * MBFG File Generation Flag
	 *
	 * @since 1.0.0
	 * @var file_generation
	 */
	public $is_allowed_assets_generation = false;

	/**
	 * MBFG File Generation Fallback Flag for CSS
	 *
	 * @since 1.0.0
	 * @var file_generation
	 */
	public $fallback_css = false;

	/**
	 * MBFG File Generation Fallback Flag for JS
	 *
	 * @since 1.0.0
	 * @var file_generation
	 */
	public $fallback_js = false;

	/**
	 * Enque Style and Script Variable
	 *
	 * @since 1.0.0
	 * @var instance
	 */
	public $assets_file_handler = array();

	/**
	 * Stylesheet
	 *
	 * @since 1.0.0
	 * @var stylesheet
	 */
	public $stylesheet = '';

	/**
	 * Script
	 *
	 * @since 1.0.0
	 * @var script
	 */
	public $script = '';

	/**
	 * Store Json variable
	 *
	 * @since 1.0.0
	 * @var instance
	 */
	public $icon_json;

	/**
	 * Page Blocks Variable
	 *
	 * @since 1.0.0
	 * @var instance
	 */
	public $page_blocks;

	/**
	 * Google fonts to enqueue
	 *
	 * @var array
	 */
	public $gfonts = array();

	/**
	 * Static CSS Added Array
	 *
	 * @since 1.0.0
	 * @var array
	 */
	public $static_css_blocks = array();

	/**
	 * Static CSS Added Array
	 *
	 * @since 1.0.0
	 * @var array
	 */
	public static $conditional_blocks_printed = false;

	/**
	 * Post ID
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $post_id;

	/**
	 * Preview
	 *
	 * @since 1.24.2
	 * @var preview
	 */
	public $preview = false;

	/**
	 * Constructor
	 *
	 * @param int $post_id Post ID.
	 */
	public function __construct( $post_id ) {

		$this->post_id = intval( $post_id );

		$this->preview = isset( $_GET['preview'] ); //phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( $this->preview ) {
			$this->file_generation              = 'disabled';
			$this->is_allowed_assets_generation = true;
		} else {
			$this->file_generation              = FBFG_Helper::$file_generation;
			$this->is_allowed_assets_generation = $this->allow_assets_generation();
		}

		if ( $this->is_allowed_assets_generation ) {
			global $post;
			$this_post = $this->preview ? $post : get_post( $this->post_id );
			$this->prepare_assets( $this_post );
		}
	}

	/**
	 * This function determines wether to generate new assets or not.
	 *
	 * @since 1.0.0
	 */
	public function allow_assets_generation() {

		$page_assets     = get_post_meta( $this->post_id, '_mbfg_page_assets', true );
		$version_updated = false;
		$css_asset_info  = array();
		$js_asset_info   = array();

		if ( empty( $page_assets ) || empty( $page_assets['mbfg_version'] ) ) {
			return true;
		}

		if ( FBFG_ASSET_VER !== $page_assets['mbfg_version'] ) {
			$version_updated = true;
		}

		if ( 'enabled' === $this->file_generation ) {

			$css_file_name = get_post_meta( $this->post_id, '_mbfg_css_file_name', true );
			$js_file_name  = get_post_meta( $this->post_id, '_mbfg_js_file_name', true );

			if ( ! empty( $css_file_name ) ) {
				$css_asset_info = FBFG_Scripts_Utils::get_asset_info( 'css', $this->post_id );
				$css_file_path  = $css_asset_info['css'];
			}

			if ( ! empty( $js_file_name ) ) {
				$js_asset_info = FBFG_Scripts_Utils::get_asset_info( 'js', $this->post_id );
				$js_file_path  = $js_asset_info['js'];
			}

			if ( $version_updated ) {
				$mbfg_filesystem = mbfg_filesystem();

				if ( ! empty( $css_file_path ) ) {
					$mbfg_filesystem->delete( $css_file_path );
				}

				if ( ! empty( $js_file_path ) ) {
					$mbfg_filesystem->delete( $js_file_path );
				}

				// Delete keys.
				delete_post_meta( $this->post_id, '_mbfg_css_file_name' );
				delete_post_meta( $this->post_id, '_mbfg_js_file_name' );
			}

			if ( empty( $css_file_path ) || ! file_exists( $css_file_path ) ) {
				return true;
			}

			if ( ! empty( $js_file_path ) && ! file_exists( $js_file_path ) ) {
				return true;
			}
		}

		// If version is updated, return true.
		if ( $version_updated ) {
			// Delete cached meta.
			delete_post_meta( $this->post_id, '_mbfg_page_assets' );
			return true;
		}

		// Set required varibled from stored data.
		$this->current_block_list  = $page_assets['current_block_list'];
		$this->mbfg_flag            = $page_assets['mbfg_flag'];
		$this->stylesheet          = $page_assets['css'];
		$this->script              = $page_assets['js'];
		$this->gfonts              = $page_assets['gfonts'];
		$this->mbfg_faq_layout      = $page_assets['mbfg_faq_layout'];
		$this->assets_file_handler = array_merge( $css_asset_info, $js_asset_info );

		return false;
	}

	/**
	 * Enqueue all page assets.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {

		// Global Required assets.
		if ( has_blocks( $this->post_id ) ) {
			/* Print conditional css for all blocks */
			add_action( 'wp_head', array( $this, 'print_conditional_css' ), 80 );
		}

		// MBFG Flag specific.
		if ( $this->is_allowed_assets_generation ) {
			$this->generate_assets();
			$this->generate_asset_files();
		}

		if ( $this->mbfg_flag ) {

			// Register Assets for Frontend & Enqueue for Editor.
			FBFG_Scripts_Utils::enqueue_blocks_dependency_both();

			// Enqueue all dependency assets.
			$this->enqueue_blocks_dependency_frontend();

			// RTL Styles Suppport.
			FBFG_Scripts_Utils::enqueue_blocks_rtl_styles();

			// Print google fonts.
			add_action( 'wp_head', array( $this, 'print_google_fonts' ), 120 );

			if ( 'enabled' === $this->file_generation ) {
				// Enqueue File Generation Assets Files.
				$this->enqueue_file_generation_assets();
			}

			// Print Dynamic CSS.
			if ( 'disabled' === $this->file_generation || $this->fallback_css ) {
				add_action( 'wp_head', array( $this, 'print_stylesheet' ), 80 );
			}
			// Print Dynamic JS.
			if ( 'disabled' === $this->file_generation || $this->fallback_js ) {
				add_action( 'wp_footer', array( $this, 'print_script' ), 1000 );
			}
		}
	}


	/**
	 * This function updates the Page assets in the Page Meta Key.
	 *
	 * @since 1.0.0
	 */
	public function update_page_assets() {

		if ( $this->preview ) {
			return;
		}

		$meta_array = array(
			'css'                => wp_slash( $this->stylesheet ),
			'js'                 => $this->script,
			'current_block_list' => $this->current_block_list,
			'mbfg_flag'           => $this->mbfg_flag,
			'mbfg_version'        => FBFG_ASSET_VER,
			'gfonts'             => $this->gfonts,
			'mbfg_faq_layout'     => $this->mbfg_faq_layout,
		);

		update_post_meta( $this->post_id, '_mbfg_page_assets', $meta_array );
	}
	/**
	 * This is the action where we create dynamic asset files.
	 * CSS Path : uploads/mbfg-plugin/mbfg-style-{post_id}-{timestamp}.css
	 * JS Path : uploads/mbfg-plugin/mbfg-script-{post_id}-{timestamp}.js
	 *
	 * @since 1.0.0
	 */
	public function generate_asset_files() {

		if ( 'enabled' === $this->file_generation ) {
			$this->file_write( $this->stylesheet, 'css', $this->post_id );
			$this->file_write( $this->script, 'js', $this->post_id );
		}

		$this->update_page_assets();
	}

	/**
	 * Enqueue Gutenberg block assets for both frontend + backend.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_blocks_dependency_frontend() {

		$block_list_for_assets = $this->current_block_list;

		$blocks = FBFG_Config::get_block_attributes();

		foreach ( $block_list_for_assets as $key => $curr_block_name ) {

			$js_assets = ( isset( $blocks[ $curr_block_name ]['js_assets'] ) ) ? $blocks[ $curr_block_name ]['js_assets'] : array();

			$css_assets = ( isset( $blocks[ $curr_block_name ]['css_assets'] ) ) ? $blocks[ $curr_block_name ]['css_assets'] : array();

			foreach ( $js_assets as $asset_handle => $val ) {
				// Scripts.
				if ( 'mbfg-faq-js' === $val ) {
					if ( $this->mbfg_faq_layout ) {
						wp_enqueue_script( 'mbfg-faq-js' );
					}
				} else {

					wp_enqueue_script( $val );
				}
			}

			foreach ( $css_assets as $asset_handle => $val ) {
				// Styles.
				wp_enqueue_style( $val );
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

		$mbfg_forms_ajax_nonce = wp_create_nonce( 'mbfg_forms_ajax_nonce' );
		wp_localize_script(
			'mbfg-forms-js',
			'mbfg_forms_data',
			array(
				'ajax_url'              => admin_url( 'admin-ajax.php' ),
				'mbfg_forms_ajax_nonce' => $mbfg_forms_ajax_nonce,
			)
		);
	}

	/**
	 * Enqueue File Generation Files.
	 */
	public function enqueue_file_generation_assets() {

		$file_handler = $this->assets_file_handler;

		if ( isset( $file_handler['css_url'] ) ) {
			wp_enqueue_style( 'mbfg-style-' . $this->post_id, $file_handler['css_url'], array(), FBFG_VER, 'all' );
		} else {
			$this->fallback_css = true;
		}
		if ( isset( $file_handler['js_url'] ) ) {
			wp_enqueue_script( 'mbfg-script-' . $this->post_id, $file_handler['js_url'], array(), FBFG_VER, true );
		} else {
			$this->fallback_js = true;
		}
	}
	/**
	 * Print the Script in footer.
	 */
	public function print_script() {

		if ( empty( $this->script ) ) {
			return;
		}

		echo '<script type="text/javascript" id="mbfg-script-frontend-' . $this->post_id . '">' . $this->script . '</script>'; //phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Print the Stylesheet in header.
	 */
	public function print_stylesheet() {

		if ( empty( $this->stylesheet ) ) {
			return;
		}

		echo '<style id="mbfg-style-frontend-' . $this->post_id . '">' . $this->stylesheet . '</style>'; //phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Print Conditional blocks css.
	 */
	public function print_conditional_css() {

		if ( self::$conditional_blocks_printed ) {
			return;
		}

		$conditional_block_css = FBFG_Block_Helper::get_condition_block_css();

		if ( in_array( 'mbfg/masonry-gallery', $this->current_block_list, true ) ) {
			$conditional_block_css .= FBFG_Block_Helper::get_masonry_gallery_css();
		}

		echo '<style id="mbfg-style-conditional-extension">' . $conditional_block_css . '</style>'; //phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped

		self::$conditional_blocks_printed = true;

	}


	/**
	 * Load the front end Google Fonts.
	 */
	public function print_google_fonts() {

		if ( empty( $this->gfonts ) ) {
			return;
		}

		$show_google_fonts = apply_filters( 'mbfg_blocks_show_google_fonts', true );
		if ( ! $show_google_fonts ) {
			return;
		}
		$link    = '';
		$subsets = array();
		foreach ( $this->gfonts as $key => $gfont_values ) {
			if ( ! empty( $link ) ) {
				$link .= '%7C'; // Append a new font to the string.
			}
			$link .= $gfont_values['fontfamily'];
			if ( ! empty( $gfont_values['fontvariants'] ) ) {
				$link .= ':';
				$link .= implode( ',', $gfont_values['fontvariants'] );
			}
			if ( ! empty( $gfont_values['fontsubsets'] ) ) {
				foreach ( $gfont_values['fontsubsets'] as $subset ) {
					if ( ! in_array( $subset, $subsets, true ) ) {
						array_push( $subsets, $subset );
					}
				}
			}
		}
		if ( ! empty( $subsets ) ) {
			$link .= '&amp;subset=' . implode( ',', $subsets );
		}
		if ( isset( $link ) && ! empty( $link ) ) {
			echo '<link href="//fonts.googleapis.com/css?family=' . esc_attr( str_replace( '|', '%7C', $link ) ) . '" rel="stylesheet">'; //phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet
		}
	}

	/**
	 * Generates CSS recurrsively.
	 *
	 * @param object $block The block object.
	 * @since 1.0.0
	 */
	public function get_block_css_and_js( $block ) {

		$block = (array) $block;

		$name     = $block['blockName'];
		$css      = array();
		$js       = '';
		$block_id = '';

		if ( ! isset( $name ) ) {
			return array(
				'css' => array(),
				'js'  => '',
			);
		}

		if ( isset( $block['attrs'] ) && is_array( $block['attrs'] ) ) {
			/**
			 * Filters the block attributes for CSS and JS generation.
			 *
			 * @param array  $block_attributes The block attributes to be filtered.
			 * @param string $name             The block name.
			 */
			$blockattr = apply_filters( 'mbfg_block_attributes_for_css_and_js', $block['attrs'], $name );
			if ( isset( $blockattr['block_id'] ) ) {
				$block_id = $blockattr['block_id'];
			}
		}

		$this->current_block_list[] = $name;

		if ( 'core/gallery' === $name && isset( $block['attrs']['masonry'] ) && true === $block['attrs']['masonry'] ) {
			$this->current_block_list[] = 'mbfg/masonry-gallery';
			$this->mbfg_flag             = true;
			$css                       += FBFG_Block_Helper::get_gallery_css( $blockattr, $block_id );
		}

		if ( strpos( $name, 'mbfg/' ) !== false ) {
			$this->mbfg_flag = true;
		}

		// Add static css here.
		$block_css_arr = FBFG_Config::get_block_assets_css();

		if ( isset( $block_css_arr[ $name ] ) && ! in_array( $block_css_arr[ $name ]['name'], $this->static_css_blocks, true ) ) {
			$common_css = array(
				'common' => $this->get_block_static_css( $block_css_arr[ $name ]['name'] ),
			);
			$css       += $common_css;
		}

		switch ( $name ) {
			case 'mbfg/forms':
				$css += FBFG_Block_Helper::get_forms_css( $blockattr, $block_id );
				$js  .= FBFG_Block_JS::get_forms_js( $blockattr, $block_id );
				FBFG_Block_JS::blocks_forms_gfont( $blockattr );
				break;

			default:
				// Nothing to do here.
				break;
		}

		if ( isset( $block['innerBlocks'] ) ) {
			foreach ( $block['innerBlocks'] as $j => $inner_block ) {
				if ( 'core/block' === $inner_block['blockName'] ) {
					$id = ( isset( $inner_block['attrs']['ref'] ) ) ? $inner_block['attrs']['ref'] : 0;

					if ( $id ) {
						$content = get_post_field( 'post_content', $id );

						$reusable_blocks = $this->parse_blocks( $content );

						$assets = $this->get_blocks_assets( $reusable_blocks );

						$this->stylesheet .= $assets['css'];
						$this->script     .= $assets['js'];
					}
				} else {
					// Get CSS for the Block.
					$inner_assets    = $this->get_block_css_and_js( $inner_block );
					$inner_block_css = $inner_assets['css'];

					$css_common  = ( isset( $css['common'] ) ? $css['common'] : '' );
					$css_desktop = ( isset( $css['desktop'] ) ? $css['desktop'] : '' );
					$css_tablet  = ( isset( $css['tablet'] ) ? $css['tablet'] : '' );
					$css_mobile  = ( isset( $css['mobile'] ) ? $css['mobile'] : '' );

					if ( isset( $inner_block_css['common'] ) ) {
						$css['common'] = $css_common . $inner_block_css['common'];
					}

					if ( isset( $inner_block_css['desktop'] ) ) {
						$css['desktop'] = $css_desktop . $inner_block_css['desktop'];
						$css['tablet']  = $css_tablet . $inner_block_css['tablet'];
						$css['mobile']  = $css_mobile . $inner_block_css['mobile'];
					}

					$js .= $inner_assets['js'];
				}
			}
		}

		$this->current_block_list = array_unique( $this->current_block_list );

		return array(
			'css' => $css,
			'js'  => $js,
		);

	}

	/**
	 * Generates stylesheet and appends in head tag.
	 *
	 * @since 1.0.0
	 */
	public function generate_assets() {

		/* Finalize prepared assets and store in static variable */
		global $content_width;

		$this->stylesheet = str_replace( '#CONTENT_WIDTH#', $content_width . 'px', $this->stylesheet );

		if ( '' !== $this->script ) {
			$this->script = 'document.addEventListener("DOMContentLoaded", function(){ ' . $this->script . ' })';
		}

		/* Update page assets */
		$this->update_page_assets();
	}

	/**
	 * Generates stylesheet in loop.
	 *
	 * @param object $this_post Current Post Object.
	 * @since 1.0.0
	 */
	public function prepare_assets( $this_post ) {

		if ( empty( $this_post ) || empty( $this_post->ID ) ) {
			return;
		}

		if ( has_blocks( $this_post->ID ) && isset( $this_post->post_content ) ) {

			$blocks            = $this->parse_blocks( $this_post->post_content );
			$this->page_blocks = $blocks;

			if ( ! is_array( $blocks ) || empty( $blocks ) ) {
				return;
			}

			$assets = $this->get_blocks_assets( $blocks );

			$this->stylesheet .= $assets['css'];
			$this->script     .= $assets['js'];

			// Update fonts.
			$this->gfonts = array_merge( $this->gfonts, FBFG_Helper::$gfonts );
		}
	}

	/**
	 * Parse Guten Block.
	 *
	 * @param string $content the content string.
	 * @since 1.0.0
	 */
	public function parse_blocks( $content ) {

		global $wp_version;

		return ( version_compare( $wp_version, '5', '>=' ) ) ? parse_blocks( $content ) : gutenberg_parse_blocks( $content );
	}

	/**
	 * Generates assets for all blocks including reusable blocks.
	 *
	 * @param array $blocks Blocks array.
	 * @since 1.0.0
	 */
	public function get_blocks_assets( $blocks ) {

		$desktop = '';
		$tablet  = '';
		$mobile  = '';

		$tab_styling_css = '';
		$mob_styling_css = '';

		$js = '';

		foreach ( $blocks as $i => $block ) {

			if ( is_array( $block ) ) {

				if ( '' === $block['blockName'] ) {
					continue;
				}

				if ( 'core/block' === $block['blockName'] ) {
					$id = ( isset( $block['attrs']['ref'] ) ) ? $block['attrs']['ref'] : 0;

					if ( $id ) {
						$content = get_post_field( 'post_content', $id );

						$reusable_blocks = $this->parse_blocks( $content );

						$assets = $this->get_blocks_assets( $reusable_blocks );

						$this->stylesheet .= $assets['css'];
						$this->script     .= $assets['js'];

					}
				} else {
					// Add your block specif css here.
					$block_assets = $this->get_block_css_and_js( $block );
					// Get CSS for the Block.
					$css = $block_assets['css'];

					if ( ! empty( $css['common'] ) ) {
						$desktop .= $css['common'];
					}

					if ( isset( $css['desktop'] ) ) {
						$desktop .= $css['desktop'];
						$tablet  .= $css['tablet'];
						$mobile  .= $css['mobile'];
					}
					$js .= $block_assets['js'];
				}
			}
		}

		if ( ! empty( $tablet ) ) {
			$tab_styling_css .= '@media only screen and (max-width: ' . FBFG_TABLET_BREAKPOINT . 'px) {';
			$tab_styling_css .= $tablet;
			$tab_styling_css .= '}';
		}

		if ( ! empty( $mobile ) ) {
			$mob_styling_css .= '@media only screen and (max-width: ' . FBFG_MOBILE_BREAKPOINT . 'px) {';
			$mob_styling_css .= $mobile;
			$mob_styling_css .= '}';
		}

		return array(
			'css' => $desktop . $tab_styling_css . $mob_styling_css,
			'js'  => $js,
		);
	}

	/**
	 * Creates a new file for Dynamic CSS/JS.
	 *
	 * @param  string $file_data The data that needs to be copied into the created file.
	 * @param  string $type Type of file - CSS/JS.
	 * @param  string $file_state Wether File is new or old.
	 * @param  string $old_file_name Old file name timestamp.
	 * @since 1.0.0
	 * @return boolean true/false
	 */
	public function create_file( $file_data, $type, $file_state = 'new', $old_file_name = '' ) {

		$date          = new DateTime();
		$new_timestamp = $date->getTimestamp();
		$uploads_dir   = FBFG_Helper::get_upload_dir();
		$file_system   = mbfg_filesystem();

		// Example 'mbfg-css-15-1645698679.css'.
		$file_name = 'mbfg-' . $type . '-' . $this->post_id . '-' . $new_timestamp . '.' . $type;

		if ( 'old' === $file_state ) {
			$file_name = $old_file_name;
		}

		// Create a new file.
		$result = $file_system->put_contents( $uploads_dir['path'] . $file_name, $file_data, FS_CHMOD_FILE );

		if ( $result ) {
			// Update meta with current timestamp.
			update_post_meta( $this->post_id, '_mbfg_' . $type . '_file_name', $file_name );
		}

		return $result;
	}

	/**
	 * Creates css and js files.
	 *
	 * @param  var $file_data    Gets the CSS\JS for the current Page.
	 * @param  var $type    Gets the CSS\JS type.
	 * @param  var $post_id Post ID.
	 * @since 1.0.0
	 */
	public function file_write( $file_data, $type = 'css', $post_id = '' ) {

		if ( ! $this->post_id ) {
			return false;
		}

		$file_system = mbfg_filesystem();

		// Get timestamp - Already saved OR new one.
		$file_name   = get_post_meta( $this->post_id, '_mbfg_' . $type . '_file_name', true );
		$file_name   = empty( $file_name ) ? '' : $file_name;
		$assets_info = FBFG_Scripts_Utils::get_asset_info( $type, $this->post_id );
		$file_path   = $assets_info[ $type ];

		if ( '' === $file_data ) {
			/**
			 * This is when the generated CSS/JS is blank.
			 * This means this page does not use MBFG block.
			 * In this scenario we need to delete the existing file.
			 * This will ensure there are no extra files added for user.
			*/

			if ( ! empty( $file_name ) && file_exists( $file_path ) ) {
				// Delete old file.
				wp_delete_file( $file_path );
			}

			return true;
		}

		/**
		 * Timestamp present but file does not exists.
		 * This is the case where somehow the files are delete or not created in first place.
		 * Here we attempt to create them again.
		 */
		if ( ! $file_system->exists( $file_path ) && '' !== $file_name ) {

			$did_create = $this->create_file( $file_data, $type, 'old', $file_name );

			if ( $did_create ) {
				$this->assets_file_handler = array_merge( $this->assets_file_handler, $assets_info );
			}

			return $did_create;
		}

		/**
		 * Need to create new assets.
		 * No such assets present for this current page.
		 */
		if ( '' === $file_name ) {

			// Create a new file.
			$did_create = $this->create_file( $file_data, $type );

			if ( $did_create ) {
				$new_assets_info           = FBFG_Scripts_Utils::get_asset_info( $type, $this->post_id );
				$this->assets_file_handler = array_merge( $this->assets_file_handler, $new_assets_info );
			}

			return $did_create;

		}

		/**
		 * File already exists.
		 * Need to match the content.
		 * If new content is present we update the current assets.
		 */
		if ( file_exists( $file_path ) ) {

			$old_data = $file_system->get_contents( $file_path );

			if ( $old_data !== $file_data ) {

				// Delete old file.
				wp_delete_file( $file_path );

				// Create a new file.
				$did_create = $this->create_file( $file_data, $type );

				if ( $did_create ) {
					$new_assets_info           = FBFG_Scripts_Utils::get_asset_info( $type, $this->post_id );
					$this->assets_file_handler = array_merge( $this->assets_file_handler, $new_assets_info );
				}

				return $did_create;
			}
		}

		$this->assets_file_handler = array_merge( $this->assets_file_handler, $assets_info );

		return true;
	}

	/**
	 * Get Static CSS of Block.
	 *
	 * @param string $block_name Block Name.
	 *
	 * @return string Static CSS.
	 * @since 1.0.0
	 */
	public function get_block_static_css( $block_name ) {

		$css = '';

		$block_static_css_path = FBFG_DIR . 'assets/css/blocks/' . $block_name . '.css';

		if ( file_exists( $block_static_css_path ) ) {

			$file_system = mbfg_filesystem();

			$css = $file_system->get_contents( $block_static_css_path );
		}

		array_push( $this->static_css_blocks, $block_name );

		return $css;
	}
}
