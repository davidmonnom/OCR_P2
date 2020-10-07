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
 * @package        price-field/functions
 * @author        Norbert Dreszer
 */

/**
 * Returns active post types except al_product related
 *
 * @return type
 */
function get_price_active_post_types() {
	$settings	 = get_price_field_settings();
	$post_types	 = array_filter( $settings[ 'enabled' ], 'filter_price_post_types_array' );
	return $post_types;
}

/**
 * Returns post types where price shows up automatically
 *
 * @return type
 */
function get_price_show_active_post_types() {
	$settings	 = get_price_field_settings();
	$post_types	 = array_filter( $settings[ 'show' ], 'filter_price_post_types_array' );
	return $post_types;
}

/**
 * Deletes all product post types from array
 *
 * @param type $string
 * @return type
 */
function filter_price_post_types_array( $string ) {
	return strpos( $string, 'al_product' ) === false;
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
			$single_names = array( 'product_price' => __( 'Price', 'price-field' ) . ':', 'product_features' => __( 'Features', 'price-field' ), 'product_shipping' => __( 'Shipping', 'price-field' ) . ':' );
		}
		return $single_names;
	}

}

add_shortcode( 'price_field', 'ic_price_field_table' );

/**
 * Defines price field table shortcode
 *
 * @param type $atts
 * @return type
 */
function ic_price_field_table( $atts ) {
	$args			 = shortcode_atts( array(
		'id' => get_the_ID(),
	), $atts );
	$single_names	 = get_external_single_names();
	return get_price_field_table( $args[ 'id' ], $single_names );
}

/**
 * Shows price field
 *
 * @param type $id
 */
function ic_price_field( $id = null ) {
	$id				 = empty( $id ) ? get_the_ID() : $id;
	$single_names	 = get_external_single_names();
	echo get_price_field_table( $id, $single_names );
}

/**
 * Returns price field table
 *
 * @param type $product_id
 * @param type $single_names
 * @return type
 */
function get_price_field_table( $product_id, $single_names ) {
	$price_value = product_price( $product_id );
	$table		 = '';
	if ( !empty( $price_value ) ) {
		$table	 .= '<style>.price-table {width: auto}.price-value {font-size: 1.2em;}</style>';
		$table	 .= '<table class="price-table">';
		$table	 .= '<tr>';
		$table	 .= '<td class="price-label">' . $single_names[ 'product_price' ] . '</td>';
		$table	 .= '<td class="price-value">' . price_format( $price_value ) . '</td>';
		$table	 .= '</tr>';
		ob_start();
		do_action( 'price_table' );
		$table	 .= ob_get_clean();
		$table	 .= '</table>';
		ob_start();
		do_action( 'after_price_table' );
		$table	 .= ob_get_contents();
	}
	return $table;
}

add_filter( 'the_content', 'show_auto_price_field' );

/**
 * Shows price on certain post types
 *
 * @param type $content
 * @return type
 */
function show_auto_price_field( $content ) {
	if ( function_exists( 'is_ic_product_listing' ) && is_ic_product_listing() ) {
		return $content;
	}
	if ( is_ic_admin() ) {
		return $content;
	}
	$post_type				 = get_post_type();
	$price_show_post_type	 = get_price_show_active_post_types();
	if ( in_array( $post_type, $price_show_post_type ) ) {
		ob_start();
		ic_show_template_file( 'product-page/product-price.php', AL_PRICE_BASE_PATH );
		$content .= ob_get_clean();
		//$single_names	 = get_external_single_names();
		//$old			 = $content;
		//$content		 = get_price_field_table( get_the_ID(), $single_names );
		//$content .= $old;
	}
	return $content;
}
