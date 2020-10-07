<?php

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Manages separate price settings
 *
 * Here price settings are defined and managed.
 *
 * @version		1.0.0
 * @package		price-field/sep
 * @author 		Norbert Dreszer
 */
add_action( 'admin_menu', 'register_price_field_settings_menu' );

/**
 * Adds price field submenu to WordPress Settings menu
 */
function register_price_field_settings_menu() {
	add_options_page( __( 'Price Field', 'price-field' ), __( 'Price Field', 'price-field' ), 'manage_options', 'ic_price', 'price_field_settings' );
}

add_action( 'admin_init', 'register_price_field_settings', 20 );

/**
 * Registers price field settings
 */
function register_price_field_settings() {
	register_setting( 'ic_price_field', 'price_field_settings' );
	if ( !defined( 'AL_BASE_PATH' ) ) {
		register_setting( 'product_settings', 'product_currency' );
		register_setting( 'product_settings', 'product_currency_settings' );
	}
}

/**
 * Sets default price field settings
 *
 * @return type
 */
function default_price_field_settings() {
	return array( 'enabled' => array( 'al_product' ), 'show' => array( '' ) );
}

/**
 * Returns price field settings
 *
 * @return type
 */
function get_price_field_settings() {
	$settings = wp_parse_args( get_option( 'price_field_settings' ), default_price_field_settings() );
	return $settings;
}

/**
 * Shows price field settings fields
 *
 */
function price_field_settings() {
	$post_types				 = get_post_types( array( 'publicly_queryable' => true ), 'objects' );
	unset( $post_types[ 'attachment' ] );
	echo '<h2>' . __( 'Settings', 'price-field' ) . ' - impleCode Price Field</h2>';
	echo '<h3>' . __( 'General Price Field Settings', 'price-field' ) . '</h3>';
	echo '<form method="post" action="options.php">';
	settings_fields( 'ic_price_field' );
	$price_field_settings	 = get_price_field_settings();
	echo '<h4>' . __( 'Enable Price Field for', 'price-field' ) . ':</h4>';
	$checked				 = in_array( 'page', $price_field_settings[ 'enabled' ] ) ? 'checked' : '';
	echo '<input ' . $checked . ' type="checkbox" name="price_field_settings[enabled][]" value="page"> ' . __( 'Pages', 'price-field' ) . '<br>';
	foreach ( $post_types as $type_key => $type_obj ) {
		if ( strpos( $type_key, 'al_product' ) !== 0 ) {
			$checked = in_array( $type_key, $price_field_settings[ 'enabled' ] ) ? 'checked' : '';
			echo '<input ' . $checked . ' type="checkbox" name="price_field_settings[enabled][]" value="' . $type_key . '"> ' . $type_obj->labels->name . '<br>';
		}
	}
	echo '<h4>' . __( 'Show Price Field Automatically on', 'price-field' ) . ':</h4>';
	$checked = in_array( 'page', $price_field_settings[ 'show' ] ) ? 'checked' : '';
	echo '<input ' . $checked . ' type="checkbox" name="price_field_settings[show][]" value="page"> ' . __( 'Pages', 'price-field' ) . '<br>';
	foreach ( $post_types as $type_key => $type_obj ) {
		if ( strpos( $type_key, 'al_product' ) !== 0 ) {
			$checked = in_array( $type_key, $price_field_settings[ 'show' ] ) ? 'checked' : '';
			echo '<input ' . $checked . ' type="checkbox" name="price_field_settings[show][]" value="' . $type_key . '"> ' . $type_obj->labels->name . '<br>';
		}
	}
	echo '<div class="al-box" style="margin-top: 10px;">' . __( 'You can also display price with', 'price-field' ) . ': <ol><li>' . sprintf( __( '%s shortcode placed in content.', 'price-field' ), '<code>' . esc_html( '[price_field]' ) . '</code>' ) . '</li><li>' . sprintf( __( '%s code placed in template file.', 'price-field' ), '<code>' . esc_html( '<?php ic_price_field() ?>' ) . '</code>' ) . '</li></ol></div>';
	echo '<p class="submit"><input type="submit" class="button-primary" value="' . __( 'Save changes', 'price-field' ) . '"/></p>';
	echo '</form>';
	if ( !defined( 'AL_BASE_PATH' ) ) {
		echo '<style>.al-box {max-width: 350px;padding: 10px;border: 1px solid;}.plugin-logo {
		position: absolute;
		right: 0px;
		bottom: 25px;
		z-index: 9999;
	}</style>';
		echo '<form method="post" action="options.php">';
		settings_fields( 'product_settings' );
		ic_price_settings();
		echo '<p class="submit"><input type="submit" class="button-primary" value="' . __( 'Save changes', 'price-field' ) . '"/></p>';
		echo '</form>';
	}
	echo '<div class="plugin-logo"><a href="https://implecode.com/#cam=price-field-settings&key=logo-link"><img class="en" src="' . AL_PRICE_BASE_URL . '/img/implecode.png' . '" width="282px" alt="impleCode" /></a></div>';
}
