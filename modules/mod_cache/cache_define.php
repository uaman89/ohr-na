<?php
if (!defined("SITE_PATH")) define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] );

include_once( SITE_PATH.'/include/defines.php' );
include_once( SITE_PATH.'/modules/mod_cache/garbagecollect.php' );
include_once( SITE_PATH.'/modules/mod_cache/cache.class.php' );
include_once( SITE_PATH.'/modules/mod_cache/file_cache.class.php' );
include_once( SITE_PATH.'/modules/mod_cache/arithmetic.class.php' );
include_once( SITE_PATH.'/modules/mod_cache/memcache.class.php' );

?>
