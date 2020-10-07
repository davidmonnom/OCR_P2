<?php

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Manages separate attributes settings
 *
 * Here attributes settings are defined and managed.
 *
 * @version		1.0.0
 * @package		attributes-table/sep
 * @author 		Norbert Dreszer
 */
add_action( 'admin_menu', 'register_attributes_table_settings_menu' );

/**
 * Adds attributes field submenu to WordPress Settings menu
 */
function register_attributes_table_settings_menu() {
	add_options_page( __( 'Attributes Table', 'attributes-table' ), __( 'Attributes Table', 'attributes-table' ), 'manage_options', 'ic_attributes', 'attributes_table_settings' );
}

add_action( 'admin_init', 'register_attributes_table_settings', 20 );

/**
 * Registers attributes field settings
 */
function register_attributes_table_settings() {
	register_setting( 'ic_attributes_table', 'attributes_table_settings' );
	if ( !defined( 'AL_BASE_PATH' ) ) {
		register_setting( 'product_attributes', 'product_attributes_number' );
		register_setting( 'product_attributes', 'al_display_attributes' );
		register_setting( 'product_attributes', 'product_attribute' );
		register_setting( 'product_attributes', 'product_attribute_label' );
		register_setting( 'product_attributes', 'product_attribute_unit' );
	}
}

/**
 * Sets default attributes field settings
 *
 * @return type
 */
function default_attributes_table_settings() {
	return array( 'enabled' => array( 'al_product' ), 'show' => array( '' ) );
}

/**
 * Returns attributes field settings
 *
 * @return type
 */
function get_attributes_table_settings() {
	$settings = wp_parse_args( get_option( 'attributes_table_settings' ), default_attributes_table_settings() );
	return $settings;
}

/**
 * Shows attributes field settings fields
 *
 */
function attributes_table_settings() {
	$post_types					 = get_post_types( array( 'publicly_queryable' => true ), 'objects' );
	unset( $post_types[ 'attachment' ] );
	echo '<h2>' . __( 'Settings', 'attributes-table' ) . ' - impleCode Attributes Table</h2>';
	echo '<h3>' . __( 'General Attributes Table Settings', 'attributes-table' ) . '</h3>';
	echo '<form method="post" action="options.php">';
	settings_fields( 'ic_attributes_table' );
	$attributes_table_settings	 = get_attributes_table_settings();
	echo '<h4>' . __( 'Enable Attributes for', 'attributes-table' ) . ':</h4>';
	$checked					 = in_array( 'page', $attributes_table_settings[ 'enabled' ] ) ? 'checked' : '';
	echo '<input ' . $checked . ' type="checkbox" name="attributes_table_settings[enabled][]" value="page"> ' . __( 'Pages', 'attributes-table' ) . '<br>';
	foreach ( $post_types as $type_key => $type_obj ) {
		if ( strpos( $type_key, 'al_product' ) !== 0 ) {
			$checked = in_array( $type_key, $attributes_table_settings[ 'enabled' ] ) ? 'checked' : '';
			echo '<input ' . $checked . ' type="checkbox" name="attributes_table_settings[enabled][]" value="' . $type_key . '"> ' . $type_obj->labels->name . '<br>';
		}
	}
	echo '<h4>' . __( 'Show Attributes Automatically on', 'attributes-table' ) . ':</h4>';
	$checked = in_array( 'page', $attributes_table_settings[ 'show' ] ) ? 'checked' : '';
	echo '<input ' . $checked . ' type="checkbox" name="attributes_table_settings[show][]" value="page"> ' . __( 'Pages', 'attributes-table' ) . '<br>';
	foreach ( $post_types as $type_key => $type_obj ) {
		if ( strpos( $type_key, 'al_product' ) !== 0 ) {
			$checked = in_array( $type_key, $attributes_table_settings[ 'show' ] ) ? 'checked' : '';
			echo '<input ' . $checked . ' type="checkbox" name="attributes_table_settings[show][]" value="' . $type_key . '"> ' . $type_obj->labels->name . '<br>';
		}
	}
	echo '<div class="al-box" style="margin-top: 10px;">' . __( 'You can also display attributes with', 'attributes-table' ) . ': <ol><li>' . sprintf( __( '%s shortcode placed in content.', 'attributes-table' ), '<code>' . esc_html( '[attributes_table]' ) . '</code>' ) . '</li><li>' . sprintf( __( '%s code placed in template file.', 'attributes-table' ), '<code>' . esc_html( '<?php ic_attributes_table() ?>' ) . '</code>' ) . '</li></ol></div>';
	echo '<p class="submit"><input type="submit" class="button-primary" value="' . __( 'Save changes', 'attributes-table' ) . '"/></p>';
	echo '</form>';
	if ( !defined( 'AL_BASE_PATH' ) ) {
		echo '<style>.al-box {max-width: 350px;padding: 10px;border: 1px solid;}.plugin-logo {
		position: absolute;
		right: 0px;
		bottom: 25px;
		z-index: 9999;
	} .product-settings-table {width: auto;} .al-box.info {margin: 10px 0;} #admin-number-field {max-width: 60px}</style>';
		$info = __( 'The table below controls the attributes default values.', 'attributes-table' );
		attributes_settings_fields( $info );
	}
	echo '<div class="plugin-logo"><a href="https://implecode.com/#cam=attributes-table-settings&key=logo-link"><img class="en" src="' . AL_ATTRIBUTES_BASE_URL . '/img/implecode.png' . '" width="282px" alt="impleCode" /></a></div>';
}
