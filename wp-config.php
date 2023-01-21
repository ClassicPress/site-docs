<?php
/**
 * The base configuration for ClassicPress
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
 * @package ClassicPress
 */

define( 'WP_HOME',    'https://staging-docs.classicpress.net' );
define( 'WP_SITEURL', 'https://staging-docs.classicpress.net' );

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for ClassicPress */
define('DB_NAME', 'stagingdocscpnet');

/** MySQL database username */
define('DB_USER', 'stagingdocscpnet');

/** MySQL database password */
define('DB_PASSWORD', 'qK7aZ3gC6yO7uB3j');

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
 * You can generate these using the {@link https://api.classicpress.net/secret-key/1.0/salt/ ClassicPress.net secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since WP-2.6.0
 */
define('AUTH_KEY',         '[a}eGbhvH*&^YHbWBi0;/2`&WI^NQU}R2/dZ{t<BI!O~_](IG)Kj3 .jk<PSxYrr');
define('SECURE_AUTH_KEY',  'P$cr|<y8/]#r*IcV)SZ*(r)XplyCxOjYYjz@T>t|TFywIIpK!YH&+b]c^l[AUGSz');
define('LOGGED_IN_KEY',    'fh=8[$VqMF~B9**.j -OE(Y:.QN*vl(^V%pRQJF*7|=Vt]G`H=IO6hoL3^xvW5Wq');
define('NONCE_KEY',        'dTId-(U*;h3mt=&YYMrB=_(*wm{LlK6$%,Xf)^Bw7lMT6<3BKyL/K2|>qL?TTze!');
define('AUTH_SALT',        'NYSNf&hj`oS{o5@wY ;&3>cJ mY(}FgI)<3}])CJxx8 6~0<?&uk~dbEIH[u+lm,');
define('SECURE_AUTH_SALT', '[g{Ny,.w<ucYG..TExl?<s`@VSL9qU*D?~(Yp5`/WQ]KgC,7F/f=Vis7ARbi=Xw{');
define('LOGGED_IN_SALT',   'D.)Ye)!a8*ZS_VOq$J9`Qc4I>Z}kD&@H[r9SY-;m>Tm_W;,ESsK~Wa}S.&~=U|d8');
define('NONCE_SALT',       'SR#)%v1?-=mMXkOXy6up^_Z(c<wsC8beJoK0D^dE%`DS$235h@HadOAU=W}*`Q4H');

/**#@-*/

/**
 * ClassicPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'cp_';

/**
 * For developers: ClassicPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the ClassicPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up ClassicPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
