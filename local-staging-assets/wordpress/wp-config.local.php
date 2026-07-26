<?php
declare(strict_types=1);

if (!defined('WP_ENVIRONMENT_TYPE')) {
    define('WP_ENVIRONMENT_TYPE', 'local');
}

if (!defined('WP_DEBUG')) {
    define('WP_DEBUG', true);
}

if (!defined('WP_DEBUG_LOG')) {
    define('WP_DEBUG_LOG', true);
}

if (!defined('WP_DEBUG_DISPLAY')) {
    define('WP_DEBUG_DISPLAY', false);
}

if (!defined('DISABLE_WP_CRON')) {
    define('DISABLE_WP_CRON', true);
}

if (!defined('ALTERNATE_WP_CRON')) {
    define('ALTERNATE_WP_CRON', false);
}

if (!defined('AUTOMATIC_UPDATER_DISABLED')) {
    define('AUTOMATIC_UPDATER_DISABLED', true);
}

if (!defined('DISALLOW_FILE_MODS')) {
    define('DISALLOW_FILE_MODS', true);
}

if (!defined('WP_AUTO_UPDATE_CORE')) {
    define('WP_AUTO_UPDATE_CORE', false);
}

if (!defined('WP_HTTP_BLOCK_EXTERNAL')) {
    define('WP_HTTP_BLOCK_EXTERNAL', true);
}

if (!defined('WP_ACCESSIBLE_HOSTS')) {
    define('WP_ACCESSIBLE_HOSTS', '127.0.0.1,localhost,fotomoto.local');
}

if (!defined('FORCE_SSL_ADMIN')) {
    define('FORCE_SSL_ADMIN', false);
}

if (!defined('WP_HOME')) {
    define('WP_HOME', 'http://fotomoto.local');
}

if (!defined('WP_SITEURL')) {
    define('WP_SITEURL', 'http://fotomoto.local');
}

$_SERVER['HTTPS'] = 'off';
