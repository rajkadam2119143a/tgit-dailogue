<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

 $_SERVER['HTTPS']='on';
 @ini_set('session.cookie_httponly', true); 
 @ini_set('session.cookie_secure', true); 
 @ini_set('session.use_only_cookies', true);
 

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'dialogue' );

/** Database username */
define( 'DB_USER', 'tgituser' );

/** Database password */
define( 'DB_PASSWORD', 'Tg!tu$er' );

/** Database hostname */
define( 'DB_HOST', '10.200.105.106:3309' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '*>Zz1[RB8@;ePU)*FlJ`5un,_C+6Winu?<|#^cf?|#lWNq~2N{tAHp1w7O8(8/$8' );
define( 'SECURE_AUTH_KEY',  '~yeyPyw[-mK;9[r=jW&1pfD|(~Zir48%l2/fXVDDQ?b3TADa~[d]yB03rDCneq5I' );
define( 'LOGGED_IN_KEY',    'T23|yA|c3X`dYE*Yk<<z^58){fL@m<M[w([FGPuWX*@GiX-; :-Uq7YsI&}E^DgL' );
define( 'NONCE_KEY',        'dYeT+xL7:deYFl!p7U~=egH!V1),cf]x%/(4-Wd&3Vq?!4S-~0V.5yLlhv{RpeIb' );
define( 'AUTH_SALT',        ';R[H%TdSWm:ZbbnrR$XM0QV>!,*B{xTSB|5_?S{HEQyoGvSjOGPFKSg*3P.(3Y6I' );
define( 'SECURE_AUTH_SALT', '|/Nm4~>b+!s@lradV-E,( qyX>O)S7G=9b|V,+<_,. ]m<9j7*oHWFO]ywR+X5Fe' );
define( 'LOGGED_IN_SALT',   'D$SI#liq&]x>C8$j,s5KM}>LfW[EbX!?O80Lb|~7rM3]sJ([>*&pNd6<:kmGmn~!' );
define( 'NONCE_SALT',       '~hBFtCCEOR36|v&Ikj3^<(c|bBZR{Hy`ooRe}G!2HY:wcMXqp5&RNS4V[sUY0nTg' );
define('AUTHENTICATION_API','https://timescape.timesofindia.com/timescape/api/external/authenticateUser');
define('RESPONSE_API','https://timescape.timesofindia.com/timescape/api/external/checkLoginCookie2');
define('MANAGER_API','https://timescape.timesofindia.com/timescape/api/leave/authorities');
/**#@-*/


/**#@-*/

define( 'API_ORIGIN_NAME', 'https://zing.timesofindia.com' );		
define('ALL_ADMIN', '');

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
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
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', true );
 @ini_set('display_errors', 1);
 define('WP_DEBUG_DISPLAY', true);


/* Add any custom values between this line and the "stop editing" line. */

define('FS_METHOD', 'direct');


/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
