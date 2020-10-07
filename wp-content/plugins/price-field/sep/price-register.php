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
 * @package        price-field/includes
 * @author        Norbert Dreszer
 */
add_action( 'add_meta_boxes', 'add_price_field_metaboxes' );

/**
 * Hook price meta box to selected post types
 *
 */
function add_price_field_metaboxes() {
	$post_types = get_price_active_post_types();
	foreach ( $post_types as $post_type ) {
		add_action( 'add_meta_boxes_' . $post_type, 'add_price_field_metabox' );
	}
}

/**
 * Add price meta box
 *
 * @param type $post
 */
function add_price_field_metabox( $post ) {
	add_meta_box( 'al_product_price', __( 'Price', 'price-field' ), 'al_product_price', $post->post_type, 'side', 'default' );
}

if ( !function_exists( 'al_product_price' ) ) {

	/**
	 * Shows price meta box content
	 *
	 * @global type $post
	 */
	function al_product_price() {
		global $post;
		echo '<input type="hidden" name="pricemeta_noncename" id="pricemeta_noncename" value="' .
		wp_create_nonce( plugin_basename( __FILE__ ) ) . '" />';
		$price_table = '';
		echo apply_filters( 'admin_product_details', $price_table, $post->ID );
	}

}

add_action( 'post_updated', 'ic_save_price_meta', 1, 2 );

/**
 * Save price meta field
 *
 * @param type $post_id
 * @param type $post
 * @return type
 */
function ic_save_price_meta( $post_id, $post ) {
	$post_types = get_price_active_post_types();
	if ( in_array( $post->post_type, $post_types ) ) {
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
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return $post->ID;
		}
		if ( !current_user_can( 'edit_post', $post->ID ) )
			return $post->ID;

		$price_meta[ '_price' ] = !empty( $_POST[ '_price' ] ) ? $_POST[ '_price' ] : '';
		foreach ( $price_meta as $key => $value ) {
			$current_value = get_post_meta( $post->ID, $key, true );
			if ( isset( $value ) && !isset( $current_value ) ) {
				add_post_meta( $post->ID, $key, $value, true );
			} else if ( isset( $value ) && $value != $current_value ) {
				update_post_meta( $post->ID, $key, $value );
			} else if ( !isset( $value ) && $current_value ) {
				delete_post_meta( $post->ID, $key );
			}
		}
	}
}
