<?php

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Manages image sizes settings
 *
 * Here image sizes settings are defined and managed.
 *
 * @version		1.1.4
 * @package		post-type-x/core/functions
 * @author 		impleCode
 */
class ic_extension_settings_info {

	function __construct() {
		//add_action( 'ic_simple_csv_bottom', array( $this, 'product_csv' ) );
		add_action( 'general_submenu', array( $this, 'extensions_info' ), 99 );
		add_filter( 'admin_product_details', array( $this, 'extensions_info_add' ), 99 );
	}

	function info_box( $content ) {
		$box = '<div class="extension-info-box">';
		//$box .= '<h4 style="margin-top: 0;">' . __( 'Did you know?', 'post-type-x' ) . '</h4>';
		$box .= $content;
		$box .= '</div>';
		return $box;
	}

	function product_csv() {
		if ( current_action() !== 'ic_plugin_logo_container' ) {
			add_action( 'ic_plugin_logo_container', array( $this, 'product_csv' ) );
		} else {
			$info = sprintf( __( 'With %sProduct CSV%s you can import, export and update unlimited number of products at once. It also supports all product fields and will import external images.', 'post-type-x' ), '<a href="https://implecode.com/wordpress/plugins/product-csv/?cam=extension-info&key=product-csv">', '</a>' );
			echo $this->info_box( $info );
		}
	}

	function extensions_info_add( $content ) {
		ob_start();
		$this->extensions_info();
		$content = ob_get_clean() . $content;
		return $content;
	}

	function extensions_info() {
		if ( !function_exists( 'start_implecode_updater' ) ) {
			echo '<span class="extensions-promo-box">' . sprintf( __( 'More free & premium features %shere%s.', 'post-type-x' ), '<a href="' . admin_url( 'edit.php?post_type=al_product&page=extensions.php' ) . '">', '</a>' ) . '</span>';
		}
	}

}

$ic_extension_settings_info = new ic_extension_settings_info;
