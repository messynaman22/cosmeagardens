<?php
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
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'cosmeagardens_latest');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '_D^pyMfF>b>aG7^ rXiG`1]ihGz4pl9Q!$iW-xX{R6f&<qBwB=#!f5`pg :`{DwP');
define('SECURE_AUTH_KEY',  ';y!$y4wo@61p{()][zM;#kxJztQ&grtsS_+/[@Ru=Uw|4jN%(n jKqr!GJboh^8v');
define('LOGGED_IN_KEY',    '@2iFK!J9}T,PK}[xnL:b#~9=A HqhRPIAu2jS#5K`UQP29Lr Z{J#]QLH{i?S#M?');
define('NONCE_KEY',        'LP-RCXY.*@szQLkQe6cC+,2Ws7]>#f!fcs=iL It=`w JiCxlj96^t nPDAX>U6s');
define('AUTH_SALT',        '4Gp`YBn!$4IoBO&J82 %B5&Dy]F*(-|YZ21BGCt;z1#zis%kj[>ziGZYsyVg(|$X');
define('SECURE_AUTH_SALT', '1MRmVk9)z%B6/BF]^gS,m4Ud3fj/f5JjG&4QZEmnBF]<-H7_C,w?x>[Zp]2,?l0v');
define('LOGGED_IN_SALT',   '=c!fWare[zMr5SL3RI w@e?KdZ1]cXVTu.Z,Ghh?(8A7sreANLTw|)D05|LGA&2z');
define('NONCE_SALT',       '^5gcjXm0s[dKL|rX~y?fO6?RZY!;7y=I1v_aNXz9YcpGuPy1:-HLe4A]8*x{>c/8');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'csmg_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
ini_set('display_errors','Off');
ini_set('error_reporting', E_ALL );
define('WP_DEBUG', false);
define('WP_DEBUG_DISPLAY', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
