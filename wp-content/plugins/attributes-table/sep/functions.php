<?php

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Manages product functions
 *
 * Here all plugin functions are defined and managed.
 *
 * @version        1.0.0
 * @package        attributes-table/functions
 * @author        Norbert Dreszer
 */

/**
 * Returns active post types except al_product related
 *
 * @return type
 */
function get_attributes_active_post_types() {
	$settings	 = get_attributes_table_settings();
	$post_types	 = array_filter( $settings[ 'enabled' ], 'ic_filter_post_types_array' );
	return $post_types;
}

/**
 * Returns post types where attributes shows up automatically
 *
 * @return type
 */
function get_attributes_show_active_post_types() {
	$settings	 = get_attributes_table_settings();
	$post_types	 = array_filter( $settings[ 'show' ], 'ic_filter_post_types_array' );
	return $post_types;
}

if ( !function_exists( 'ic_filter_post_types_array' ) ) {

	/**
	 * Deletes all product post types from array
	 *
	 * @param type $string
	 * @return type
	 */
	function ic_filter_post_types_array( $string ) {
		return strpos( $string, 'al_product' ) === false;
	}

}

if ( !function_exists( 'get_external_single_names' ) ) {

	/**
	 * Defines single name for shipping if catalog is missing
	 * @return string
	 */
	function get_external_single_names() {
		if ( function_exists( 'get_single_names' ) ) {
			$single_names = get_single_names();
		} else {
			$single_names = array( 'product_price' => __( 'Price', 'shipping-options' ) . ':', 'product_features' => __( 'Features', 'shipping-options' ), 'product_shipping' => __( 'Shipping', 'shipping-options' ) . ':' );
		}
		return $single_names;
	}

}

add_shortcode( 'attributes_table', 'ic_attributes_table_shortcode' );

/**
 * Defines attributes field table shortcode
 *
 * @param type $atts
 * @return type
 */
function ic_attributes_table_shortcode( $atts ) {
	$args			 = shortcode_atts( array(
		'id' => get_the_ID(),
	), $atts );
	$single_names	 = get_external_single_names();
	return get_product_attributes( $args[ 'id' ], $single_names );
}

/**
 * Shows attributes field
 *
 * @param type $id
 */
function ic_attributes_table( $id = null ) {
	$id				 = empty( $id ) ? get_the_ID() : $id;
	$single_names	 = get_external_single_names();
	echo get_product_attributes( $id, $single_names );
}

add_filter( 'the_content', 'show_auto_attributes_table' );

/**
 * Shows attributes on certain post types
 *
 * @param type $content
 * @return type
 */
function show_auto_attributes_table( $content ) {
	if ( is_ic_admin() ) {
		return $content;
	}
	$post_type					 = get_post_type();
	$attributes_show_post_type	 = get_attributes_show_active_post_types();
	if ( in_array( $post_type, $attributes_show_post_type ) ) {
		ob_start();
		ic_show_template_file( 'product-page/product-attributes.php', AL_ATTRIBUTES_BASE_PATH );
		$content .= ob_get_clean();
//$single_names = get_external_single_names();
		//$content .= '<style>.features-table {width: auto;}</style>';
		//$content .= get_product_attributes( get_the_ID(), $single_names );
	}
	return $content;
}

add_action( 'admin_enqueue_scripts', 'ic_enqueue_attr_sep_scripts' );

/**
 * Adds admin scripts for selected post types
 *
 */
function ic_enqueue_attr_sep_scripts() {
	if ( is_ic_sep_admin_screen() ) {
		wp_enqueue_script( 'al_product_admin-scripts' );
	}
}

if ( !function_exists( 'is_ic_sep_admin_screen' ) ) {

	function is_ic_sep_admin_screen() {
		if ( is_admin() && function_exists( 'get_current_screen' ) ) {
			$screen = get_current_screen();
			if ( isset( $screen->post_type ) ) {
				$post_types = get_attributes_active_post_types();
				if ( in_array( $screen->post_type, $post_types ) ) {
					return true;
				}
			}
		}
		return false;
	}

}
