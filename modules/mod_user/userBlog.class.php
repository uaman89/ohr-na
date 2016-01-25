<?
/* ================================================================================================
* System : CMS
* Module : userBlog.class.php
* Date : 03.05.2011
* Licensed To: Panarin Sergey 
* Purpose : Class definition For work with user Blog data
*================================================================================================
*/
include_once( SITE_PATH.'/modules/mod_user/user.defines.php' );

/* ================================================================================================
*    Class             : User
*    Date              : 22.02.2011
*    Constructor     : Yes
*    Parms            : session_id / session id
*                         user_id    / UserID
*    Returns           : None
*    Description       : Class definition For work with user Blog data
*    Programmer     :  Panarin Sergey 
* ================================================================================================
*/
 class userBlog extends User {
     var $db;
     
      private function __construct() { 
          if (empty($this->db))  $this->db = Singleton::getInstance('DB');
      }
 }
?>