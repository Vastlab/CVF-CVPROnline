<?php

/**/
/** Enable W3 Total Cache */

 // Added by W3 Total Cache




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


define('ALTERNATE_WP_CRON', true);

// ** MySQL settings - You can get this info from your web host ** //

/** The name of the database for WordPress */

define('WP_CACHE', true);
define( 'WPCACHEHOME', '/var/www/html/wp-content/plugins/wp-super-cache/' );
define('DB_NAME', "CVPR20");


/** MySQL database username */

define('DB_USER', "CVPRUSER");


/** MySQL database password */

define('DB_PASSWORD', "Its4CVPR20");




/** Database Charset to use in creating database tables. */

define('DB_CHARSET', 'utf8');


/** The Database Collate type. Don't change this if in doubt. */

define('DB_COLLATE', '');


define('FS_METHOD', 'direct');

/** MySQL hostname and split read/rewite  info  */

define('DB_HOST', "localhost");

define('DB_WRITE_HOST','10.2.0.122');
define('WP_HOME','http://cvpr20.com/');
define('WP_SITEURL','http://cvpr20.com/');
//define('WP_HOME','http://cvpr20.com');
//define('WP_SITEURL','http://cvpr20.com');
define('DISABLE_WP_CRON', true);


/**#@+

 * Authentication Unique Keys and Salts.

 *

 * Change these to different unique phrases!

 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}

 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.

 *

 * @since 2.6.0

 */

define('AUTH_KEY',         'RgN?vjk!O9sE7Z]vAkHcmv[=pH]ug74a!?Snw![iUNc]0WQ1h18l.E&S`fk|u?Yz');

define('SECURE_AUTH_KEY',  'cSz(-voe fAkB,9])e()PQlYr@:>9av46TI@P.OW@_mFFGC*^J?7A<%;f|ix-/tv');

define('LOGGED_IN_KEY',    'rY[n-xCxHX{L_zMEY!8=L5kQ nuNV+H-^7{N+Hm<zeS)?Fx?J@DMgSgmXoKu6v#]');

define('NONCE_KEY',        '3*qYDETvYPtl<deKWJx/CVjh++QHD=5Ix:o0Rs}n<AMKd&q%k3LDD`=hufHH/;O/');

define('AUTH_SALT',        '<=]+{sFowLj ?tKVCAMKy_f|4HC)I%;5,3bo}82@$uEb%0onU q<{=*RTpN[vC)U');

define('SECURE_AUTH_SALT', '6RgprkRl(fatB|[frz=b@`jI:}3O-Ht_&1d=E&Y70cG_(oV!a0S%j;Zqqh>U%GXb');

define('LOGGED_IN_SALT',   'k ~*6{k<dv>=bf1Gkts_~qK  -s*Cqdr0~i/gD6=L+{hS%CF0#&(a/*eKM]+-zur');

define('NONCE_SALT',       '/~s<^4GHo?Qo2zAG[Ff(oD6;)/FY.O30`.|=|sMVo-_}=H0Li}333~xWmmn}<C?g');

/**#@-*/


/**

 * WordPress Database Table prefix.

 *

 * You can have multiple installations in one database if you give each

 * a unique prefix. Only numbers, letters, and underscores please!

 */

$table_prefix  = 'wp_';


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

//#define('WP_DEBUG', true);
//#define('WP_SCRIPT_DEBUG', true);
//#define('WP_DEBUG_LOG', 'wp-debug.log' );

/* That's all, stop editing! Happy blogging. */


/** Absolute path to the WordPress directory. */

if ( !defined('ABSPATH') )

	define('ABSPATH', dirname(__FILE__) . '/');

@ini_set( 'memory_limit', '64M' );
@ini_set('php_value max_execution_time', '30' );

define('ALLOW_UNFILTERED_UPLOADS', true);
/** Sets up WordPress vars and included files. */


//define( 'SAVEQUERIES', true );
// Enable WP_DEBUG mode
define('WP_DEBUG', true);
// Enable Debug logging to the /wp-content/debug.log file
define('WP_DEBUG_LOG', true);
// Disable display of errors and warnings
define('WP_DEBUG_DISPLAY', true);
//@ini_set('display_errors',0); // false
//define( 'SCRIPT_DEBUG', true );

require_once(ABSPATH . 'wp-settings.php');

