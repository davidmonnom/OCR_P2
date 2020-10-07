<?php

/**
 * Plugin Name: Price Field
 * Plugin URI: https://implecode.com/wordpress/product-attributes/#cam=in-plugin-urls&key=plugin-url
 * Description: Add Price Field to posts, pages or any custom post type. Full integration with Post Type X.
 * Version: 1.1.12
 * Author: impleCode
 * Author URI: https://implecode.com/#cam=in-plugin-urls&key=author-url
 * Text Domain: price-field
 * Domain Path: /lang
  Copyright: 2020 impleCode.
  License: GNU General Public License v3.0
  License URI: http://www.gnu.org/licenses/gpl-3.0.html */
if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

define( 'AL_PRICE_BASE_PATH', dirname( __FILE__ ) );
define( 'AL_PRICE_BASE_URL', plugins_url( '/', __FILE__ ) );

if ( !(is_admin() && isset( $_GET[ 'action' ] ) && $_GET[ 'action' ] == 'activate' && isset( $_GET[ 'plugin' ] ) && ($_GET[ 'plugin' ] == 'ecommerce-product-catalog/ecommerce-product-catalog.php' || $_GET[ 'plugin' ] == 'post-type-x/post-type-x.php')) ) {
	add_action( 'post_type_x_addons', 'start_price_field', 5 );
	add_action( 'after_setup_theme', 'start_price_field', 16 );
}

function start_price_field() {
	load_plugin_textdomain( 'price-field', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
	if ( !defined( 'AL_BASE_PATH' ) || !function_exists( 'is_ic_price_enabled' ) ) {
		require_once(AL_PRICE_BASE_PATH . '/modules/index.php' );
	}
	require_once(AL_PRICE_BASE_PATH . '/sep/index.php' );
	remove_action( 'plugins_loaded', 'start_price_field', 16 );
}
