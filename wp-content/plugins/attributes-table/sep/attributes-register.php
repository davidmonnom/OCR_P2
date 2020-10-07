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
 * @package        attributes-table/includes
 * @author        Norbert Dreszer
 */
add_action( 'add_meta_boxes', 'add_attributes_table_metaboxes' );

/**
 * Hook attributes meta box to selected post types
 *
 */
function add_attributes_table_metaboxes() {
	$post_types = get_attributes_active_post_types();
	foreach ( $post_types as $post_type ) {
		add_action( 'add_meta_boxes_' . $post_type, 'add_attributes_table_metabox' );
	}
}

/**
 * Add attributes meta box
 *
 * @param type $post
 */
function add_attributes_table_metabox( $post ) {
	add_meta_box( 'al_product_attributes', __( 'Attributes', 'attributes-table' ), 'al_product_attributes', $post->post_type, 'normal', 'default' );
}

add_action( 'post_updated', 'ic_save_attributes_meta', 1, 2 );

/**
 * Save attributes meta field
 *
 * @param type $post_id
 * @param type $post
 * @return type
 */
function ic_save_attributes_meta( $post_id, $post ) {
	$post_types = get_attributes_active_post_types();
	if ( in_array( $post->post_type, $post_types ) ) {
		$attributesmeta_noncename = isset( $_POST[ 'attributesmeta_noncename' ] ) ? $_POST[ 'attributesmeta_noncename' ] : '';
		if ( !empty( $attributesmeta_noncename ) && !wp_verify_nonce( $attributesmeta_noncename, AL_BASE_PATH . 'attributes_meta' ) ) {
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

		$count = product_attributes_number();
		for ( $i = 1; $i <= $count; $i++ ) {
			$attributes_meta[ '_attribute' . $i ]		 = !empty( $_POST[ '_attribute' . $i ] ) ? $_POST[ '_attribute' . $i ] : '';
			$attributes_meta[ '_attribute-label' . $i ]	 = !empty( $_POST[ '_attribute-label' . $i ] ) ? $_POST[ '_attribute-label' . $i ] : '';
			$attributes_meta[ '_attribute-unit' . $i ]	 = !empty( $_POST[ '_attribute-unit' . $i ] ) ? $_POST[ '_attribute-unit' . $i ] : '';
		}
		foreach ( $attributes_meta as $key => $value ) {
			$current_value = get_post_meta( $post->ID, $key, true );
			if ( isset( $value ) && !isset( $current_value ) ) {
				add_post_meta( $post->ID, $key, $value, true );
			} else if ( isset( $value ) && $value != $current_value ) {
				update_post_meta( $post->ID, $key, $value );
			} else if ( !isset( $value ) && $current_value ) {
				delete_post_meta( $post->ID, $key );
			}
		}
		ic_assign_product_attributes( $attributes_meta, $post );
	}
}

add_filter( 'ic_attributes_register_post_types', 'ic_attributes_sep_post_types' );

/**
 * Adds additional post types to register attributes taxonomy
 * 
 * @param type $post_types
 * @return type
 */
function ic_attributes_sep_post_types( $post_types ) {
	$attr_post_types = get_attributes_active_post_types();
	$post_types		 = array_merge( $post_types, $attr_post_types );
	return $post_types;
}
