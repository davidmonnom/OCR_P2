<?php
if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Manages product post type
 *
 * Here all product fields are defined.
 *
 * @version        1.1.1
 * @package		post-type-x/core/includes
 * @author        impleCode
 */
class ic_register_product {

	function __construct() {
		add_action( 'register_catalog_styles', array( $this, 'frontend_scripts' ) );
		add_action( 'init', array( $this, 'ic_create_product' ), 4 );
		add_action( 'admin_head', array( $this, 'product_icons' ) );
		add_action( 'post_updated', array( $this, 'implecode_save_products_meta' ), 1, 2 );
		add_action( 'current_screen', array( $this, 'edit_screen' ) );

		add_filter( 'generate_rewrite_rules', array( $this, 'rewrite_rules' ) );

		add_filter( 'use_block_editor_for_post_type', array( $this, 'ret_false' ), 99, 2 );
		add_filter( 'gutenberg_can_edit_post_type', array( $this, 'ret_false' ), 99, 2 );
		add_filter( 'use_block_editor_for_post', array( $this, 'ret_false' ), 99, 2 );
		add_filter( 'gutenberg_can_edit_post', array( $this, 'ret_false' ), 99, 2 );

		add_action( 'wp_print_scripts', array( $this, 'structured_data' ) );

		require_once(AL_BASE_PATH . '/includes/product-categories.php');
	}

	function edit_screen() {
		if ( is_ic_new_product_screen() || is_ic_edit_product_screen() ) {
			add_action( 'edit_form_after_title', array( $this, 'ic_remove_default_desc_editor' ) );
			add_action( 'edit_form_after_editor', array( $this, 'ic_restore_default_desc_editor' ) );

			add_action( 'admin_menu', array( $this, 'ic_remove_unnecessary_metaboxes' ) );
			add_action( 'admin_head', array( $this, 'ic_remove_unnecessary_metaboxes' ), 999 );

			add_action( 'do_meta_boxes', array( $this, 'change_image_box' ) );

			add_filter( 'post_updated_messages', array( $this, 'set_product_messages' ) );
		}
	}

	function ret_false( $can_edit, $post_type ) {
		if ( isset( $post_type->post_type ) ) {
			$post_type = $post_type->post_type;
		}
		if ( ic_string_contains( $post_type, 'al_product' ) ) {
			return false;
		}
		return $can_edit;
	}

	/**
	 * Registers product related front-end scripts
	 */
	function frontend_scripts() {
		if ( !is_admin() || is_ic_ajax() ) {
			$dependence = array( 'jquery' );
			if ( is_ic_product_page() ) {
				if ( is_ic_magnifier_enabled() || (function_exists( 'is_customize_preview' ) && is_customize_preview()) ) {
					wp_register_script( 'ic_magnifier', AL_PLUGIN_BASE_PATH . 'js/magnifier/magnifier.js' . ic_filemtime( AL_BASE_PATH . '/js/magnifier/magnifier.js' ) );
					$dependence[] = 'ic_magnifier';
				}
				if ( (function_exists( 'is_customize_preview' ) && is_customize_preview()) || (is_lightbox_enabled() && is_ic_product_gallery_enabled()) ) {
					wp_register_script( 'colorbox', AL_PLUGIN_BASE_PATH . 'js/colorbox/jquery.colorbox-min.js', array( 'jquery' ) );
					wp_register_style( 'colorbox', AL_PLUGIN_BASE_PATH . 'js/colorbox/colorbox.css' );
					$dependence[] = 'colorbox';
				}
			}
			wp_register_script( 'al_product_scripts', AL_PLUGIN_BASE_PATH . 'js/product.min.js' . ic_filemtime( AL_BASE_PATH . '/js/product.min.js' ), apply_filters( 'al_product_scripts_dependence', $dependence ) );
		}
	}

	/**
	 * Registers products post type
	 * @global type $wp_version
	 */
	function ic_create_product() {
		global $wp_version;
		$slug = get_product_slug();
//$listing_status	 = ic_get_product_listing_status();
		if ( is_ic_product_listing_enabled() && (get_integration_type() != 'simple' && !is_ic_shortcode_integration()) ) {
			$product_listing_t = $slug;
		} else {
			$product_listing_t = false;
//$product_listing_t	 = $slug;
		}
		$names		 = get_catalog_names();
		$query_var	 = $this->get_product_query_var();
		if ( is_plural_form_active() ) {
			$labels = array(
				'name'					 => $names[ 'plural' ],
				'singular_name'			 => $names[ 'singular' ],
				'add_new'				 => sprintf( __( 'Add New %s', 'post-type-x' ), ic_ucfirst( $names[ 'singular' ] ) ),
				'add_new_item'			 => sprintf( __( 'Add New %s', 'post-type-x' ), ic_ucfirst( $names[ 'singular' ] ) ),
				'edit_item'				 => sprintf( __( 'Edit %s', 'post-type-x' ), ic_ucfirst( $names[ 'singular' ] ) ),
				'new_item'				 => sprintf( __( 'Add New %s', 'post-type-x' ), ic_ucfirst( $names[ 'singular' ] ) ),
				'view_item'				 => sprintf( __( 'View %s', 'post-type-x' ), ic_ucfirst( $names[ 'singular' ] ) ),
				'search_items'			 => sprintf( __( 'Search %s', 'post-type-x' ), ic_ucfirst( $names[ 'plural' ] ) ),
				'not_found'				 => sprintf( __( 'No %s found', 'post-type-x' ), $names[ 'plural' ] ),
				'not_found_in_trash'	 => sprintf( __( 'No %s found in trash', 'post-type-x' ), $names[ 'plural' ] ),
				'set_featured_image'	 => sprintf( __( 'Set main %s image', 'post-type-x' ), $names[ 'plural' ] ),
				'remove_featured_image'	 => sprintf( __( 'Remove main %s image', 'post-type-x' ), $names[ 'plural' ] ),
				'featured_image'		 => sprintf( __( '%s Image', 'post-type-x' ), ic_ucfirst( $names[ 'singular' ] ) ),
			);
		} else {
			$labels = array(
				'name'					 => $names[ 'plural' ],
				'singular_name'			 => $names[ 'singular' ],
				'add_new'				 => __( 'Add New', 'post-type-x' ),
				'add_new_item'			 => __( 'Add New Item', 'post-type-x' ),
				'edit_item'				 => __( 'Edit Item', 'post-type-x' ),
				'new_item'				 => __( 'Add New Item', 'post-type-x' ),
				'view_item'				 => __( 'View Item', 'post-type-x' ),
				'search_items'			 => __( 'Search Items', 'post-type-x' ),
				'not_found'				 => __( 'Nothing found', 'post-type-x' ),
				'not_found_in_trash'	 => __( 'Nothing found in trash', 'post-type-x' ),
				'set_featured_image'	 => __( 'Set main image', 'post-type-x' ),
				'remove_featured_image'	 => __( 'Remove main image', 'post-type-x' ),
				'featured_image'		 => __( 'Image', 'post-type-x' )
			);
		}
		if ( version_compare( $wp_version, 3.8 ) < 0 ) {
			$reg_settings = array(
				'labels'				 => $labels,
				'public'				 => true,
				'show_in_rest'			 => true,
				'show_in_nav_menus'		 => true,
				'hierarchical'			 => false,
				'has_archive'			 => $product_listing_t,
				'rewrite'				 => array( 'slug' => apply_filters( 'product_slug_value_register', $slug ), 'with_front' => false ),
				'query_var'				 => $query_var,
				'supports'				 => apply_filters( 'ic_products_type_support', array( 'title', 'thumbnail', 'editor', 'excerpt' ) ),
				'register_meta_box_cb'	 => array( $this, 'add_product_metaboxes' ),
				'taxonomies'			 => array( 'al_product_cat' ),
				'menu_icon'				 => plugins_url() . '/ecommerce-product-catalog/img/product.png',
				'capability_type'		 => 'product',
				'map_meta_cap'			 => true,
				'menu_position'			 => 30,
				/*
				  'capabilities'			 => array(
				  'publish_posts'			 => 'publish_products',
				  'edit_posts'			 => 'edit_products',
				  'edit_others_posts'		 => 'edit_others_products',
				  'edit_published_posts'	 => 'edit_published_products',
				  'edit_private_posts'	 => 'edit_private_products',
				  'delete_posts'			 => 'delete_products',
				  'delete_others_posts'	 => 'delete_others_products',
				  'delete_private_posts'	 => 'delete_private_products',
				  'delete_published_posts' => 'delete_published_products',
				  'read_private_posts'	 => 'read_private_products',
				  'edit_post'				 => 'edit_product',
				  'delete_post'			 => 'delete_product',
				  'read_post'				 => 'read_product',
				  ),
				 *
				 */
				'exclude_from_search'	 => false,
			);
		} else {
			$reg_settings = array(
				'labels'				 => $labels,
				'public'				 => true,
				'show_in_rest'			 => true,
				'show_in_nav_menus'		 => true,
				'hierarchical'			 => false,
				'has_archive'			 => $product_listing_t,
				'rewrite'				 => array( 'slug' => apply_filters( 'product_slug_value_register', $slug ), 'with_front' => false, 'pages' => true ),
				'query_var'				 => $query_var,
				'supports'				 => apply_filters( 'ic_products_type_support', array( 'title', 'thumbnail', 'editor', 'excerpt' ) ),
				'register_meta_box_cb'	 => array( $this, 'add_product_metaboxes' ),
				'taxonomies'			 => array( 'al_product-cat' ),
				'capability_type'		 => 'product',
				'map_meta_cap'			 => true,
				'menu_position'			 => 30,
				/*
				  'capabilities'			 => array(
				  'publish_posts'			 => 'publish_products',
				  'edit_posts'			 => 'edit_products',
				  'edit_others_posts'		 => 'edit_others_products',
				  'edit_published_posts'	 => 'edit_published_products',
				  'edit_private_posts'	 => 'edit_private_products',
				  'delete_posts'			 => 'delete_products',
				  'delete_others_posts'	 => 'delete_others_products',
				  'delete_private_posts'	 => 'delete_private_products',
				  'delete_published_posts' => 'delete_published_products',
				  'read_private_posts'	 => 'read_private_products',
				  'edit_post'				 => 'edit_product',
				  'delete_post'			 => 'delete_product',
				  'read_post'				 => 'read_product',
				  ),
				 *
				 */
				'exclude_from_search'	 => false,
			);
		}
		register_post_type( 'al_product', $reg_settings );
	}

	function get_product_query_var() {
		$query_var = 'al_product';
		if ( !is_ic_permalink_product_catalog() ) {
			$names			 = get_catalog_names();
			$new_query_var	 = sanitize_title( ic_strtolower( $names[ 'singular' ] ) );
			$new_query_var	 = (strpos( $new_query_var, '%' ) !== false) ? 'product' : $new_query_var;
			$forbidden		 = ic_forbidden_query_vars();
			if ( array_search( $new_query_var, $forbidden ) === false ) {
				$query_var = $new_query_var;
			}
		}
		return apply_filters( 'product_query_var', $query_var );
	}

	function product_icons() {
		?>
		<style>
		<?php if ( isset( $_GET[ 'post_type' ] ) == 'al_product' ) : ?>
				#icon-edit {
					background: transparent url('<?php echo plugins_url() . '/ecommerce-product-catalog/img/product-32.png';
			?>') no-repeat;
				}

		<?php endif; ?>
		</style>
		<?php
	}

	function add_product_metaboxes() {
		$names				 = get_catalog_names();
		$names[ 'singular' ] = ic_ucfirst( $names[ 'singular' ] );
		if ( is_plural_form_active() ) {
			$labels[ 's_desc' ]	 = sprintf( __( '%s Short Description', 'post-type-x' ), $names[ 'singular' ] );
			$labels[ 'desc' ]	 = sprintf( __( '%s description', 'post-type-x' ), $names[ 'singular' ] );
			$labels[ 'details' ] = sprintf( __( '%s Details', 'post-type-x' ), $names[ 'singular' ] );
		} else {
			$labels[ 's_desc' ]	 = __( 'Short Description', 'post-type-x' );
			$labels[ 'desc' ]	 = __( 'Long Description', 'post-type-x' );
			$labels[ 'details' ] = __( 'Details', 'post-type-x' );
		}
		add_meta_box( 'al_product_short_desc', $labels[ 's_desc' ], array( $this, 'al_product_short_desc' ), 'al_product', apply_filters( 'short_desc_box_column', 'normal' ), apply_filters( 'short_desc_box_priority', 'default' ) );
		add_meta_box( 'al_product_desc', $labels[ 'desc' ], array( $this, 'al_product_desc' ), 'al_product', apply_filters( 'desc_box_column', 'normal' ), apply_filters( 'desc_box_priority', 'default' ) );
		if ( ic_product_details_box_visible() ) {
			add_meta_box( 'al_product_details', $labels[ 'details' ], array( $this, 'al_product_details' ), 'al_product', apply_filters( 'product_details_box_column', 'side' ), apply_filters( 'product_details_box_priority', 'default' ) );
		}
		do_action( 'add_product_metaboxes', $names );
	}

	function al_product_details() {
		global $post;
		echo '<input type="hidden" name="pricemeta_noncename" id="pricemeta_noncename" value="' .
		wp_create_nonce( plugin_basename( __FILE__ ) ) . '" />';
		$product_details = '';
		echo apply_filters( 'admin_product_details', $product_details, $post->ID );
	}

	function al_product_short_desc() {
		global $post;
		echo '<input type="hidden" name="shortdescmeta_noncename" id="shortdescmeta_noncename" value="' .
		wp_create_nonce( plugin_basename( __FILE__ ) ) . '" />';
		$shortdesc			 = get_product_short_description( $post->ID );
		$short_desc_settings = array( 'media_buttons'	 => false, 'textarea_rows'	 => 5, 'tinymce'		 => array(
				'menubar'	 => false,
				'toolbar1'	 => 'bold,italic,underline,blockquote,strikethrough,bullist,numlist,alignleft,aligncenter,alignright,undo,redo,link,unlink,fullscreen',
				'toolbar2'	 => '',
				'toolbar3'	 => '',
				'toolbar4'	 => '',
			) );
		wp_editor( $shortdesc, 'excerpt', $short_desc_settings );
	}

	function al_product_desc() {
		global $post;
		echo '<input type="hidden" name="descmeta_noncename" id="descmeta_noncename" value="' .
		wp_create_nonce( plugin_basename( __FILE__ ) ) . '" />';
		$desc			 = get_product_description( $post->ID );
		$desc_settings	 = array( 'textarea_rows' => 30 );
		wp_editor( $desc, 'content', $desc_settings );
	}

	/**
	 * Handles product data save
	 *
	 * @param type $post_id
	 * @param type $post
	 * @return type
	 */
	function implecode_save_products_meta( $post_id, $post ) {
		$post_type_now = substr( $post->post_type, 0, 10 );
		if ( $post_type_now == 'al_product' ) {
			$pricemeta_noncename = isset( $_POST[ 'pricemeta_noncename' ] ) ? $_POST[ 'pricemeta_noncename' ] : '';
			if ( !empty( $pricemeta_noncename ) && !wp_verify_nonce( $pricemeta_noncename, plugin_basename( __FILE__ ) ) ) {
				return $post->ID;
			}
			if ( !isset( $_POST[ 'action' ] ) ) {
				return $post->ID;
			} else if ( isset( $_POST[ 'action' ] ) && $_POST[ 'action' ] != 'editpost' ) {
				return $post->ID;
			}
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return $post->ID;
			}
			if ( is_ic_ajax() ) {
				return $post->ID;
			}
			if ( !current_user_can( 'edit_post', $post->ID ) ) {
				return $post->ID;
			}
			$product_meta[ 'excerpt' ]		 = isset( $_POST[ 'excerpt' ] ) && !empty( $_POST[ 'excerpt' ] ) ? $_POST[ 'excerpt' ] : '';
			$product_meta[ 'content' ]		 = isset( $_POST[ 'content' ] ) && !empty( $_POST[ 'content' ] ) ? $_POST[ 'content' ] : '';
			$product_meta[ '_product_name' ] = isset( $_POST[ 'post_title' ] ) && !empty( $_POST[ 'post_title' ] ) ? $_POST[ 'post_title' ] : '';

			$product_meta = apply_filters( 'product_meta_save', $product_meta, $post );
			foreach ( $product_meta as $key => $value ) {
				if ( in_array( $key, get_post_custom_keys( $post->ID ) ) ) {
					$current_value = get_post_meta( $post->ID, $key, true );
				}
				if ( (!empty( $value ) || $value === 0 || $value === '0') && !isset( $current_value ) ) {
					add_post_meta( $post->ID, $key, $value, true );
					if ( is_array( $value ) ) {
						foreach ( $value as $val ) {
							add_post_meta( $post->ID, $key . '_filterable', $val, false );
						}
					}
				} else if ( (!empty( $value ) || $value === 0 || $value === '0') && $value !== $current_value ) {
					update_post_meta( $post->ID, $key, $value );
					if ( is_array( $value ) ) {
						delete_post_meta( $post->ID, $key . '_filterable' );
						foreach ( $value as $val ) {
							add_post_meta( $post->ID, $key . '_filterable', $val, false );
						}
					}
				} else if ( empty( $value ) && isset( $current_value ) ) {
					delete_post_meta( $post->ID, $key );
					delete_post_meta( $post->ID, $key . '_filterable' );
				}
				unset( $current_value );
			}
			do_action( 'product_edit_save', $post, $product_meta );
		}
	}

	/**
	 * Disables the default editor screen on product add/edit page
	 *
	 */
	function ic_remove_default_desc_editor() {
		remove_post_type_support( 'al_product', 'editor' );
	}

	/**
	 * Restores editor support
	 */
	function ic_restore_default_desc_editor() {
		add_post_type_support( 'al_product', 'editor' );
	}

	/**
	 * Removes unnecessary metaboxes for product edit/add screen
	 *
	 */
	function ic_remove_unnecessary_metaboxes() {
		remove_meta_box( 'postexcerpt', 'al_product', 'normal' );
	}

	function change_image_box() {
		$names = get_catalog_names();
		remove_meta_box( 'postimagediv', 'al_product', 'side' );
		if ( is_plural_form_active() ) {
			$label = sprintf( __( '%s Image', 'post-type-x' ), ic_ucfirst( $names[ 'singular' ] ) );
		} else {
			$label = __( 'Image', 'post-type-x' );
		}
		add_meta_box( 'postimagediv', $label, 'post_thumbnail_meta_box', 'al_product', apply_filters( 'product_image_box_column', 'side' ), apply_filters( 'product_image_box_priority', 'high' ) );
	}

	/*
	  function change_thumbnail_html( $content ) {
	  if ( is_ic_catalog_admin_page() ) {
	  //add_filter( 'admin_post_thumbnail_html', 'modify_add_product_image_label' );
	  }
	  }

	  //add_action( 'admin_head-post-new.php', 'change_thumbnail_html' );
	  //add_action( 'admin_head-post.php', 'change_thumbnail_html' );

	  function modify_add_product_image_label( $label ) {
	  if ( is_plural_form_active() ) {
	  $names				 = get_catalog_names();
	  $names[ 'singular' ] = ic_strtolower( $names[ 'singular' ] );
	  $label				 = str_replace( __( 'Set featured image' ), sprintf( __( 'Set %s image', 'post-type-x' ), $names[ 'singular' ] ), $label );
	  $label				 = str_replace( __( 'Remove featured image' ), sprintf( __( 'Remove %s image', 'post-type-x' ), $names[ 'singular' ] ), $label );
	  } else {
	  $label	 = str_replace( __( 'Set featured image' ), __( 'Set image', 'post-type-x' ), $label );
	  $label	 = str_replace( __( 'Remove featured image' ), __( 'Remove image', 'post-type-x' ), $label );
	  }
	  return $label;
	  }
	 *
	 *
	 */

	function set_product_messages( $messages ) {
		global $post, $post_ID;
		$quasi_post_type = get_quasi_post_type();
		$post_type		 = get_post_type( $post_ID );
		if ( $quasi_post_type == 'al_product' ) {
			$obj		 = get_post_type_object( $post_type );
			$singular	 = $obj->labels->singular_name;

			$messages[ $post_type ] = array(
				0	 => '',
				1	 => sprintf( __( '%s updated. <a href="%s">View ' . ic_strtolower( $singular ) . '</a>' ), $singular, esc_url( get_permalink( $post_ID ) ) ),
				2	 => __( 'Custom field updated.' ),
				3	 => __( 'Custom field deleted.' ),
				4	 => sprintf( __( '%s updated.', 'post-type-x' ), $singular ),
				5	 => isset( $_GET[ 'revision' ] ) ? sprintf( __( $singular . ' restored to revision from %s' ), $singular, wp_post_revision_title( (int) $_GET[ 'revision' ], false ) ) : false,
				6	 => sprintf( __( $singular . ' published. <a href="%s">View ' . ic_strtolower( $singular ) . '</a>' ), esc_url( get_permalink( $post_ID ) ), $singular ),
				7	 => __( 'Page saved.' ),
				8	 => sprintf( __( '%s submitted. <a target="_blank" href="%s">Preview %s</a>' ), $singular, esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ), strtolower( $singular ) ),
				9	 => sprintf( __( '%3$s scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview ' . ic_strtolower( $singular ) . '</a>' ), date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink( $post_ID ) ), $singular ),
				10	 => sprintf( __( '%s draft updated. <a target="_blank" href="%s">Preview ' . ic_strtolower( $singular ) . '</a>' ), $singular, esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
			);
		}
		return $messages;
	}

	/**
	 * Rewrite to support pagination on shortcode archive
	 *
	 * @param type $wp_rewrite
	 */
	function rewrite_rules( $wp_rewrite ) {
		if ( is_ic_shortcode_integration() ) {
			$slug		 = get_product_slug();
			$listing_id	 = intval( get_product_listing_id() );
			if ( !empty( $slug ) && !empty( $listing_id ) ) {
				$rule			 = $slug . '/page/?([0-9]{1,})/?$';
				$rewrite		 = 'index.php?page_id=' . $listing_id . '&paged=$matches[1]';
				$rules[ $rule ]	 = $rewrite;
			}
		}

		if ( !empty( $rules ) ) {
			$wp_rewrite->rules = $rules + $wp_rewrite->rules;
		}
		return apply_filters( 'ic_cat_urls_rewrite', $wp_rewrite );
	}

	function structured_data() {
		$archive_multiple_settings = get_multiple_settings();
		if ( !empty( $archive_multiple_settings[ 'enable_structured_data' ] ) ) {
			ic_show_template_file( 'product-page/structured-data.php', AL_BASE_TEMPLATES_PATH );
		}
	}

}

$ic_register_product = new ic_register_product;
