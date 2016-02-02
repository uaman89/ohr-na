<?php
// ================================================================================================
// System : CMS
// Module : catalog_settings.class.php
// Date : 04.01.2011
// Licensed To:   Yaroslav Gyryn
// Purpose : Class definition for all actions with settings of catalog
// ================================================================================================

include_once( SITE_PATH.'/modules/mod_catalog/catalog.defines.php' );

// ================================================================================================
//    Class             : Catalog_settings
//    Date              : 04.01.2011
//    Constructor       : Yes
//    Parms             : session_id / session id
//                        usre_id    / UserID
//                        user_      /
//                        user_type  / id of group of user
//    Returns           : None
//    Description       : Class definition for all actions with managment of Catalog
//    Programmer        :  Yaroslav Gyryn
// ================================================================================================
 class Catalog_settings extends Catalog {

       // ================================================================================================
       //    Function          : Catalog_settings (Constructor)
       //    Date              : 04.01.2011
       //    Parms             : usre_id   / User ID
       //                        module    / module ID
       //                        sort      / field by whith data will be sorted
       //                        display   / count of records for show
       //                        start     / first records for show
       //                        width     / width of the table in with all data show
       //    Returns           : Error Indicator
       //    Description       : Opens and selects a dabase
       // ================================================================================================
       function Catalog_settings ($user_id=NULL, $module=NULL, $display=NULL, $sort=NULL, $start=NULL, $width = NULL) {
                //Check if Constants are overrulled
                ( $user_id   !="" ? $this->user_id = $user_id  : $this->user_id = NULL );
                ( $module   !="" ? $this->module  = $module   : $this->module  = NULL );
                ( $display  !="" ? $this->display = $display  : $this->display = 20   );
                ( $sort     !="" ? $this->sort    = $sort     : $this->sort    = NULL );
                ( $start    !="" ? $this->start   = $start    : $this->start   = 0    );
                ( $width    !="" ? $this->width   = $width    : $this->width   = 750  );

                if (empty($this->db)) $this->db = DBs::getInstance();
                if (empty($this->Right)) $this->Right = new Rights($this->user_id, $this->module);
                //if (empty($this->Msg)) $this->Msg = new ShowMsg();
                //$this->Msg->SetShowTable(TblModCatalogSprTxt);
                if (empty($this->Form)) $this->Form = new Form('form_mod_catalog');
                if (empty($this->Spr)) $this->Spr = new  SysSpr($this->user_id, $this->module);
                //if (empty($this->multi)) $this->multi = $this->Spr->GetMulti(TblModCatalogSprTxt);
                if (empty($this->multi)) $this->multi = check_init_txt('TblBackMulti',TblBackMulti);

                //$this->AddTable();

       } // End of Catalog_settings Constructor


       // ================================================================================================
       // Function : AddTable()
       // Date : 04.01.2011
       // Returns : true,false / Void
       // Description : Adding fields setting to table TblModCatalogSet
       // Programmer : Yaroslav Gyryn
       // ================================================================================================
       function AddTable()
       {
           // add field id_group to the table settings
           if ( !$this->db->IsFieldExist(TblModCatalogSet, "id_group") ) {
               $q = "ALTER table `".TblModCatalogSet."` ADD `id_group` SET( '0', '1' ) DEFAULT NULL ;";
               $res = $this->db->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;

               $q = "ALTER table `".TblModCatalogSet."` ADD INDEX ( `id_group` ) ;";
               $res = $this->db->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;
           }

           // add field name to the table settings
           if ( !$this->db->IsFieldExist(TblModCatalogSet, "name") ) {
               $q = "ALTER table `".TblModCatalogSet."` ADD `name` SET( '0', '1' ) DEFAULT NULL ;";
               $res = $this->db->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;

               $q = "ALTER table `".TblModCatalogSet."` ADD INDEX ( `name` ) ;";
               $res = $this->db->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;
           }

           // add field manufac to the table settings
           if ( !$this->db->IsFieldExist(TblModCatalogSet, "manufac") ) {
               $q = "ALTER table `".TblModCatalogSet."` ADD `manufac` SET( '0', '1' ) DEFAULT NULL ;";
               $res = $this->db->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;

               $q = "ALTER table `".TblModCatalogSet."` ADD INDEX ( `manufac` ) ;";
               $res = $this->db->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;
           }

           // add field files to the table settings
           if ( !$this->db->IsFieldExist(TblModCatalogSet, "files") ) {
               $q = "ALTER table `".TblModCatalogSet."` ADD `files` SET( '0', '1' ) DEFAULT NULL ;";
               $res = $this->db->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;

               $q = "ALTER table `".TblModCatalogSet."` ADD INDEX ( `files` ) ;";
               $res = $this->db->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;
           }

           // add field files to the table settings
           if ( !$this->db->IsFieldExist(TblModCatalogSet, "files_path") ) {
               $q = "ALTER table `mod_catalog_set` ADD `files_path` VARCHAR( 255 );";
               $res = $this->db->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;
           }

           // add field responses to the table settings
           if ( !$this->db->IsFieldExist(TblModCatalogSet, "responses") ) {
               $q = "ALTER table `".TblModCatalogSet."` ADD `responses` SET( '0', '1' ) DEFAULT NULL ;";
               $res = $this->db->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;

               $q = "ALTER table `".TblModCatalogSet."` ADD INDEX ( `responses` ) ;";
               $res = $this->db->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;
           }
           // add field responses to the table settings
           if ( !$this->db->IsFieldExist(TblModCatalogSet, "rating") ) {
               $q = "ALTER table `".TblModCatalogSet."` ADD `rating` SET( '0', '1' ) DEFAULT NULL ;";
               $res = $this->db->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;

               $q = "ALTER table `".TblModCatalogSet."` ADD INDEX ( `rating` ) ;";
               $res = $this->db->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;
           }
           // add field price_currency to the table settings
           if ( !$this->db->IsFieldExist(TblModCatalogSet, "price_currency") ) {
               $q = "ALTER table `".TblModCatalogSet."` ADD `price_currency` SET( '0', '1' ) DEFAULT NULL ;";
               $res = $this->db->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;

               $q = "ALTER table `".TblModCatalogSet."` ADD INDEX ( `price_currency` ) ;";
               $res = $this->db->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;
           }
           // add field opt_price_currency to the table settings
           if ( !$this->db->IsFieldExist(TblModCatalogSet, "opt_price_currency") ) {
               $q = "ALTER table `".TblModCatalogSet."` ADD `opt_price_currency` SET( '0', '1' ) DEFAULT NULL ;";
               $res = $this->db->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;

               $q = "ALTER table `".TblModCatalogSet."` ADD INDEX ( `opt_price_currency` ) ;";
               $res = $this->db->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;
           }
           // add field price_levels to the table settings
           if ( !$this->db->IsFieldExist(TblModCatalogSet, "price_levels") ) {
               $q = "ALTER table `".TblModCatalogSet."` ADD `price_levels` SET( '0', '1' ) DEFAULT NULL ;";
               $res = $this->db->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;

               $q = "ALTER table `".TblModCatalogSet."` ADD INDEX ( `price_levels` ) ;";
               $res = $this->db->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;
           }
           // add field price_levels_currency to the table settings
           if ( !$this->db->IsFieldExist(TblModCatalogSet, "price_levels_currency") ) {
               $q = "ALTER table `".TblModCatalogSet."` ADD `price_levels_currency` SET( '0', '1' ) DEFAULT NULL ;";
               $res = $this->db->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;

               $q = "ALTER table `".TblModCatalogSet."` ADD INDEX ( `price_levels_currency` ) ;";
               $res = $this->db->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;
           }

           // add field tags to the table settings
           if ( !$this->db->IsFieldExist(TblModCatalogSet, "tags") ) {
               $q = "ALTER table `".TblModCatalogSet."` ADD `tags` SET( '0', '1' ) DEFAULT NULL ;";
               $res = $this->db->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;

               $q = "ALTER table `".TblModCatalogSet."` ADD INDEX ( `tags` ) ;";
               $res = $this->db->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;
           }

           // add field new to the table mod_catalog_set
           if ( !$this->db->IsFieldExist(TblModCatalogSet, "new") ) {
               $q = "ALTER table `".TblModCatalogSet."` ADD `new` SET( '0', '1' ) NULL DEFAULT '0';";
               $res = $this->db->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;

               $q = "ALTER table `".TblModCatalogSet."` ADD INDEX ( `new` ) ;";
               $res = $this->db->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;
           }

           if ( !$this->db->IsFieldExist(TblModCatalogSet, "imgColors") ) {
               $q = "ALTER table `".TblModCatalogSet."` ADD `imgColors` SET( '0', '1' ) NULL DEFAULT '0';";
               $res = $this->db->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;

               $q = "ALTER table `".TblModCatalogSet."` ADD INDEX ( `imgColors` ) ;";
               $res = $this->db->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;
           }

           if ( !$this->db->IsFieldExist(TblModCatalogSet, "sizes") ) {
               $q = "ALTER table `".TblModCatalogSet."` ADD `sizes` SET( '0', '1' ) NULL DEFAULT '0';";
               $res = $this->db->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;

               $q = "ALTER table `".TblModCatalogSet."` ADD INDEX ( `sizes` ) ;";
               $res = $this->db->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;
           }

           if ( !$this->db->IsFieldExist(TblModCatalogSet, "sizesCount") ) {
               $q = "ALTER table `".TblModCatalogSet."` ADD `sizesCount` SET( '0', '1' ) NULL DEFAULT '0';";
               $res = $this->db->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;

               $q = "ALTER table `".TblModCatalogSet."` ADD INDEX ( `sizesCount` ) ;";
               $res = $this->db->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;
           }

           // add field best to the table mod_catalog_set
           if ( !$this->db->IsFieldExist(TblModCatalogSet, "best") ) {
               $q = "ALTER table `".TblModCatalogSet."` ADD `best` SET( '0', '1' ) NULL DEFAULT '0';";
               $res = $this->db->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;

               $q = "ALTER table `".TblModCatalogSet."` ADD INDEX ( `best` ) ;";
               $res = $this->db->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;
           }

           // add field art_num to the table mod_catalog_set
           if ( !$this->db->IsFieldExist(TblModCatalogSet, "art_num") ) {
               $q = "ALTER table `".TblModCatalogSet."` ADD `art_num` SET( '0', '1' ) NULL DEFAULT '0';";
               $res = $this->db->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;

               $q = "ALTER table `".TblModCatalogSet."` ADD INDEX ( `art_num` ) ;";
               $res = $this->db->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;
           }

           // add field barcode to the table mod_catalog_set
           if ( !$this->db->IsFieldExist(TblModCatalogSet, "barcode") ) {
               $q = "ALTER table `".TblModCatalogSet."` ADD `barcode` SET( '0', '1' ) NULL DEFAULT '0';";
               $res = $this->db->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;

               $q = "ALTER table `".TblModCatalogSet."` ADD INDEX ( `barcode` ) ;";
               $res = $this->db->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;
           }

           // add field cod_pli to the table mod_catalog_set
           if ( !$this->db->IsFieldExist(TblModCatalogSet, "cod_pli") ) {
               $q = "ALTER table `".TblModCatalogSet."` ADD `cod_pli` SET( '0', '1' ) NULL DEFAULT '0';";
               $res = $this->db->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;

               $q = "ALTER table `".TblModCatalogSet."` ADD INDEX ( `cod_pli` ) ;";
               $res = $this->db->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;
           }

           // add field relat_prop to the table mod_catalog_set
           if ( !$this->db->IsFieldExist(TblModCatalogSet, "relat_prop") ) {
               $q = "ALTER table `".TblModCatalogSet."` ADD `relat_prop` SET( '0', '1' ) NULL DEFAULT '0';";
               $res = $this->db->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;

               $q = "ALTER table `".TblModCatalogSet."` ADD INDEX ( `relat_prop` ) ;";
               $res = $this->db->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;
           }

           // add field multi_categs to the table mod_catalog_set
           if ( !$this->db->IsFieldExist(TblModCatalogSet, "multi_categs") ) {
               $q = "ALTER table `".TblModCatalogSet."` ADD `multi_categs` SET( '0', '1' ) NULL DEFAULT '0';";
               $res = $this->db->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;

               $q = "ALTER table `".TblModCatalogSet."` ADD INDEX ( `multi_categs` ) ;";
               $res = $this->db->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;
           }

           // add field name_quick_edit to the table mod_catalog_set
           if ( !$this->db->IsFieldExist(TblModCatalogSet, "name_quick_edit") ) {
               $q = "ALTER table `".TblModCatalogSet."` ADD `name_quick_edit` SET( '0', '1' ) NULL DEFAULT '0';";
               $res = $this->db->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;

               $q = "ALTER table `".TblModCatalogSet."` ADD INDEX ( `name_quick_edit` ) ;";
               $res = $this->db->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;
           }


           // add field cat_name_ind to the table mod_catalog_set
           if ( !$this->db->IsFieldExist(TblModCatalogSet, "cat_name_ind") ) {
               $q = "ALTER table `".TblModCatalogSet."` ADD `cat_name_ind` SET( '0', '1' ) NULL DEFAULT '0';";
               $res = $this->db->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;

               $q = "ALTER table `".TblModCatalogSet."` ADD INDEX ( `cat_name_ind` ) ;";
               $res = $this->db->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;
           }

           // add field cat_descr to the table mod_catalog_set
           if ( !$this->db->IsFieldExist(TblModCatalogSet, "cat_descr") ) {
               $q = "ALTER table `".TblModCatalogSet."` ADD `cat_descr` SET( '0', '1' ) NULL DEFAULT '0';";
               $res = $this->db->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;

               $q = "ALTER table `".TblModCatalogSet."` ADD INDEX ( `cat_descr` ) ;";
               $res = $this->db->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;
           }

           // add field cat_descr2 to the table mod_catalog_set
           if ( !$this->db->IsFieldExist(TblModCatalogSet, "cat_descr2") ) {
               $q = "ALTER table `".TblModCatalogSet."` ADD `cat_descr2` SET( '0', '1' ) NULL DEFAULT '0';";
               $res = $this->db->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;

               $q = "ALTER table `".TblModCatalogSet."` ADD INDEX ( `cat_descr2` ) ;";
               $res = $this->db->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;
           }

           // add field cat_img to the table mod_catalog_set
           if ( !$this->db->IsFieldExist(TblModCatalogSet, "cat_img") ) {
               $q = "ALTER table `".TblModCatalogSet."` ADD `cat_img` SET( '0', '1' ) NULL DEFAULT '0';";
               $res = $this->db->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;

               $q = "ALTER table `".TblModCatalogSet."` ADD INDEX ( `cat_img` ) ;";
               $res = $this->db->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;
           }

           // add field cat_sublevels to the table mod_catalog_set
           if ( !$this->db->IsFieldExist(TblModCatalogSet, "cat_sublevels") ) {
               $q = "ALTER table `".TblModCatalogSet."` ADD `cat_sublevels` SET( '0', '1' ) NULL DEFAULT '0';";
               $res = $this->db->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;

               $q = "ALTER table `".TblModCatalogSet."` ADD INDEX ( `cat_sublevels` ) ;";
               $res = $this->db->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;
           }

           // add field sublevels to the table mod_catalog_set
           if ( !$this->db->IsFieldExist(TblModCatalogSet, "cat_content") ) {
               $q = "ALTER table `".TblModCatalogSet."` ADD `cat_content` SET( '0', '1' ) NULL DEFAULT '0';";
               $res = $this->db->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;

               $q = "ALTER table `".TblModCatalogSet."` ADD INDEX ( `cat_content` ) ;";
               $res = $this->db->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;
           }

           // add field cat_params to the table mod_catalog_set
           if ( !$this->db->IsFieldExist(TblModCatalogSet, "cat_params") ) {
               $q = "ALTER table `".TblModCatalogSet."` ADD `cat_params` SET( '0', '1' ) NULL DEFAULT '0';";
               $res = $this->db->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;

               $q = "ALTER table `".TblModCatalogSet."` ADD INDEX ( `cat_params` ) ;";
               $res = $this->db->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;
           }

           // add field cat_relat to the table mod_catalog_set
           if ( !$this->db->IsFieldExist(TblModCatalogSet, "cat_relat") ) {
               $q = "ALTER table `".TblModCatalogSet."` ADD `cat_relat` SET( '0', '1' ) NULL DEFAULT '0';";
               $res = $this->db->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;

               $q = "ALTER table `".TblModCatalogSet."` ADD INDEX ( `cat_relat` ) ;";
               $res = $this->db->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;
           }
           if ( !$this->db->IsFieldExist(TblModCatalogSet, "priceFromSizeColor") ) {
               $q = "ALTER table `".TblModCatalogSet."` ADD `priceFromSizeColor` SET( '0', '1' ) NULL DEFAULT '0';";
               $res = $this->db->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;

               $q = "ALTER table `".TblModCatalogSet."` ADD INDEX ( `priceFromSizeColor` ) ;";
               $res = $this->db->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;
           }
            if ( !$this->db->IsFieldExist(TblModCatalogSet, "share") ) {
               $q = "ALTER table `".TblModCatalogSet."` ADD `share` SET( '0', '1' ) NULL DEFAULT '0';";
               $res = $this->db->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;

               $q = "ALTER table `".TblModCatalogSet."` ADD INDEX ( `share` ) ;";
               $res = $this->db->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;
           }


       }// end of function AddTable()


       // ================================================================================================
       // Function : ShowSettings()
       // Date : 04.01.2011
       // Returns : true,false / Void
       // Description : Show setting of Catalog
       // Programmer : Yaroslav Gyryn
       // ================================================================================================
       function ShowSettings()
       {
         $Panel = new Panel();
         $ln_sys = new SysLang();

        $script = $_SERVER['PHP_SELF'].'?module='.$this->module;

         $q="select * from `".TblModCatalogSet."` where 1";
         $res = $this->Right->Query( $q, $this->user_id, $this->module );
         if( !$this->Right->result ) return false;
         $row = $this->Right->db_FetchAssoc();

        /* Write Form Header */
        $this->Form->WriteHeader( $script );
        AdminHTML::PanelSimpleH();

        $q_spr1 = "SELECT `".TblSysFunc."`.*, `".TblSysSprFunc."`.`name` AS `func_name`
                   FROM `".TblSysFunc."`, `".TblSysSprFunc."`
                   WHERE `".TblSysFunc."`.`id`=`".TblSysSprFunc."`.`cod`
                   AND `".TblSysSprFunc."`.`lang_id`='".$this->lang_id."'
                   ORDER BY `func_name` asc";
        $res_spr1 = $this->Right->Query($q_spr1, $this->user_id, $this->module);
        //echo '<br>$q_spr1='.$q_spr1.' $res_spr1='.$res_spr1;
        if( !$res OR !$this->Right->result) return false;
        $rows_spr1 = $this->Right->db_GetNumRows();
        $mas1['']='';
        for($i=0; $i<$rows_spr1; $i++)
        {
          $row_spr1=$this->Right->db_FetchAssoc();
          if (!empty($row_spr1['name'])) $mas1[$row_spr1['id']]=$row_spr1['func_name'].' ('.$row_spr1['name'].')';
        }

        ?>
           <?=AdminHTML::PanelSimpleH();?>
                <table border="0" class="EditTable" cellspacing=1 cellpading=0>
                 <TR class=TR1 >
                  <TD><?=$this->multi['FLD_CONTENT_FUNCTION'];?>
                  <TD><?=$this->Form->Select( $mas1, 'content_func', $row['content_func'], NULL, 'style="width:300px;"' );?>
                 </TR>
                 <TR class=TR2>
                  <TD><?=$this->multi['FLD_PARAMS_FUNCTION'];?>
                  <TD><?=$this->Form->Select( $mas1, 'params_func', $row['params_func'], NULL, 'style="width:300px;"' );?>
                 </TR>
                </table>
           <?=AdminHTML::PanelSimpleF();?>

        <div class="float-to-left" style="margin: 0px 20px 0px 0px;">
           <?=AdminHTML::PanelSimpleH();?>
           <table border="0" cellspacing="1" cellpading="0" width="150" class="EditTable">
            <tr>
             <td colspan="2"><b><?=$this->multi['TXT_USED_CATEGS_PROPS']?>:</b></td>
            </tr>

            <tr class="TR1">
             <td align="left"><?=$this->multi['FLD_NAME_INDIVIDUAL'];?>
             <td><?$this->Form->CheckBox( "cat_name_ind", '', $row['cat_name_ind'] );?>
            </tr>
            <tr class="TR2">
             <td align="left"><?=$this->multi['FLD_DESCRIP'];?>
             <td><?$this->Form->CheckBox( "cat_descr", '', $row['cat_descr'] );?>
            </tr>
            <tr class="TR1">
             <td align="left"><?=$this->multi['FLD_DESCRIP2'];?>
             <td><?$this->Form->CheckBox( "cat_descr2", '', $row['cat_descr2'] );?>
            </tr>
            <tr class="TR2">
             <td align="left"><?=$this->multi['FLD_IMG'];?>
             <td><?$this->Form->CheckBox( "cat_img", '', $row['cat_img'] );?>
            </tr>

            <tr class="TR1">
             <td align="left"><?=$this->multi['FLD_SUBLEVEL'];?>
             <td><?$this->Form->CheckBox( "cat_sublevels", '', $row['cat_sublevels'] );?>
            </tr>
            <tr class="TR2">
             <td align="left"><?=$this->multi['FLD_CONTENT'];?>
             <td><?$this->Form->CheckBox( "cat_content", '', $row['cat_content'] );?>
            </tr>
            <tr class="TR1">
             <td align="left"><?=$this->multi['FLD_PARAMS'];?>
             <td><?$this->Form->CheckBox( "cat_params", '', $row['cat_params'] );?>
            </tr>
            <tr class="TR2">
             <td align="left"><?=$this->multi['FLD_RELAT_CATEGORIES'];?>
             <td><?$this->Form->CheckBox( "cat_relat", '', $row['cat_relat'] );?>
            </tr>
               <tr class=TR1>
                   <td align="left">INFO
                   <td><?$this->Form->CheckBox( "info_cat", '', $row['info_cat'] );?>
               </tr>
           </table>
           <?=AdminHTML::PanelSimpleF();?>
        </div>

        <div class="float-to-left">
           <?=AdminHTML::PanelSimpleH();?>
           <table border="0" cellspacing="1" cellpading="0" width="150" class="EditTable">
            <tr>
             <td colspan="2"><b><?=$this->multi['TXT_USED_CATALOG_PROPS']?>:</b></td>
            </tr>

            <tr class="TR1">
             <td align="left"><?=$this->multi['FLD_COD_PLI'];?>
             <td><?$this->Form->CheckBox( "cod_pli", '', $row['cod_pli'] );?>
            </tr>
            <tr class="TR2">
             <td align="left"><?=$this->multi['FLD_ADDITIONAL_CATEGORIES'];?>
             <td><?$this->Form->CheckBox( "multi_categs", '', $row['multi_categs'] );?>
            </tr>
            <tr class="TR1">
             <td align="left"><?=$this->multi['FLD_RELAT_PROP'];?>
             <td><?$this->Form->CheckBox( "relat_prop", '', $row['relat_prop'] );?>
            </tr>
            <tr class="TR2">
             <td align="left"><?=$this->multi['FLD_GROUP'];?>
             <td><?$this->Form->CheckBox( "id_group", '', $row['id_group'] );?>
            </tr>
            <tr class="TR1">
             <td align="left"><?=$this->multi['FLD_SHOW_SHARE'];?>
             <td><?$this->Form->CheckBox( "share", '', $row['share'] );?>
            </tr>
            <tr class="TR2">
             <td align="left"><?=$this->multi['FLD_MANUFAC'];?>
             <td><?$this->Form->CheckBox( "manufac", '', $row['manufac'] );?>
            </tr>
            <tr class="TR1">
             <td align="left"><?=$this->multi['FLD_TAGS'];?>
             <td><?$this->Form->CheckBox( "tags", '', $row['tags'] );?>
            </tr>
            <tr class="TR2">
             <td align="left"><?=$this->multi['FLD_NAME'];?>
             <td><?$this->Form->CheckBox( "name", '', $row['name'] );?>
            </tr>
            <tr class="TR1">
             <td align="left"><?=$this->multi['TXT_NAME_QUICK_EDIT'];?>
             <td><?$this->Form->CheckBox( "name_quick_edit", '', $row['name_quick_edit'] );?>
            </tr>
            <tr class=TR2>
             <td align="left"><?=$this->multi['FLD_IMG'];?>
             <td><?$this->Form->CheckBox( "img", '', $row['img'] );?>
            </tr>
            <tr class="TR1">
             <td align="left"><?=$this->multi['FLD_IMG_COLORS'];?>
             <td><?$this->Form->CheckBox( "imgColors", '', $row['imgColors'] );?>
            </tr>
            <tr class="TR2">
             <td align="left"><?=$this->multi['FLD_SIZES'];?>
             <td><?$this->Form->CheckBox( "sizes", '', $row['sizes'] );?>
            </tr>
            <tr class="TR1">
             <td align="left"><?=$this->multi['FLD_SIZES_COUNT'];?>
             <td><?$this->Form->CheckBox( "sizesCount", '', $row['sizesCount'] );?>
            </tr>
            <tr class="TR2">
             <td align="left"><?=$this->multi['FLD_SIZES_COLOR_PRICE'];?>
             <td><?$this->Form->CheckBox( "priceFromSizeColor", '', $row['priceFromSizeColor'] );?>
            </tr>
            <tr class=TR1>
             <td align="left"><?=$this->multi['FLD_FILES'];?>
             <td><?$this->Form->CheckBox( "files", '', $row['files'] );?>
            </tr>
            <tr class=TR2>
             <td align="left"><?=$this->multi['FLD_SHORT_DESCR'];?>
             <td><?$this->Form->CheckBox( "short_descr", '', $row['short_descr'] );?>
            </tr>
            <tr class=TR1>
             <td align="left"><?=$this->multi['FLD_FULL_DESCR'];?>
             <td><?$this->Form->CheckBox( "full_descr", '', $row['full_descr'] );?>
            </tr>
            <tr class=TR2>
             <td align="left"><?=$this->multi['FLD_SPECIF'];?>
             <td><?$this->Form->CheckBox( "specif", '', $row['specif'] );?>
            </tr>
            <tr class=TR1>
             <td align="left"><?=$this->multi['FLD_REVIEWS'];?>
             <td><?$this->Form->CheckBox( "reviews", '', $row['reviews'] );?>
            </tr>
            <tr class=TR2>
             <td align="left"><?=$this->multi['FLD_SUPPORT'];?>
             <td><?$this->Form->CheckBox( "support", '', $row['support'] );?>
            </tr>
            <tr class=TR1>
             <td align="left"><?=$this->multi['FLD_EXIST'];?>
             <td><?$this->Form->CheckBox( "exist", '', $row['exist'] );?>
            </tr>
            <tr class=TR2>
             <td align="left"><?=$this->multi['FLD_NUMBER_NAME'];?>
             <td><?$this->Form->CheckBox( "number_name", '', $row['number_name'] );?>
            </tr>
            <tr class=TR1>
             <td align="left"><?=$this->multi['FLD_ART_NUM'];?>
             <td><?$this->Form->CheckBox( "art_num", '', $row['art_num'] );?>
            </tr>
            <tr class=TR1>
             <td align="left"><?=$this->multi['FLD_BARCODE'];?>
             <td><?$this->Form->CheckBox( "barcode", '', $row['barcode'] );?>
            </tr>
            <tr class=TR2>
             <td align="left"><?=$this->multi['FLD_GUARANTEE'];?>
             <td><?$this->Form->CheckBox( "grnt", '', $row['grnt'] );?>
            </tr>
            <tr class=TR1>
             <td align="left"><?=$this->multi['FLD_DATE'];?>
             <td><?$this->Form->CheckBox( "dt", '', $row['dt'] );?>
            </tr>
            <tr class=TR2>
             <td align="left"><?=$this->multi['FLD_NEW'];?>
             <td><?$this->Form->CheckBox( "new", '', $row['new'] );?>
            </tr>
            <tr class=TR1>
             <td align="left"><?=$this->multi['FLD_BEST'];?>
             <td><?$this->Form->CheckBox( "best", '', $row['best'] );?>
            </tr>
            <tr class=TR2>
             <td align="left"><?=$this->multi['FLD_RESPONSES'];?>
             <td><?$this->Form->CheckBox( "responses", '', $row['responses'] );?>
            </tr>
            <tr class=TR1>
             <td align="left"><?=$this->multi['FLD_RATING'];?>
             <td><?$this->Form->CheckBox( "rating", '', $row['rating'] );?>
            </tr>
            <tr><td height="5"></td></tr>
            <tr class=TR2>
             <td align="left">
              <?=$this->multi['FLD_PRICE'];?>
              <div align="right"><?=$this->multi['FLD_CURRENCY'];?></div>
             </td>
             <td><?$this->Form->CheckBox( "price", '', $row['price'] );?><br><?$this->Form->CheckBox( "price_currency", '', $row['price_currency'] );?>
            </tr>
            <tr class=TR1>
             <td align="left">
              <?=$this->multi['FLD_OPT_PRICE'];?>
              <div align="right"><?=$this->multi['FLD_CURRENCY'];?></div>
             </td>
             <td><?$this->Form->CheckBox( "opt_price", '', $row['opt_price'] );?><br><?$this->Form->CheckBox( "opt_price_currency", '', $row['opt_price_currency'] );?>
            </tr>
            <tr class=TR2>
             <td align="left">
              <?=$this->multi['FLD_PRICE_LEVELS'];?>
              <div align="right"><?=$this->multi['FLD_CURRENCY'];?></div>
             </td>
             <td><?$this->Form->CheckBox( "price_levels", '', $row['price_levels'] );?><br><?$this->Form->CheckBox( "price_levels_currency", '', $row['price_levels_currency'] );?>
            </tr>
               <tr class=TR1>
                   <td align="left">INFO
                   <td><?$this->Form->CheckBox( "info_prop", '', $row['info_prop'] );?>
               </tr>
           </table>
           <?=AdminHTML::PanelSimpleF();?>
        </div>

        <div>
          <?=AdminHTML::PanelSimpleH();?>
          <table border="0" cellspacing="1" cellpading="0" class="EditTable">
            <tr>
             <td><b><?=$this->multi['TXT_IMG_PATH']?>:</b>
             <?$this->Err!=NULL ? $val=$this->img_path : $val=$row['img_path'];
               if ( trim($val)=='' ) $val = Img_Path;?>
             <br/>
             <?echo SITE_PATH; echo $this->Form->TextBox( 'img_path', $val, 40 )?>
             </td>
            </tr>
            <tr>
             <td><b><?=$this->multi['TXT_FILES_PATH']?>:</b>
             <?$this->Err!=NULL ? $val=$this->files_path : $val=$row['files_path'];
               if ( trim($val)=='' ) $val = Catalog_Upload_Files_Path;?>
             <br/>
             <?echo SITE_PATH; echo $this->Form->TextBox( 'files_path', $val, 40 )?>
             </td>
            </tr>
           </table>
           <?=AdminHTML::PanelSimpleF();?>
        </div>

        <div>
           <?=AdminHTML::PanelSimpleH();?>
           <table border=0 cellspacing=1 cellpading=0  class="EditTable">
            <tr>
             <td colspan=2><b><?=$this->multi['TXT_META_DATA']?>:</b></td>
            </tr>
            <tr>
             <td>
              <?
                $Panel->WritePanelHead( "SubPanel_" );
                $ln_arr = $ln_sys->LangArray( _LANG_ID );
                while( $el = each( $ln_arr ) )
                {
                  $lang_id = $el['key'];
                  $lang = $el['value'];
                  $mas_s[$lang_id] = $lang;

                  $Panel->WriteItemHeader( $lang );
                  echo "\n <table border=0 class='EditTable'>";

                  echo "\n<tr><td><b>".$this->multi['FLD_TITLE'].":</b></td>";
                  echo "\n<td>";
                  $name = $this->Spr->GetByCod( TblModCatalogSetSprTitle, 1, $lang_id );
                  $this->Err!=NULL ? $val=$this->title[$lang_id] : $val=$name[$lang_id];
                  //else $val=$this->title[$lang_id];
                   $this->Form->TextBox( 'title['.$lang_id.']', stripslashes($val),60 );

                  echo "\n<tr><td><b>".$this->multi['FLD_DESCRIPTION'].":</b></td>";
                  echo "\n<td>";
                  $name = $this->Spr->GetByCod( TblModCatalogSetSprDescription, 1, $lang_id );
                  $this->Err!=NULL ? $val=$this->description[$lang_id] : $val=$name[$lang_id];
                  //else $val=$this->description[$lang_id];
                  $this->Form->TextArea( 'description['.$lang_id.']', stripslashes($val), 3, 55 );

                  echo "\n<tr><td><b>".$this->multi['FLD_KEYWORDS'].":</b></td>";
                  echo "\n<td>";
                  $name = $this->Spr->GetByCod( TblModCatalogSetSprKeywords, 1, $lang_id );
                  $this->Err!=NULL ? $val=$this->keywords[$lang_id] : $val=$name[$lang_id];
                  //else $val=$this->keywords[$lang_id];
                  $this->Form->TextArea( 'keywords['.$lang_id.']', stripslashes($val), 3, 55 );
                  echo "\n</table>";
                  $Panel->WriteItemFooter();
                }
                $Panel->WritePanelFooter();
                ?>
             </td>
            </tr>
           </table>
           <?=AdminHTML::PanelSimpleF();?>
        </div>
        <?

        $this->Form->WriteSavePanel( $script );
        //$this->Form->WriteCancelPanel( $script );
        AdminHTML::PanelSimpleF();
        //AdminHTML::PanelSubF();

        $this->Form->WriteFooter();
        return true;
       } //end of function ShowSettings()

       // ================================================================================================
       // Function : SaveSettings()
       // Date : 04.01.2011
       // Returns : true,false / Void
       // Description : show setting of Catalog
       // Programmer : Yaroslav Gyryn
       // ================================================================================================
       function SaveSettings()
       {
        $q="select * from `".TblModCatalogSet."` where 1";
        $res = $this->Right->Query( $q, $this->user_id, $this->module );
        if( !$this->Right->result ) return false;
        $rows = $this->Right->db_GetNumRows();

        $uploaddir = SITE_PATH.$this->img_path;
        if ( !file_exists ($uploaddir) ) mkdir($uploaddir,0755);
        else @chmod($uploaddir,0755);

        $uploaddir = SITE_PATH.$this->files_path;
        if ( !file_exists ($uploaddir) ) mkdir($uploaddir,0755);
        else @chmod($uploaddir,0755);

        if($rows>0)
        {
          $q="update `".TblModCatalogSet."` set
              `content_func`='$this->content_func',
              `params_func`='$this->params_func',
              `img`='$this->img',
              `short_descr`='$this->short_descr',
              `full_descr`='$this->full_descr',
              `specif`='$this->specif',
              `reviews`='$this->reviews',
              `support`='$this->support',
              `exist`='$this->exist',
              `number_name`='$this->number_name',
              `price`='$this->price',
              `opt_price`='$this->opt_price',
              `grnt`='$this->grnt',
              `dt`='$this->dt',
              `img_path`='$this->img_path',
              `id_group`='$this->id_group',
              `name`='$this->name',
              `manufac`='$this->manufac',
              `files`='$this->files',
              `files_path`='$this->files_path',
              `responses`='$this->responses',
              `rating`='$this->rating',
              `price_currency`='$this->price_currency',
              `opt_price_currency`='$this->opt_price_currency',
              `price_levels`='$this->price_levels',
              `price_levels_currency`='$this->price_levels_currency',
              `tags`='".$this->tags."',
              `new`='".$this->new."',
              `best`='".$this->best."',
              `art_num`='".$this->art_num."',
              `barcode`='".$this->barcode."',
              `cod_pli`='".$this->cod_pli."',
              `multi_categs`='".$this->multi_categs."',
              `relat_prop`='".$this->relat_prop."',
              `name_quick_edit`='".$this->name_quick_edit."',
              `cat_name_ind`='".$this->cat_name_ind."',
              `cat_descr`='".$this->cat_descr."',
              `cat_descr2`='".$this->cat_descr2."',
              `cat_img`='".$this->cat_img."',
              `cat_sublevels`='".$this->cat_sublevels."',
              `cat_content`='".$this->cat_content."',
              `cat_params`='".$this->cat_params."',
              `imgColors`='".$this->imgColors."',
              `sizes`='".$this->sizes."',
              `sizesCount`='".$this->sizesCount."',
              `priceFromSizeColor`='".$this->priceFromSizeColor."',
              `cat_relat`='".$this->cat_relat."',
              `share`='".$this->share."',
              `info_prop`='".$this->info_prop."',
              `info_cat`='".$this->info_cat."'
              ";
          $res = $this->Right->Query( $q, $this->user_id, $this->module );
          //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;
          if( !$res ) return false;
          if( !$this->Right->result ) return false;
        }
        else
        {
          $q="select * from `".TblModCatalogSet."` where 1";
          $res = $this->Right->Query( $q, $this->user_id, $this->module );
          $rows = $this->Right->db_GetNumRows();
          if($rows>0) return false;

          $q="INSERT INTO `".TblModCatalogSet."` SET
              `content_func`='$this->content_func',
              `params_func`='$this->params_func',
              `img`='$this->img',
              `short_descr`='$this->short_descr',
              `full_descr`='$this->full_descr',
              `specif`='$this->specif',
              `reviews`='$this->reviews',
              `support`='$this->support',
              `exist`='$this->exist',
              `number_name`='$this->number_name',
              `price`='$this->price',
              `opt_price`='$this->opt_price',
              `grnt`='$this->grnt',
              `dt`='$this->dt',
              `img_path`='$this->img_path',
              `id_group`='$this->id_group',
              `name`='$this->name',
              `manufac`='$this->manufac',
              `files`='$this->files',
              `files_path`='$this->files_path',
              `responses`='$this->responses',
              `rating`='$this->rating',
              `price_currency`='$this->price_currency',
              `opt_price_currency`='$this->opt_price_currency',
              `price_levels`='$this->price_levels',
              `price_levels_currency`='$this->price_levels_currency',
              `tags`='".$this->tags."',
              `new`='".$this->new."',
              `best`='".$this->best."',
              `art_num`='".$this->art_num."',
              `barcode`='".$this->barcode."',
              `cod_pli`='".$this->cod_pli."',
              `multi_categs`='".$this->multi_categs."',
              `relat_prop`='".$this->relat_prop."',
              `name_quick_edit`='".$this->name_quick_edit."',
              `cat_name_ind`='".$this->cat_name_ind."',
              `cat_descr`='".$this->cat_descr."',
              `cat_descr2`='".$this->cat_descr2."',
              `cat_img`='".$this->cat_img."',
              `cat_sublevels`='".$this->cat_sublevels."',
              `cat_content`='".$this->cat_content."',
              `cat_params`='".$this->cat_params."',
              `imgColors`='".$this->imgColors."',
              `sizes`='".$this->sizes."',
              `sizesCount`='".$this->sizesCount."',
              `priceFromSizeColor`='".$this->priceFromSizeColor."',
              `cat_relat`='".$this->cat_relat."',
              `share`='".$this->share."',
              `info_prop`='".$this->info_prop."',
              `info_cat`='".$this->info_cat."'
              ";
          $res = $this->Right->Query( $q, $this->user_id, $this->module );
          if( !$this->Right->result) return false;
        }
        $id = 1;

        //---- Save fields on different languages ----
        $res=$this->Spr->SaveNameArr( $id, $this->title, TblModCatalogSetSprTitle );
        if( !$res ) return false;

        $res=$this->Spr->SaveNameArr( $id, $this->description, TblModCatalogSetSprDescription );
        if( !$res ) return false;

        $res=$this->Spr->SaveNameArr( $id, $this->keywords, TblModCatalogSetSprKeywords );
        if( !$res ) return false;

        return true;
       } // end of function SaveSettings()

 } //end of class Catalog_settings