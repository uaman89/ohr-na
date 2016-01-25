<?php
// ================================================================================================
// System : SEOCMS
// Module : user.defines.php
// Version : 1.0.0
// Date : 06.01.2006
// Licensed To:
// Igor Trokhymchuk ihoru@mail.ru
// Andriy Lykhodid las_zt@mail.ru
//
// Purpose : All Definitions for module of External users
//
// ================================================================================================
include_once( SITE_PATH.'/include/defines.php' );
include_once( SITE_PATH.'/modules/mod_user/user.class.php' );
include_once( SITE_PATH.'/modules/mod_user/backend/user_ctrl.class.php' );
include_once( SITE_PATH.'/modules/mod_user/backend/userBlog_ctrl.class.php' );
include_once( SITE_PATH.'/modules/mod_user/userShow.class.php' );

define("MOD_USER", true);

define("TblModUser","mod_user");
define("TblModUserSprStatus","mod_user_spr_status");
define("TblModUserSprTxt","mod_user_spr_txt");
define("TblModUserBlog","mod_user_blog");

?>