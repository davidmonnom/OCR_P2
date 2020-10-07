<?php
define('WP_CACHE', true); // WP-Optimize Cache
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */
// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wordpress' );
/** MySQL database username */
define( 'DB_USER', 'wordpress' );
/** MySQL database password */
define( 'DB_PASSWORD', 'Poisson01' );
/** MySQL hostname */
define( 'DB_HOST', 'localhost' );
/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );
/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );
/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '=D~8I.D0%1d}D;,ff-`JS.m@@wMMfAt= 5G}iFYG|<Fh0Js6g7*5IGg%:-]:PD_o' );
define( 'SECURE_AUTH_KEY',  'q)P>CEZqCIwK_5<Q,0SaF-OY4mSW_zv@h>()r dD]<4}.vBUr)E%?0<;v1lErtP#' );
define( 'LOGGED_IN_KEY',    'Rn7pLPlV8:Pi^h.bi:>MfH{^k`2.6Ge$%,/3@m?uGgs@-0rX_$E(8msKuc,K]jTe' );
define( 'NONCE_KEY',        'PmL7E>vR@@V]Lr)sfFu.wHJzS#@Mf7~ICH0vIl51&%R)Z&dJAm?`%c(p8lzR.2h`' );
define( 'AUTH_SALT',        '<5v_Vere(N%:0(/PTses|u{p 2ASA<~E#/tRv??%T;.EJ7ub91%OnbaETWqA@j:,' );
define( 'SECURE_AUTH_SALT', 'U;Nh]z:3U[ vCJf7CAna4e0q{oYL9cMzR+c5WuNqoMvg%)5>uCv!0-`zvcetd(){' );
define( 'LOGGED_IN_SALT',   '# pO$VW$JJ:w|?bw>8rJSI]2?B[zh0OhoWCE4kxfuh/T]I-yT#3vvpsggA^96c?L' );
define( 'NONCE_SALT',       '<ZPe1;~4+zI:9w)=GaXH/9&3~>gc2?P#HF1UIiEf`9if|e)AUMuU`&Pm{?RpArpW' );
/**#@-*/
/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';
/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define('WP_HOME','https://wordpress.fludy.net');
define('WP_SITEURL','https://wordpress.fludy.net');
define( 'WP_DEBUG', false );
define('WP_TEMP_DIR', dirname(__FILE__) . '/wp-content/temp/');
/* That's all, stop editing! Happy publishing. */
/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}
/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
