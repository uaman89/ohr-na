<?php
// ================================================================================================
// System : SEOCMS
// Module : catalog_stat.class.php
// Version : 1.0.0
// Date : 06.08.2007
// Licensed To:
// Igor Trokhymchuk ihoru@mail.ru
// Andriy Lykhodid las_zt@mail.ru
//
// Purpose : Class definition for all actions with managment of Catalog Sttictic
//
// ================================================================================================

include_once( SITE_PATH.'/modules/mod_catalog/catalog.defines.php' );

// ================================================================================================
//    Class             : Catalog_Stat
//    Version           : 1.0.0
//    Date              : 06.08.2007
//
//    Constructor       : Yes
//    Parms             : session_id / session id
//                        usre_id    / UserID
//                        user_      /
//                        user_type  / id of group of user
//    Returns           : None                                               
//    Description       : Class definition for all actions with managment of Catalog Sttictic  
// ================================================================================================
//    Programmer        :  Igor Trokhymchuk
//    Date              :  06.08.2007 
//    Reason for change :  Creation
//    Change Request Nbr:  N/A
// ================================================================================================
 class Catalog_Stat extends Catalog {

       var $user_id = NULL;
       var $module = NULL;
       var $Err = NULL;
       var $lang_id = _LANG_ID;
       
       var $sort = NULL;
       var $display = 20;
       var $start = 0;
       var $fln = NULL;
       var $width = 500;
       var $srch = NULL;
       var $fltr = NULL;
       var $fltr2 = NULL;
       var $fltr3 = NULL;
       var $script = NULL;
       var $parent_script = NULL; 

       var $db = NULL;
       var $Msg = NULL;
       var $Right = NULL;
       var $Form = NULL;
       var $Spr = NULL;

       // ================================================================================================
       //    Function          : Catalog_Stat (Constructor)
       //    Version           : 1.0.0
       //    Date              : 06.08.2007
       //    Parms             : usre_id   / User ID
       //                        module    / module ID
       //                        sort      / field by whith data will be sorted
       //                        display   / count of records for show
       //                        start     / first records for show
       //                        width     / width of the table in with all data show
       //    Returns           : Error Indicator
       //
       //    Description       : Opens and selects a dabase
       // ================================================================================================
       function Catalog_Stat ($user_id=NULL, $module=NULL, $display=NULL, $sort=NULL, $start=NULL, $width = NULL) {
                //Check if Constants are overrulled
                ( $user_id   !="" ? $this->user_id = $user_id  : $this->user_id = NULL );
                ( $module   !="" ? $this->module  = $module   : $this->module  = NULL );
                ( $display  !="" ? $this->display = $display  : $this->display = 20   );
                ( $sort     !="" ? $this->sort    = $sort     : $this->sort    = NULL );
                ( $start    !="" ? $this->start   = $start    : $this->start   = 0    );
                ( $width    !="" ? $this->width   = $width    : $this->width   = 750  );
                
                $this-> lang_id = _LANG_ID;

                if (empty($this->db)) $this->db = new DB();
                if (empty($this->Right)) $this->Right = new Rights($this->user_id, $this->module);
                if (empty($this->Msg)) $this->Msg = new ShowMsg();
                $this->Msg->SetShowTable(TblModCatalogSprTxt);
                if (empty($this->Form)) $this->Form = new Form('form_mod_catalog');
                if (empty($this->Spr)) $this->Spr = new  SysSpr($this->user_id, $this->module);
       } // End of Catalog_Stat Constructor
 } //end of class Catalog_Stat