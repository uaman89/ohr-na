<?php
include_once( SITE_PATH.'/sys/classes/view.php' );
include_once( SITE_PATH.'/sys/classes/arr.php' );
include_once( SITE_PATH.'/sys/classes/date.php' );
include_once( SITE_PATH.'/sys/classes/utf8.php' );
include_once( SITE_PATH.'/sys/classes/url.php' );
include_once( SITE_PATH.'/sys/classes/html.php' );
include_once( SITE_PATH.'/sys/classes/form.php' );
include_once( SITE_PATH.'/sys/classes/image.php' );
include_once( SITE_PATH.'/sys/classes/image_driver/Image_GD.php' );
include_once( SITE_PATH.'/sys/classes/exeption.php' );

include_once( SITE_PATH.'/sys/classes/sysBase.php' );
include_once( SITE_PATH.'/sys/classes/jqueryUi.php' );
include_once( SITE_PATH.'/sys/classes/antihacker.class.php' );
include_once( SITE_PATH.'/sys/classes/sysShowMsg.class.php' );
include_once( SITE_PATH.'/sys/classes/sysDatabase.class.php' );
include_once( SITE_PATH.'/sys/classes/sysDatabase.single.class.php' );
include_once( SITE_PATH.'/sys/classes/sysPDOsingle.class.php' );
include_once( SITE_PATH.'/sys/classes/sysRights.class.php' );
include_once( SITE_PATH.'/sys/classes/sysRightOld.class.php' );
include_once( SITE_PATH.'/sys/classes/sysCalendar.class.php' );
include_once( SITE_PATH.'/sys/classes/sysForm.class.php' ); 
include_once( SITE_PATH.'/sys/classes/sysImage.class.php' );
include_once( SITE_PATH.'/sys/classes/sysSpr.class.php' );
include_once( SITE_PATH.'/sys/classes/sysUser.class.php' );   
include_once( SITE_PATH.'/sys/classes/sysAuthorize.class.php' );  
include_once( SITE_PATH.'/sys/classes/sysCrypt.class.php' );
include_once( SITE_PATH.'/sys/classes/sysDate.class.php' );
include_once( SITE_PATH.'/sys/classes/sysLang.class.php' );    
include_once( SITE_PATH.'/sys/classes/sysThumbnail.class.php' ); 
include_once( SITE_PATH.'/sys/classes/sysSettings.class.php' );  
include_once( SITE_PATH.'/sys/classes/sysCurrencies.class.php' ); 
include_once( SITE_PATH.'/sys/classes/sysTags.class.php' );
include_once( SITE_PATH.'/sys/classes/sysComments.class.php' );
include_once( SITE_PATH.'/admin/modules/sys_user/sys_user.class.php' );

//============ Images default settings START ===============
if(!defined("MAX_IMAGE_WIDTH")) define("MAX_IMAGE_WIDTH","5000");
if(!defined("MAX_IMAGE_HEIGHT")) define("MAX_IMAGE_HEIGHT","5000");
if(!defined("STORE_IMAGE_WIDTH")) define("STORE_IMAGE_WIDTH","2500");
if(!defined("STORE_IMAGE_HEIGHT")) define("STORE_IMAGE_HEIGHT","2500");
if(!defined("MAX_IMAGE_SIZE")) define("MAX_IMAGE_SIZE",8182 * 1024);
if(!defined("UPLOAD_IMAGES_COUNT")) define("UPLOAD_IMAGES_COUNT", 5);
if(!defined("MAX_UPLOAD_IMAGES_COUNT")) define("MAX_UPLOAD_IMAGES_COUNT", 50);
if(!defined("MAX_IMAGES_QUANTITY")) define("MAX_IMAGES_QUANTITY","85");
if(!defined("ADDITIONAL_FILES_TEXT")) define("ADDITIONAL_FILES_TEXT","_cmszoom_");
if(!defined("WATERMARK_IMG_PATH")) define("WATERMARK_IMG_PATH", "/images/design/watermark.png");
if(!defined("WATERMARK_TEXT")) define("WATERMARK_TEXT","");
if(!defined("DEFAULT_NO_IMAGE_PATH")) define("DEFAULT_NO_IMAGE_PATH", "/images/design/no-image.jpg");
//============ Images default settings END ===============
define("TblModUploadImg","mod_pages_uploads_img");
define("TblModUploadImgSpr","mod_pages_uploads_img_spr");
define("TblModUploadFiles","mod_pages_uploads_files");
define("TblModUploadFilesSpr","mod_pages_uploads_files_spr");
?>