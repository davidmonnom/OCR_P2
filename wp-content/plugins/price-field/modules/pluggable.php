<?php

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * Manages product includes folder
 *
 * Here all plugin includes folder is defined and managed.
 *
 * @version		1.0.0
 * @package		price-field/price
 * @author 		Norbert Dreszer
 */
if ( !function_exists( 'ic_save_global' ) ) {

	/**
	 * Saves implecode global
	 *
	 * @global array $implecode
	 * @param string $name
	 * @param type $value
	 * @return boolean
	 */
	function ic_save_global( $name, $value ) {
		global $implecode;
		if ( !empty( $name ) ) {
			$implecode[ $name ] = $value;
			return true;
		}
		return false;
	}

}

if ( !function_exists( 'ic_delete_global' ) ) {

	/**
	 * Deletes implecode global
	 *
	 * @global type $implecode
	 * @param type $name
	 * @return string
	 */
	function ic_delete_global( $name = null ) {
		global $implecode;
		if ( !empty( $name ) ) {
			unset( $implecode[ $name ] );
		} else {
			unset( $implecode );
		}
	}

}

if ( !function_exists( 'ic_get_global' ) ) {

	/**
	 * Returns implecode global
	 *
	 * @global type $implecode
	 * @param type $name
	 * @return string
	 */
	function ic_get_global( $name = null ) {
		global $implecode;
		if ( !empty( $name ) ) {
			if ( isset( $implecode[ $name ] ) ) {
				return $implecode[ $name ];
			} else {
				return false;
			}
		} else {
			return $implecode;
		}
	}

}

if ( !function_exists( 'ic_get_template_file' ) ) {

	/**
	 * Manages template files paths
	 *
	 * @param type $file_path
	 * @return type
	 */
	function ic_get_template_file( $file_path, $base_path = AL_BASE_TEMPLATES_PATH ) {
		$folder		 = get_custom_templates_folder();
		$file_name	 = basename( $file_path );
		if ( file_exists( $folder . $file_name ) ) {
			return $folder . $file_name;
		} else if ( file_exists( $base_path . '/templates/template-parts/' . $file_path ) ) {
			return $base_path . '/templates/template-parts/' . $file_path;
		} else {
			return false;
		}
	}

}

if ( !function_exists( 'ic_show_template_file' ) ) {

	/**
	 * Includes template file
	 *
	 * @param type $file_path
	 * @return type
	 */
	function ic_show_template_file( $file_path, $base_path = AL_BASE_TEMPLATES_PATH ) {
		$path = ic_get_template_file( $file_path, $base_path );
		if ( $path ) {
			include $path;
		}
		return;
	}

}

if ( !function_exists( 'get_custom_templates_folder' ) ) {

	/**
	 * Returns custom templates folder in theme directory
	 *
	 * @return type
	 */
	function get_custom_templates_folder() {
		return get_stylesheet_directory() . '/implecode/';
	}

}

if ( !function_exists( 'is_ic_new_product_screen' ) ) {

	/**
	 * Checks if new entry screen is being displayed
	 *
	 * @return boolean
	 */
	function is_ic_new_product_screen() {
		if ( is_admin() ) {
			$screen = get_current_screen();
			if ( $screen->action == 'add' ) {
				return true;
			}
		}
		return false;
	}

}

if ( !function_exists( 'is_ic_ajax' ) ) {

	function is_ic_ajax( $action = null ) {
		if ( !is_admin() ) {
			return false;
		}

		$return = false;
		if ( function_exists( 'wp_doing_ajax' ) ) {
			$doing = wp_doing_ajax();
			if ( $doing ) {
				$return = true;
			}
		} else if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			$return = true;
		}
		if ( $return && isset( $_POST[ 'action' ] ) && $_POST[ 'action' ] === 'heartbeat' ) {
			$return = false;
		}
		if ( $return && !empty( $action ) ) {
			if ( !isset( $_POST[ 'action' ] ) || ( isset( $_POST[ 'action' ] ) && $_POST[ 'action' ] !== $action) ) {
				$return = false;
			}
		}
		return $return;
	}

}

if ( !function_exists( 'is_ic_admin' ) ) {

	function is_ic_admin() {
		if ( is_admin() && !is_ic_ajax() ) {
			return true;
		}
		return false;
	}

}
