<?php
/**
 * MBFG Helper.
 *
 * @package MBFG
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'FBFG_Helper' ) ) {

	/**
	 * Class FBFG_Helper.
	 */
	final class FBFG_Helper {


		/**
		 * Member Variable
		 *
		 * @since 1.0.0
		 * @var instance
		 */
		private static $instance;

		/**
		 * Member Variable
		 *
		 * @since 1.0.0
		 * @var instance
		 */
		public static $block_list;

		/**
		 * MBFG File Generation Flag
		 *
		 * @since 1.0.0
		 * @var file_generation
		 */
		public static $file_generation = 'disabled';

		/**
		 * Store Json variable
		 *
		 * @since 1.0.0
		 * @var instance
		 */
		public static $icon_json;

		/**
		 * Google fonts to enqueue
		 *
		 * @var array
		 */
		public static $gfonts = array();

		/**
		 * Current Block List
		 *
		 * @since 1.0.0
		 * @var current_block_list
		 */
		public static $current_block_list = array();

		/**
		 * MBFG Block Flag
		 *
		 * @since 1.0.0
		 * @var mbfg_flag
		 * @deprecated 1.23.0
		 */
		public static $mbfg_flag = false;

		/**
		 * Page Blocks Variable
		 *
		 * @since 1.0.0
		 * @var page_blocks
		 */
		public static $page_blocks;

		/**
		 * Stylesheet
		 *
		 * @since 1.0.0
		 * @var stylesheet
		 */
		public static $stylesheet = '';

		/**
		 * Script
		 *
		 * @since 1.0.0
		 * @var script
		 */
		public static $script = '';

		/**
		 * MBFG FAQ Layout Flag
		 *
		 * @since 1.0.0
		 * @var mbfg_faq_layout
		 */
		public static $mbfg_faq_layout = false;

		/**
		 * MBFG TOC Flag
		 *
		 * @since 1.0.0
		 * @var table_of_contents_flag
		 */
		public static $table_of_contents_flag = false;

		/**
		 *  Initiator
		 *
		 * @since 1.0.0
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
			require FBFG_DIR . 'classes/class-mbfg-config.php';
			require FBFG_DIR . 'classes/class-mbfg-block-helper.php';
			require FBFG_DIR . 'classes/class-mbfg-block-js.php';

			self::$block_list      = FBFG_Config::get_block_attributes();
			self::$file_generation = self::allow_file_generation();
		}

		/**
		 * Parse CSS into correct CSS syntax.
		 *
		 * @param array  $selectors The block selectors.
		 * @param string $id The selector ID.
		 * @since 1.0.0
		 */
		public static function generate_css( $selectors, $id ) {
			$styling_css = '';

			if ( empty( $selectors ) ) {
				return '';
			}

			foreach ( $selectors as $key => $value ) {

				$css = '';

				foreach ( $value as $j => $val ) {

					if ( 'font-family' === $j && 'Default' === $val ) {
						continue;
					}

					if ( ! empty( $val ) || 0 === $val ) {
						if ( 'font-family' === $j ) {
							$css .= $j . ': "' . $val . '";';
						} else {
							$css .= $j . ': ' . $val . ';';
						}
					}
				}

				if ( ! empty( $css ) ) {
					$styling_css     .= $id;
					$styling_css     .= $key . '{';
						$styling_css .= $css . '}';
				}
			}

			return $styling_css;
		}

		/**
		 * Get CSS value
		 *
		 * Syntax:
		 *
		 *  get_css_value( VALUE, UNIT );
		 *
		 * E.g.
		 *
		 *  get_css_value( VALUE, 'em' );
		 *
		 * @param string $value  CSS value.
		 * @param string $unit  CSS unit.
		 * @since 1.0.0
		 */
		public static function get_css_value( $value = '', $unit = '' ) {
			if ( '' == $value ) { // phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
				return $value;
			}

			$css_val = '';

			if ( ! empty( $value ) ) {
				$css_val = esc_attr( $value ) . $unit;
			}

			return $css_val;
		}


		/**
		 * Adds Google fonts all blocks.
		 *
		 * @param array $load_google_font the blocks attr.
		 * @param array $font_family the blocks attr.
		 * @param array $font_weight the blocks attr.
		 * @param array $font_subset the blocks attr.
		 */
		public static function blocks_google_font( $load_google_font, $font_family, $font_weight, $font_subset ) {
			if ( true === $load_google_font ) {
				if ( ! array_key_exists( $font_family, self::$gfonts ) ) {
					$add_font                     = array(
						'fontfamily'   => $font_family,
						'fontvariants' => ( isset( $font_weight ) && ! empty( $font_weight ) ? array( $font_weight ) : array() ),
						'fontsubsets'  => ( isset( $font_subset ) && ! empty( $font_subset ) ? array( $font_subset ) : array() ),
					);
					self::$gfonts[ $font_family ] = $add_font;
				} else {
					if ( isset( $font_weight ) && ! empty( $font_weight ) && ! in_array( $font_weight, self::$gfonts[ $font_family ]['fontvariants'], true ) ) {
						array_push( self::$gfonts[ $font_family ]['fontvariants'], $font_weight );
					}
					if ( isset( $font_subset ) && ! empty( $font_subset ) && ! in_array( $font_subset, self::$gfonts[ $font_family ]['fontsubsets'], true ) ) {
						array_push( self::$gfonts[ $font_family ]['fontsubsets'], $font_subset );
					}
				}
			}
		}

		/**
		 * Get Json Data.
		 *
		 * @since 1.0.0
		 * @return Array
		 */
		public static function backend_load_font_awesome_icons() {
			$json_file = FBFG_DIR . 'blocks-config/mbfg-controls/FBFGIcon.json';
			if ( ! file_exists( $json_file ) ) {
				return array();
			}

			// Function has already run.
			if ( null !== self::$icon_json ) {
				return self::$icon_json;
			}

			$str             = mbfg_filesystem()->get_contents( $json_file );
			self::$icon_json = json_decode( $str, true );
			return self::$icon_json;
		}

		/**
		 *  Check MIME Type
		 *
		 *  @since 1.20.0
		 */
		public static function get_mime_type() {
			$allowed_types = get_allowed_mime_types();

			return ( array_key_exists( 'json', $allowed_types ) ) ? true : false;

		}

		/**
		 * Returns Query.
		 *
		 * @param array  $attributes The block attributes.
		 * @param string $block_type The Block Type.
		 * @since 1.0.0
		 */
		public static function get_query( $attributes, $block_type ) {
			// Block type is grid/masonry/carousel/timeline.
			$query_args = array(
				'posts_per_page'      => ( isset( $attributes['postsToShow'] ) ) ? $attributes['postsToShow'] : 6,
				'post_status'         => 'publish',
				'post_type'           => ( isset( $attributes['postType'] ) ) ? $attributes['postType'] : 'post',
				'order'               => ( isset( $attributes['order'] ) ) ? $attributes['order'] : 'desc',
				'orderby'             => ( isset( $attributes['orderBy'] ) ) ? $attributes['orderBy'] : 'date',
				'ignore_sticky_posts' => 1,
				'paged'               => 1,
			);

			if ( $attributes['excludeCurrentPost'] ) {
				$query_args['post__not_in'] = array( get_the_ID() );
			}

			if ( isset( $attributes['categories'] ) && '' !== $attributes['categories'] ) {
				$query_args['tax_query'][] = array(
					'taxonomy' => ( isset( $attributes['taxonomyType'] ) ) ? $attributes['taxonomyType'] : 'category',
					'field'    => 'id',
					'terms'    => $attributes['categories'],
					'operator' => 'IN',
				);
			}

			if ( 'grid' === $block_type && isset( $attributes['postPagination'] ) && true === $attributes['postPagination'] ) {

				if ( get_query_var( 'paged' ) ) {

					$paged = get_query_var( 'paged' );

				} elseif ( get_query_var( 'page' ) ) {

					$paged = get_query_var( 'page' );

				} else {

					$paged = 1;

				}
				$query_args['posts_per_page'] = $attributes['postsToShow'];
				$query_args['paged']          = $paged;

			}

			if ( 'masonry' === $block_type && isset( $attributes['paginationType'] ) && 'none' !== $attributes['paginationType'] && isset( $attributes['paged'] ) ) {

				$query_args['paged'] = $attributes['paged'];

			}

			$query_args = apply_filters( "mbfg_post_query_args_{$block_type}", $query_args, $attributes );

			return new WP_Query( $query_args );
		}

		/**
		 * Get size information for all currently-registered image sizes.
		 *
		 * @global $_wp_additional_image_sizes
		 * @uses   get_intermediate_image_sizes()
		 * @link   https://codex.wordpress.org/Function_Reference/get_intermediate_image_sizes
		 * @since 1.0.0
		 * @return array $sizes Data for all currently-registered image sizes.
		 */
		public static function get_image_sizes() {
			global $_wp_additional_image_sizes;

			$sizes       = get_intermediate_image_sizes();
			$image_sizes = array();

			$image_sizes[] = array(
				'value' => 'full',
				'label' => esc_html__( 'Full', 'map-block-for-gutenberg' ),
			);

			foreach ( $sizes as $size ) {
				if ( in_array( $size, array( 'thumbnail', 'medium', 'medium_large', 'large' ), true ) ) {
					$image_sizes[] = array(
						'value' => $size,
						'label' => ucwords( trim( str_replace( array( '-', '_' ), array( ' ', ' ' ), $size ) ) ),
					);
				} else {
					$image_sizes[] = array(
						'value' => $size,
						'label' => sprintf(
							'%1$s (%2$sx%3$s)',
							ucwords( trim( str_replace( array( '-', '_' ), array( ' ', ' ' ), $size ) ) ),
							$_wp_additional_image_sizes[ $size ]['width'],
							$_wp_additional_image_sizes[ $size ]['height']
						),
					);
				}
			}

			$image_sizes = apply_filters( 'mbfg_post_featured_image_sizes', $image_sizes );

			return $image_sizes;
		}

		/**
		 * Get Post Types.
		 *
		 * @since 1.0.0
		 * @access public
		 */
		public static function get_post_types() {
			$post_types = get_post_types(
				array(
					'public'       => true,
					'show_in_rest' => true,
				),
				'objects'
			);

			$options = array();

			foreach ( $post_types as $post_type ) {
				if ( 'product' === $post_type->name ) {
					continue;
				}

				if ( 'attachment' === $post_type->name ) {
					continue;
				}

				$options[] = array(
					'value' => $post_type->name,
					'label' => $post_type->label,
				);
			}

			return apply_filters( 'mbfg_loop_post_types', $options );
		}

		/**
		 * Get all taxonomies.
		 *
		 * @since 1.0.0
		 * @access public
		 */
		public static function get_related_taxonomy() {
			$post_types = self::get_post_types();

			$return_array = array();

			foreach ( $post_types as $key => $value ) {
				$post_type = $value['value'];

				$taxonomies = get_object_taxonomies( $post_type, 'objects' );
				$data       = array();

				foreach ( $taxonomies as $tax_slug => $tax ) {
					if ( ! $tax->public || ! $tax->show_ui || ! $tax->show_in_rest ) {
						continue;
					}

					$data[ $tax_slug ] = $tax;

					$terms = get_terms( $tax_slug );

					$related_tax = array();

					if ( ! empty( $terms ) ) {
						foreach ( $terms as $t_index => $t_obj ) {
							$related_tax[] = array(
								'id'    => $t_obj->term_id,
								'name'  => $t_obj->name,
								'child' => get_term_children( $t_obj->term_id, $tax_slug ),
							);
						}
						$return_array[ $post_type ]['terms'][ $tax_slug ] = $related_tax;
					}
				}

				$return_array[ $post_type ]['taxonomy'] = $data;

			}

			return apply_filters( 'mbfg_post_loop_taxonomies', $return_array );
		}

		/**
		 * Get all taxonomies list.
		 *
		 * @since 1.0.0
		 * @access public
		 */
		public static function get_taxonomy_list() {
			$post_types   = self::get_post_types();
			$return_array = array();

			foreach ( $post_types as $key => $value ) {
				$post_type = $value['value'];

				$taxonomies = get_object_taxonomies( $post_type, 'objects' );
				$data       = array();

				$get_singular_name = get_post_type_object( $post_type );
				foreach ( $taxonomies as $tax_slug => $tax ) {
					if ( ! $tax->public || ! $tax->show_ui || ! $tax->show_in_rest ) {
						continue;
					}

					$data[ $tax_slug ] = $tax;

					$terms = get_terms( $tax_slug );

					$related_tax_terms = array();

					if ( ! empty( $terms ) ) {
						foreach ( $terms as $t_index => $t_obj ) {
							$related_tax_terms[] = array(
								'id'            => $t_obj->term_id,
								'name'          => $t_obj->name,
								'count'         => $t_obj->count,
								'link'          => get_term_link( $t_obj->term_id ),
								'singular_name' => $get_singular_name->labels->singular_name,
							);
						}

						$return_array[ $post_type ]['terms'][ $tax_slug ] = $related_tax_terms;
					}

					$newcategoriesList = get_terms(
						$tax_slug,
						array(
							'hide_empty' => true,
							'parent'     => 0,
						)
					);

					$related_tax = array();

					if ( ! empty( $newcategoriesList ) ) {
						foreach ( $newcategoriesList as $t_index => $t_obj ) {
							$child_arg     = array(
								'hide_empty' => true,
								'parent'     => $t_obj->term_id,
							);
							$child_cat     = get_terms( $tax_slug, $child_arg );
							$child_cat_arr = $child_cat ? $child_cat : null;
							$related_tax[] = array(
								'id'            => $t_obj->term_id,
								'name'          => $t_obj->name,
								'count'         => $t_obj->count,
								'link'          => get_term_link( $t_obj->term_id ),
								'singular_name' => $get_singular_name->labels->singular_name,
								'children'      => $child_cat_arr,
							);

						}

						$return_array[ $post_type ]['without_empty_taxonomy'][ $tax_slug ] = $related_tax;

					}

					$newcategoriesList_empty_tax = get_terms(
						$tax_slug,
						array(
							'hide_empty' => false,
							'parent'     => 0,
						)
					);

					$related_tax_empty_tax = array();

					if ( ! empty( $newcategoriesList_empty_tax ) ) {
						foreach ( $newcategoriesList_empty_tax as $t_index => $t_obj ) {
							$child_arg_empty_tax     = array(
								'hide_empty' => false,
								'parent'     => $t_obj->term_id,
							);
							$child_cat_empty_tax     = get_terms( $tax_slug, $child_arg_empty_tax );
							$child_cat_empty_tax_arr = $child_cat_empty_tax ? $child_cat_empty_tax : null;
							$related_tax_empty_tax[] = array(
								'id'            => $t_obj->term_id,
								'name'          => $t_obj->name,
								'count'         => $t_obj->count,
								'link'          => get_term_link( $t_obj->term_id ),
								'singular_name' => $get_singular_name->labels->singular_name,
								'children'      => $child_cat_empty_tax_arr,
							);
						}

						$return_array[ $post_type ]['with_empty_taxonomy'][ $tax_slug ] = $related_tax_empty_tax;

					}
				}
				$return_array[ $post_type ]['taxonomy'] = $data;

			}

			return apply_filters( 'mbfg_taxonomies_list', $return_array );
		}

		/**
		 *  Get - RGBA Color
		 *
		 *  Get HEX color and return RGBA. Default return RGB color.
		 *
		 * @param  var   $color      Gets the color value.
		 * @param  var   $opacity    Gets the opacity value.
		 * @param  array $is_array Gets an array of the value.
		 * @since 1.0.0
		 */
		public static function hex2rgba( $color, $opacity = false, $is_array = false ) {
			$default = $color;

			// Return default if no color provided.
			if ( empty( $color ) ) {
				return $default;
			}

			// Sanitize $color if "#" is provided.
			if ( '#' === $color[0] ) {
				$color = substr( $color, 1 );
			}

			// Check if color has 6 or 3 characters and get values.
			if ( strlen( $color ) === 6 ) {
					$hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
			} elseif ( strlen( $color ) === 3 ) {
					$hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
			} else {
					return $default;
			}

			// Convert hexadec to rgb.
			$rgb = array_map( 'hexdec', $hex );

			// Check if opacity is set(rgba or rgb).
			if ( false !== $opacity && '' !== $opacity ) {
				if ( abs( $opacity ) >= 1 ) {
					$opacity = $opacity / 100;
				}
				$output = 'rgba(' . implode( ',', $rgb ) . ',' . $opacity . ')';
			} else {
				$output = 'rgb(' . implode( ',', $rgb ) . ')';
			}

			if ( $is_array ) {
				return $rgb;
			} else {
				// Return rgb(a) color string.
				return $output;
			}
		}

		/**
		 * Returns an array of paths for the upload directory
		 * of the current site.
		 *
		 * @since 1.0.0
		 * @return array
		 */
		public static function get_upload_dir() {
			$wp_info = wp_upload_dir( null, false );

			// SSL workaround.
			if ( self::is_ssl() ) {
				$wp_info['baseurl'] = str_ireplace( 'http://', 'https://', $wp_info['baseurl'] );
			}

			$dir_name = 'mbfg-plugin';

			// Build the paths.
			$dir_info = array(
				'path' => trailingslashit( trailingslashit( $wp_info['basedir'] ) . $dir_name ),
				'url'  => trailingslashit( trailingslashit( $wp_info['baseurl'] ) . $dir_name ),
			);

			// Create the upload dir if it doesn't exist.
			if ( ! file_exists( $dir_info['path'] ) ) {
				// Create the directory.
				$wp_filesystem = mbfg_filesystem();
				$wp_filesystem->mkdir( $dir_info['path'] );
				// Add an index file for security.
				$wp_filesystem->put_contents( $dir_info['path'] . 'index.html', '', FS_CHMOD_FILE );
			}

			return apply_filters( 'mbfg_get_upload_dir', $dir_info );
		}

		/**
		 * Deletes the upload dir.
		 *
		 * @since 1.0.0
		 * @return array
		 */
		public static function delete_upload_dir() {
			$wp_info  = wp_upload_dir( null, false );
			$dir_name = 'mbfg-plugin';

			// Build the paths.
			$dir_info = array(
				'path' => trailingslashit( trailingslashit( $wp_info['basedir'] ) . $dir_name ),
			);

			// Check the upload dir if it doesn't exist or not.
			if ( file_exists( $dir_info['path'] . 'index.html' ) ) {
				// Remove the directory.
				$wp_filesystem = mbfg_filesystem();
				return $wp_filesystem->rmdir( $dir_info['path'], true );
			}
			return false;
		}

		/**
		 * Get MBFG upload dir path.
		 *
		 * @since 1.0.0
		 * @return string
		 */
		public static function get_mbfg_upload_dir_path() {
			$wp_info = self::get_upload_dir();

			// Build the paths.
			return $wp_info['path'];
		}

		/**
		 * Get MBFG upload url path.
		 *
		 * @since 1.0.0
		 * @return string
		 */
		public static function get_mbfg_upload_url_path() {
			$wp_info = self::get_upload_dir();

			// Build the paths.
			return $wp_info['url'];
		}

		/**
		 * Delete all files from MBFG upload dir.
		 *
		 * @since 1.0.0
		 * @return string
		 */
		public static function delete_all_mbfg_dir_files() {
			$dir           = self::get_mbfg_upload_dir_path();
			$wp_filesystem = mbfg_filesystem();
			$filelist      = $wp_filesystem->dirlist( $dir, true );
			$retval        = true;

			if ( is_array( $filelist ) ) {
				unset( $filelist['index.html'] );
				unset( $filelist['custom-style-blocks.css'] );

				foreach ( $filelist as $filename => $fileinfo ) {
					if ( ! $wp_filesystem->delete( $dir . $filename, true, $fileinfo['type'] ) ) {
						$retval = false;
					}
				}
			}

			return $retval;
		}

		/**
		 * Checks to see if the site has SSL enabled or not.
		 *
		 * @since 1.0.0
		 * @return bool
		 */
		public static function is_ssl() {
			if (
				is_ssl() ||
				( 0 === stripos( get_option( 'siteurl' ), 'https://' ) ) ||
				( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && 'https' === $_SERVER['HTTP_X_FORWARDED_PROTO'] )
			) {
				return true;
			}
			return false;
		}

		/**
		 * Allow File Geranation flag.
		 *
		 * @since 1.0.0
		 */
		public static function allow_file_generation() {
			return get_option( '_mbfg_allow_file_generation', 'disabled' );
		}

		/**
		 * Check if MBFG upload folder has write permissions or not.
		 *
		 * @since 1.0.0
		 * @return bool true or false.
		 */
		public static function is_mbfg_dir_has_write_permissions() {
			$upload_dir = self::get_upload_dir();

			return mbfg_filesystem()->is_writable( $upload_dir['path'] );
		}
		/**
		 * Gives the paged Query var.
		 *
		 * @param Object $query Query.
		 * @return int $paged Paged Query var.
		 * @since 1.0.0
		 */
		public static function get_paged( $query ) {
			global $paged;

			// Check the 'paged' query var.
			$paged_qv = $query->get( 'paged' );

			if ( is_numeric( $paged_qv ) ) {
				return $paged_qv;
			}

			// Check the 'page' query var.
			$page_qv = $query->get( 'page' );

			if ( is_numeric( $page_qv ) ) {
				return $page_qv;
			}

			// Check the $paged global?
			if ( is_numeric( $paged ) ) {
				return $paged;
			}

			return 0;
		}

		/**
		 * Builds the base url.
		 *
		 * @param string $permalink_structure Premalink Structure.
		 * @param string $base Base.
		 * @since 1.0.0
		 */
		public static function build_base_url( $permalink_structure, $base ) {
			// Check to see if we are using pretty permalinks.
			if ( ! empty( $permalink_structure ) ) {

				if ( strrpos( $base, 'paged-' ) ) {
					$base = substr_replace( $base, '', strrpos( $base, 'paged-' ), strlen( $base ) );
				}

				// Remove query string from base URL since paginate_links() adds it automatically.
				// This should also fix the WPML pagination issue that was added since 1.10.2.
				if ( count( $_GET ) > 0 ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					$base = strtok( $base, '?' );
				}

				// Add trailing slash when necessary.
				if ( '/' === substr( $permalink_structure, -1 ) ) {
					$base = trailingslashit( $base );
				} else {
					$base = untrailingslashit( $base );
				}
			} else {
				$url_params = wp_parse_url( $base, PHP_URL_QUERY );

				if ( empty( $url_params ) ) {
					$base = trailingslashit( $base );
				}
			}

			return $base;
		}
		/**
		 * Returns the Paged Format.
		 *
		 * @param string $permalink_structure Premalink Structure.
		 * @param string $base Base.
		 * @since 1.0.0
		 */
		public static function paged_format( $permalink_structure, $base ) {
			$page_prefix = empty( $permalink_structure ) ? 'paged' : 'page';

			if ( ! empty( $permalink_structure ) ) {
				$format  = substr( $base, -1 ) !== '/' ? '/' : '';
				$format .= $page_prefix . '/';
				$format .= '%#%';
				$format .= substr( $permalink_structure, -1 ) === '/' ? '/' : '';
			} elseif ( empty( $permalink_structure ) || is_search() ) {
				$parse_url = wp_parse_url( $base, PHP_URL_QUERY );
				$format    = empty( $parse_url ) ? '?' : '&';
				$format   .= $page_prefix . '=%#%';
			}

			return $format;
		}

		/**
		 * Get Typography Dynamic CSS.
		 *
		 * @param  array  $attr The Attribute array.
		 * @param  string $slug The field slug.
		 * @param  string $selector The selector array.
		 * @param  array  $combined_selectors The combined selector array.
		 * @since 1.0.0
		 * @return bool|string
		 */
		public static function get_typography_css( $attr, $slug, $selector, $combined_selectors ) {
			$typo_css_desktop = array();
			$typo_css_tablet  = array();
			$typo_css_mobile  = array();

			$already_selectors_desktop = ( isset( $combined_selectors['desktop'][ $selector ] ) ) ? $combined_selectors['desktop'][ $selector ] : array();
			$already_selectors_tablet  = ( isset( $combined_selectors['tablet'][ $selector ] ) ) ? $combined_selectors['tablet'][ $selector ] : array();
			$already_selectors_mobile  = ( isset( $combined_selectors['mobile'][ $selector ] ) ) ? $combined_selectors['mobile'][ $selector ] : array();

			$family_slug = ( '' === $slug ) ? 'fontFamily' : $slug . 'FontFamily';
			$weight_slug = ( '' === $slug ) ? 'fontWeight' : $slug . 'FontWeight';

			$l_ht_slug      = ( '' === $slug ) ? 'lineHeight' : $slug . 'LineHeight';
			$f_sz_slug      = ( '' === $slug ) ? 'fontSize' : $slug . 'FontSize';
			$l_ht_type_slug = ( '' === $slug ) ? 'lineHeightType' : $slug . 'LineHeightType';
			$f_sz_type_slug = ( '' === $slug ) ? 'fontSizeType' : $slug . 'FontSizeType';

			$typo_css_desktop[ $selector ] = array(
				'font-family' => $attr[ $family_slug ],
				'font-weight' => $attr[ $weight_slug ],
				'font-size'   => ( isset( $attr[ $f_sz_slug ] ) ) ? self::get_css_value( $attr[ $f_sz_slug ], $attr[ $f_sz_type_slug ] ) : '',
				'line-height' => ( isset( $attr[ $l_ht_slug ] ) ) ? self::get_css_value( $attr[ $l_ht_slug ], $attr[ $l_ht_type_slug ] ) : '',
			);

			$typo_css_desktop[ $selector ] = array_merge(
				$typo_css_desktop[ $selector ],
				$already_selectors_desktop
			);

			$typo_css_tablet[ $selector ] = array(
				'font-size'   => ( isset( $attr[ $f_sz_slug . 'Tablet' ] ) ) ? self::get_css_value( $attr[ $f_sz_slug . 'Tablet' ], $attr[ $f_sz_type_slug ] ) : '',
				'line-height' => ( isset( $attr[ $l_ht_slug . 'Tablet' ] ) ) ? self::get_css_value( $attr[ $l_ht_slug . 'Tablet' ], $attr[ $l_ht_type_slug ] ) : '',
			);

			$typo_css_tablet[ $selector ] = array_merge(
				$typo_css_tablet[ $selector ],
				$already_selectors_tablet
			);

			$typo_css_mobile[ $selector ] = array(
				'font-size'   => ( isset( $attr[ $f_sz_slug . 'Mobile' ] ) ) ? self::get_css_value( $attr[ $f_sz_slug . 'Mobile' ], $attr[ $f_sz_type_slug ] ) : '',
				'line-height' => ( isset( $attr[ $l_ht_slug . 'Mobile' ] ) ) ? self::get_css_value( $attr[ $l_ht_slug . 'Mobile' ], $attr[ $l_ht_type_slug ] ) : '',
			);

			$typo_css_mobile[ $selector ] = array_merge(
				$typo_css_mobile[ $selector ],
				$already_selectors_mobile
			);

			return array(
				'desktop' => array_merge(
					$combined_selectors['desktop'],
					$typo_css_desktop
				),
				'tablet'  => array_merge(
					$combined_selectors['tablet'],
					$typo_css_tablet
				),
				'mobile'  => array_merge(
					$combined_selectors['mobile'],
					$typo_css_mobile
				),
			);
		}

		/**
		 * Parse CSS into correct CSS syntax.
		 *
		 * @param array  $combined_selectors The combined selector array.
		 * @param string $id The selector ID.
		 * @since 1.0.0
		 */
		public static function generate_all_css( $combined_selectors, $id ) {
			return array(
				'desktop' => self::generate_css( $combined_selectors['desktop'], $id ),
				'tablet'  => self::generate_css( $combined_selectors['tablet'], $id ),
				'mobile'  => self::generate_css( $combined_selectors['mobile'], $id ),
			);
		}

		/**
		 * Get Post Assets Instance.
		 * 
		 * @since 1.0.0
		 */
		public function get_post_assets_instance() {
			return mbfg_get_front_post_assets();
		}

		/** Generates stylesheet in loop.
		 *
		 * @since 1.0.0
		 * @param object $this_post Post Object.
		 *
		 * @access public
		 */
		public function get_generated_stylesheet( $this_post ) {
			if ( ! is_object( $this_post ) ) {
				return;
			}

			if ( ! isset( $this_post->ID ) ) {
				return;
			}

			if ( has_blocks( $this_post->ID ) && isset( $this_post->post_content ) ) {

				$blocks            = parse_blocks( $this_post->post_content );
				self::$page_blocks = $blocks;

				if ( ! is_array( $blocks ) || empty( $blocks ) ) {
					return;
				}

				$assets = $this->get_assets( $blocks );

				self::$stylesheet .= $assets['css'];
				self::$script     .= $assets['js'];
			}
		}

		/**
		 * Generates stylesheet for reusable blocks.
		 *
		 * @since 1.0.0
		 * @param array $blocks Blocks.
		 * @access public
		 */
		public function get_assets( $blocks ) {
			$desktop = '';
			$tablet  = '';
			$mobile  = '';

			$tab_styling_css = '';
			$mob_styling_css = '';

			$js = '';

			foreach ( $blocks as $i => $block ) {

				if ( is_array( $block ) ) {

					if ( empty( $block['blockName'] ) ) {
						continue;
					}

					if ( 'core/block' === $block['blockName'] ) {
						$id = ( isset( $block['attrs']['ref'] ) ) ? $block['attrs']['ref'] : 0;

						if ( $id ) {
							$content = get_post_field( 'post_content', $id );

							$reusable_blocks = parse_blocks( $content );

							$assets = $this->get_assets( $reusable_blocks );

							self::$stylesheet .= $assets['css'];
							self::$script     .= $assets['js'];

						}
					} else {

						$block_assets = $this->get_block_css_and_js( $block );
						// Get CSS for the Block.
						$css = $block_assets['css'];

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

			$post_assets_instance = $this->get_post_assets_instance();
			if ( $post_assets_instance ) {

				$post_assets_instance->stylesheet .= $desktop . $tab_styling_css . $mob_styling_css;
				$post_assets_instance->script     .= $js;
			}

			return array(
				'css' => $desktop . $tab_styling_css . $mob_styling_css,
				'js'  => $js,
			);
		}

		/**
		 * Parse Guten Block.
		 *
		 * @since 1.0.0
		 * @param string $content the content string.
		 * @access public
		 */
		public function parse( $content ) {
			return parse_blocks( $content );
		}
		/**
		 * This is the action where we create dynamic asset files.
		 * CSS Path : uploads/mbfg-plugin/mbfg-style-{post_id}-{timestamp}.css
		 * JS Path : uploads/mbfg-plugin/mbfg-script-{post_id}-{timestamp}.js
		 *
		 * @since 1.0.0
		 */
		public function generate_asset_files() {
			global $content_width;
			self::$stylesheet = str_replace( '#CONTENT_WIDTH#', $content_width . 'px', self::$stylesheet );
			if ( '' !== self::$script ) {
				self::$script = 'document.addEventListener("DOMContentLoaded", function(){ ' . self::$script . ' })';
			}

			if ( 'enabled' === self::$file_generation ) {

				$post_assets_instance = $this->get_post_assets_instance();

				if ( $post_assets_instance ) {
					$post_assets_instance->stylesheet .= self::$stylesheet;
					$post_assets_instance->script     .= self::$script;
				}
			}
		}

		/**
		 * Enqueue Gutenberg block assets for both frontend + backend.
		 *
		 * @since 1.0.0
		 */
		public function block_assets() {
			$this->get_post_assets_instance()->enqueue_blocks_dependency_frontend();

		}
		/**
		 * Print the Script in footer.
		 *
		 * @since 1.0.0
		 */
		public function print_script() {
			$this->get_post_assets_instance()->print_script();

		}
		/**
		 * Print the Stylesheet in header.
		 *
		 * @since 1.0.0
		 */
		public function print_stylesheet() {
			$this->get_post_assets_instance()->print_stylesheet();

		}
		/**
		 * Load the front end Google Fonts.
		 *
		 * @since 1.0.0
		 */
		public function frontend_gfonts() {
			$this->get_post_assets_instance()->print_google_fonts();

		}

		/**
		 * Generates CSS recurrsively.
		 *
		 * @param object $block The block object.
		 * @since 1.0.0
		 */
		public function get_block_css_and_js( $block ) {
			$block    = (array) $block;
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

			self::$current_block_list[] = $name;

			if ( strpos( $name, 'mbfg/' ) !== false ) {
				self::$mbfg_flag = true;
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

							$reusable_blocks = $this->parse( $content );

							$assets = $this->get_assets( $reusable_blocks );

							self::$stylesheet .= $assets['css'];
							self::$script     .= $assets['js'];
						}
					} else {
						// Get CSS for the Block.
						$inner_assets    = $this->get_block_css_and_js( $inner_block );
						$inner_block_css = $inner_assets['css'];

						$css_desktop = ( isset( $css['desktop'] ) ? $css['desktop'] : '' );
						$css_tablet  = ( isset( $css['tablet'] ) ? $css['tablet'] : '' );
						$css_mobile  = ( isset( $css['mobile'] ) ? $css['mobile'] : '' );

						if ( isset( $inner_block_css['desktop'] ) ) {
							$css['desktop'] = $css_desktop . $inner_block_css['desktop'];
							$css['tablet']  = $css_tablet . $inner_block_css['tablet'];
							$css['mobile']  = $css_mobile . $inner_block_css['mobile'];
						}

						$js .= $inner_assets['js'];
					}
				}
			}

			self::$current_block_list = array_unique( self::$current_block_list );

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
			$this_post = array();

			if ( class_exists( 'WooCommerce' ) ) {

				if ( is_cart() ) {

					$id        = get_option( 'woocommerce_cart_page_id' );
					$this_post = get_post( $id );

				} elseif ( is_account_page() ) {

					$id        = get_option( 'woocommerce_myaccount_page_id' );
					$this_post = get_post( $id );

				} elseif ( is_checkout() ) {

					$id        = get_option( 'woocommerce_checkout_page_id' );
					$this_post = get_post( $id );

				} elseif ( is_checkout_pay_page() ) {

					$id        = get_option( 'woocommerce_pay_page_id' );
					$this_post = get_post( $id );

				} elseif ( is_shop() ) {

					$id        = get_option( 'woocommerce_shop_page_id' );
					$this_post = get_post( $id );
				}

				if ( is_object( $this_post ) ) {
					$this->get_generated_stylesheet( $this_post );
				}
			}

			if ( is_single() || is_page() || is_404() ) {

				global $post;
				$this_post = $post;

				if ( ! is_object( $this_post ) ) {
					return;
				}

				/**
				 * Filters the post to build stylesheet for.
				 *
				 * @param \WP_Post $this_post The global post.
				 */
				$this_post = apply_filters( 'mbfg_post_for_stylesheet', $this_post );

				$this->get_generated_stylesheet( $this_post );

			} elseif ( is_archive() || is_home() || is_search() ) {

				global $wp_query;
				$cached_wp_query = $wp_query;

				foreach ( $cached_wp_query as $post ) { // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
					$this->get_generated_stylesheet( $post );
				}
			}
		}
	}

	/**
	 *  Prepare if class 'FBFG_Helper' exist.
	 *  Kicking this off by calling 'get_instance()' method
	 */
	FBFG_Helper::get_instance();
}
