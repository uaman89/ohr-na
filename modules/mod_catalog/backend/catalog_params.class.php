<?php
// ================================================================================================
// System : PrCSM05
// Module : catalog_params.class.php
// Version : 1.0.0
// Date : 21.03.2006
// Licensed To:
// Igor Trokhymchuk ihoru@mail.ru
// Andriy Lykhodid las_zt@mail.ru
//
// Purpose : Class definition for all actions with managment of catalog
//
// ================================================================================================

include_once( SITE_PATH.'/modules/mod_catalog/catalog.defines.php' );

// ================================================================================================
//    Class             : CatalogParams
//    Version           : 1.0.0
//    Date              : 21.03.2006
//
//    Constructor       : Yes
//    Parms             : session_id / session id
//                        usre_id    / UserID
//                        user_      /
//                        user_type  / id of group of user
//    Returns           : None
//    Description       : Class definition for all actions with managment of Catalog
// ================================================================================================
//    Programmer        :  Igor Trokhymchuk
//    Date              :  21.03.2006
//    Reason for change :  Creation
//    Change Request Nbr:  N/A
// ================================================================================================
class CatalogParams extends Catalog {
    var $db = NULL;
    var $Right = NULL;
    var $Msg = NULL;
    var $Form = NULL;
    var $Spr = NULL;

    var $settings = NULL;
    var $is_meta = NULL;
    var $is_descr = NULL;
    var $is_meta_for_spr = NULL;

    // ================================================================================================
    //    Function          : CatalogParams (Constructor)
    //    Version           : 1.0.0
    //    Date              : 21.03.2006
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
    function CatalogParams ($user_id=NULL, $module=NULL, $display=NULL, $sort=NULL, $start=NULL, $width = NULL) {
            //Check if Constants are overrulled
            ( $user_id   !="" ? $this->user_id = $user_id  : $this->user_id = NULL );
            ( $module   !="" ? $this->module  = $module   : $this->module  = NULL );
            ( $display  !="" ? $this->display = $display  : $this->display = 20   );
            ( $sort     !="" ? $this->sort    = $sort     : $this->sort    = NULL );
            ( $start    !="" ? $this->start   = $start    : $this->start   = 0    );
            ( $width    !="" ? $this->width   = $width    : $this->width   = 750  );

            if (empty($this->db)) $this->db = DBs::getInstance();
            if (empty($this->Right)) $this->Right = check_init('RightsCatalogPar', 'Rights', "'".$this->user_id."', '".$this->module."'");
            if (empty($this->Msg)) $this->Msg = check_init('ShowMsg', 'ShowMsg');
            if (empty($this->Form)) $this->Form = check_init('FormCatalogParams', 'Form', "'form_mod_catalog_params'");
            if (empty($this->Spr)) $this->Spr = check_init('SysSpr', 'SysSpr', "'$this->user_id', '$this->module'");
            if (empty($this->settings)) $this->settings = $this->GetSettings();
            if (empty($this->multi)) $this->multi = check_init_txt('TblBackMulti',TblBackMulti);

            //load all data of categories to arrays $this->treeCat, $this->treeCatLevels, $this->treeCatData
            $this->loadTree();
            //echo '<br />treeCatLevels=';print_r($this->treeCatLevels);
            //echo '<br />treeCatData=';print_r($this->treeCatData);

            $this->is_meta=1;
            $this->is_descr=1;
            $this->is_meta_for_spr=1;
    } // End of CatalogParams Constructor

    // ================================================================================================
    // Function : show
    // Version : 1.0.0
    // Date : 14.04.2006
    //
    // Parms :
    // Returns : true,false / Void
    // Description : Show data from $module table
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 14.04.2006
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function show()
    {
    $tmp_db = new DB();

    if( !$this->sort ) $this->sort='move';
    $q = "SELECT `".TblModCatalogParams."`.*,
                 `".TblModCatalogParamsSprName."`.`name` AS `name`,
                 `".TblModCatalogParamsSprPrefix."`.`name` AS `prefix`,
                 `".TblModCatalogParamsSprSufix."`.`name` AS `sufix`,
                 `".TblModCatalogParamsSprDescr."`.`name` AS `descr`,
                 `".TblModCatalogParamsSprMTitle."`.`name` AS `mtitle`,
                 `".TblModCatalogParamsSprMDescr."`.`name` AS `mdescr`,
                 `".TblModCatalogParamsSprMKeywords."`.`name` AS `mkeywords`
          FROM `".TblModCatalogParams."`
          LEFT JOIN `".TblModCatalogParamsSprName."` ON (`".TblModCatalogParams."`.`id`=`".TblModCatalogParamsSprName."`.`cod`AND `".TblModCatalogParamsSprName."`.`lang_id`='".$this->lang_id."')
          LEFT JOIN `".TblModCatalogParamsSprPrefix."` ON (`".TblModCatalogParams."`.`id`=`".TblModCatalogParamsSprPrefix."`.`cod`AND `".TblModCatalogParamsSprPrefix."`.`lang_id`='".$this->lang_id."')
          LEFT JOIN `".TblModCatalogParamsSprSufix."` ON (`".TblModCatalogParams."`.`id`=`".TblModCatalogParamsSprSufix."`.`cod`AND `".TblModCatalogParamsSprSufix."`.`lang_id`='".$this->lang_id."')
          LEFT JOIN `".TblModCatalogParamsSprDescr."` ON (`".TblModCatalogParams."`.`id`=`".TblModCatalogParamsSprDescr."`.`cod`AND `".TblModCatalogParamsSprDescr."`.`lang_id`='".$this->lang_id."')
          LEFT JOIN `".TblModCatalogParamsSprMTitle."` ON (`".TblModCatalogParams."`.`id`=`".TblModCatalogParamsSprMTitle."`.`cod`AND `".TblModCatalogParamsSprMTitle."`.`lang_id`='".$this->lang_id."')
          LEFT JOIN `".TblModCatalogParamsSprMDescr."` ON (`".TblModCatalogParams."`.`id`=`".TblModCatalogParamsSprMDescr."`.`cod`AND `".TblModCatalogParamsSprMDescr."`.`lang_id`='".$this->lang_id."')
          LEFT JOIN `".TblModCatalogParamsSprMKeywords."` ON (`".TblModCatalogParams."`.`id`=`".TblModCatalogParamsSprMKeywords."`.`cod`AND `".TblModCatalogParamsSprMKeywords."`.`lang_id`='".$this->lang_id."')
          WHERE `".TblModCatalogParams."`.`id_cat`='".$this->id_cat."'";
    if( $this->fltr ) $q .= " AND `type`='".$this->type."'";
    $q .= " ORDER BY `".TblModCatalogParams."`.`".$this->sort."`";
    $res = $this->Right->Query( $q, $this->user_id, $this->module );
    //echo '<br>$q='.$q.' $res='.$res.'$this->Right->result='.$this->Right->result.' $this->user_id='.$this->user_id;
    if( !$res )return false;
    $rows = $this->Right->db_GetNumRows();

    $a = $rows;
    $j = 0;
    $up = 0;
    $down = 0;
    $row_arr = NULL;
    for( $i = 0; $i < $rows; $i++ )
    {
      $row = $this->Right->db_FetchAssoc();
      if( $i >= $this->start && $i < ( $this->start+$this->display ) )
      {
        $row_arr[$j] = $row;
        $j = $j + 1;
      }
    }
    $rows = count( $row_arr );

    /* Write Form Header */
    $this->Form->WriteHeader( $this->script );

    if ( empty($this->parent_script) ) $this->parent_script = $_SERVER['PHP_SELF'].'?module='.$this->parent_module.'&display='.$this->parent_display.'&start='.$this->parent_start.'&sort='.$this->parent_sort;
    else $this->parent_script = str_replace('_AND_', '&', $this->parent_script);
    if ( $this->id_cat>0 ) {
        $this->ShowPathToLevel($this->id_cat, NULL, $this->parent_script );
        echo ' <span style="color:#000000; font-size:8pt; font-weight:bold;">('.$this->Msg->show_text('FLD_PARAMS').')</span>';
    }else{
        ?><a href="<?=$this->parent_script?>&task=show_main_category"><?=$this->multi['TXT_SETTING_ROOT_CATALOG']?></a><?
    }
    $this->parent_script= str_replace('&', '_AND_', $this->parent_script);
    //echo '<br> $this->parent_script='.$this->parent_script;
    $this->Form->Hidden( 'parent_script', $this->parent_script );


    /* Write Table Part */
    AdminHTML::TablePartH();

    /* Write Links on Pages */
    ?>
    <tr>
     <td colspan="12">
      <?
      $script1 = 'module='.$this->module.'&fltr='.$this->fltr;
      $script1 = $_SERVER['PHP_SELF']."?$script1";
      $this->Form->WriteLinkPages( $this->script, $a, $this->display, $this->start, $this->sort );
      ?>
     </td>
    </tr>
    <tr>
     <td colspan="12">
      <?$this->Form->WriteTopPanel( $this->script );?>
      &nbsp;<a CLASS="toolbar" href="javascript:<?=$this->Form->name;?>.task.value='show_get_copy_params';<?=$this->Form->name;?>.submit();" onmouseout="MM_swapImgRestore();"  onmouseover="MM_swapImage('add_params','','images/icons/add_params_f2.png',1);"><img src="images/icons/add_params.png" alt="<?=$this->Msg->show_text('BTN_GET_COPY_PARAMETERS');?>" title="<?=$this->Msg->show_text('BTN_GET_COPY_PARAMETERS');?>" align="center" name="add_params" border="0" /><?=$this->Msg->show_text('BTN_GET_COPY_PARAMETERS');?></a>
     </td>
    </tr>
    <?
    $script2 = 'module='.$this->module.'&display='.$this->display.'&start='.$this->start.'&task=show&fltr='.$this->fltr;
    $script2 = $_SERVER['PHP_SELF']."?$script2";

    if($rows>$this->display) $ch = $this->display;
    else $ch = $rows;
    ?>
    <TR>
    <Th class="THead"><input value="0" id="cAll" onclick="if (this.value == '1') {unCheckAll(<?=$ch;?>); this.value = '0';} else {checkAll(<?=$ch;?>); this.value = '1';}" type="checkbox"></Th>
    <Th class="THead"><A HREF="<?=$script2?>&sort=id" class="aTHead"><?=$this->Msg->show_text('FLD_ID')?></A></Th>
    <Th class="THead"><?=$this->Msg->show_text('FLD_PARAM_NAME')?></Th>
    <Th class="THead"><A HREF="<?=$script2?>&sort=type" class="aTHead"><?=$this->Msg->show_text('FLD_TYPE')?></A></Th>
    <Th class="THead small"><?=$this->Msg->show_text('FLD_INFLUENCE_ON_IMAGE')?></Th>
    <Th class="THead small" ><?=$this->Msg->show_text('FLD_CAN_BE_MODIFY_BY_USER')?></Th>
    <Th class="THead small"><?=$this->Msg->show_text('FLD_PREFIX')?></Th>
    <Th class="THead small"><?=$this->Msg->show_text('FLD_VALUES')?></Th>
    <Th class="THead small"><?=$this->Msg->show_text('FLD_SUFIX')?></Th>
    <?if( $this->is_descr==1 ){?><th class="THead"><?=$this->Msg->show_text('FLD_DESCRIPTION')?></th><?}?>
    <?if( $this->is_meta==1 ){?><th class="THead"><?=$this->Msg->show_text('TXT_META_DATA')?></th><?}?>
    <Th class="THead"><?=$this->Msg->show_text('FLD_DISPLAY')?></Th>
    <?

    $style1 = 'TR1';
    $style2 = 'TR2';
    for( $i = 0; $i < $rows; $i++ )
    {
      $row = $row_arr[$i];

      if ( (float)$i/2 == round( $i/2 ) )
      {
       echo '<TR CLASS="'.$style1.'">';
      }
      else echo '<TR CLASS="'.$style2.'">';

      echo '<TD align="center">';
      //$this->Form->CheckBox( "id_del[]", $row['id'] );
      $this->Form->CheckBox( "id_del[]", $row['id'], null, "check".$i );

      echo '<TD>';
      $this->Form->Link( $this->script."&task=edit&id=".$row['id'], stripslashes( $row['id'] ), $this->Msg->show_text('TXT_EDIT') );

      echo '<TD align=center>';
      echo stripslashes($row['name']);

      echo '<TD align="center" style="padding:5px; font-weight: normal;">';
      if ($row['type']=='1') echo $this->Msg->show_text('TXT_NUMBER');
      if ($row['type']=='2') echo $this->Msg->show_text('TXT_BOOL');
      if ($row['type']=='3') echo $this->Msg->show_text('TXT_LIST');
      if ($row['type']=='4') echo $this->Msg->show_text('TXT_MULTI_LIST');
      if ($row['type']=='5') echo $this->Msg->show_text('TXT_TEXT');

      echo '<TD align=center>';  if ( $row['is_img']==1 ) $this->Form->ButtonCheck();

      echo '<TD align=center>';  if ( $row['modify']==1 ) $this->Form->ButtonCheck();

      ?><td align="center"><?=stripslashes( $row['prefix']);?></td><?

      if ( $row['type']=='3' || $row['type']=='4' ) {
        //$tmp_rows = $this->IsParamsValues( $row['id_cat'], $row['id'] );
        //echo '<br>$tmp_rows='.$tmp_rows;
        $script2 = $_SERVER['PHP_SELF']."?module=$this->module&task=showvalues&id=".$row['id']."&id_cat=".$row['id_cat']."&parent=1&parent_id=".$row['id']."&parent_module=".$this->module."&parent_display=".$this->display."&parent_start=".$this->start."&parent_sort=".$this->sort."&parent_task=show&parent_level=".$this->level.'&parent_script='.$this->parent_script;
        echo '<TD align=center>';
        $param_val_str = $this->GetListNameOfParamVal($row['id_cat'], $row['id'], $this->lang_id);
        if ( !empty($param_val_str) ) {
            $tmp_name = $this->Msg->show_text('FLD_PARAMS');
            //echo '<a href="'.$script2.'">'.stripslashes($this->Spr->GetListName( $this->BuildNameOfValuesTable($row['id_cat'], $row['id']) )).'</a>';
            echo '<a href="'.$script2.'">'.stripslashes($param_val_str).'</a>';
        }
        else {
            $tmp_name = $this->Msg->show_text('TXT_CREATE_CONTENT');
            $this->Form->Link( $script2, $tmp_name );
        }
        echo '</TD>';
      }
      else echo '<TD>';

      ?><td align="center"><?=stripslashes( $row['sufix']);?></td><?

      if( $this->is_descr==1 ){
          $tmpstr = stripslashes($row['descr']);
          ?><td align="left"><?=$this->GetSubStrCutByWorld($tmpstr, 0, 255); if( strlen($tmpstr)>255) echo '...';?></td>
      <?}
      if( $this->is_meta==1 ){?>
       <td align="left" style="padding:5px; font-weight: normal;" nowrap="nowrap" st>
        <?
        $title = stripslashes($row['mtitle']);
        $descr = stripslashes($row['mdescr']);
        $keywords = stripslashes($row['mkeywords']);
        ?>
        <?if( !empty($title)){ ?><div onmouseover="return overlib('<?=$title;?>',WRAP);" onmouseout="nd();" onclick="nd();"><?=$this->Form->ButtonCheck(); echo ' '.$this->Msg->show_text('FLD_PAGES_TITLE'); ?></div><?}?>
        <?if( !empty($descr)){ ?><div onmouseover="return overlib('<?=$descr;?>',WRAP);" onmouseout="nd();" onclick="nd();"><?=$this->Form->ButtonCheck(); echo ' '.$this->Msg->show_text('FLD_PAGES_DESCR');?></div><?}?>
        <?if( !empty($keywords)){ ?><div onmouseover="return overlib('<?=$keywords;?>',WRAP);" onmouseout="nd();" onclick="nd();"><?=$this->Form->ButtonCheck(); echo ' '.$this->Msg->show_text('FLD_KEYWORDS');?></div><?}?>
       </td>
      <?}
       echo '<TD align=center>';
       if( $up!=0 )
       {
       ?>
        <a href=<?=$this->script?>&task=up&move=<?=$row['move']?>><?=$this->Form->ButtonUp( $row['id'] );?></a>
       <?
       }

       if( $i!=($rows-1) )
       {
       ?>
         <a href=<?=$this->script?>&task=down&move=<?=$row['move']?>></a><?=$this->Form->ButtonDown( $row['id'] );?></a>
       <?
       }

       $up=$row['id'];
       $a=$a-1;

    } //-- end for

    AdminHTML::TablePartF();
    $this->Form->WriteFooter();
    return true;

    } // end of function show()

    // ================================================================================================
    // Function : edit
    // Version : 1.0.0
    // Date : 14.04.2006
    //
    // Parms :
    // Returns : true,false / Void
    // Description : Show data from $spr table for editing
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 14.04.2006
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function edit()
    {
        $Panel = check_init('Panel', 'Panel');
        $ln_sys = check_init('SysLang', 'SysLang');
        $mas=NULL;

        $fl = NULL;

        if( $this->id!=NULL and ( $mas==NULL ) )
        {
         $q="select * from `".TblModCatalogParams."` where id='".$this->id."'";
         $res = $this->Right->Query( $q, $this->user_id, $this->module );
         if( !$this->Right->result ) return false;
         $mas = $this->Right->db_FetchAssoc();
        }

        /* Write Form Header */
        $this->Form->WriteHeader( $this->script );
        $settings=SysSettings::GetGlobalSettings();
        $this->Form->textarea_editor = $settings['editer']; //'tinyMCE';
        $this->Form->IncludeSpecialTextArea( $settings['editer']);

        $this->Form->Hidden( 'parent_script', $this->parent_script );

        if( $this->id!=NULL ) $txt = $this->multi['TXT_EDIT'];
        else $txt = $this->multi['TXT_ADD'];

        AdminHTML::PanelSubH( $txt );

        //-------- Show Error text for validation fields --------------
        $this->ShowErrBackEnd();
        //-------------------------------------------------------------

        AdminHTML::PanelSimpleH();
        ?>

         <tr>
          <td width="10%"><b><?echo $this->Msg->show_text('FLD_ID')?>: </b>
           <?
           if( $this->id!=NULL )
           {
            echo $mas['id'];
            $this->Form->Hidden( 'id', $mas['id'] );
           }
           else $this->Form->Hidden( 'id', '' );
           ?>
          </td>
             <td><b><?echo $this->Msg->show_text('FLD_PARPAM_FOR_CATEGORY')?>:</b>
              <?
              if( $this->id!=NULL ) $this->Err!=NULL ? $id_cat=$this->id_cat : $id_cat=$mas['id_cat'];
              else $id_cat=$this->id_cat;
              if ( !empty($id_cat) ) {
                echo '<b>'.$this->Spr->GetNameByCod( TblModCatalogSprName, $id_cat ).' ['.$this->Msg->show_text('FLD_ID').' '.$id_cat.'] </b>';
                $this->Form->Hidden( 'id_cat', $id_cat );
              }
              else echo $this->Spr->ShowInComboBox( TblModCatalogSprName, 'id_cat', $id_cat, 50 );
              ?>
             </td>
         </tr>
         <tr>
          <td colspan="2">
           <table border="0" cellpadding="0" cellspacing="0" class="EditTable">
            <tr>
             <td><b><?echo $this->Msg->show_text('FLD_TYPE')?>:</b></td>
             <td>&nbsp;
              <?
              $arr_v[0]='';
              $arr_v[1]=$this->Msg->show_text('TXT_NUMBER');
              $arr_v[2]=$this->Msg->show_text('TXT_BOOL');
              $arr_v[3]=$this->Msg->show_text('TXT_LIST');
              $arr_v[4]=$this->Msg->show_text('TXT_MULTI_LIST');
              $arr_v[5]=$this->Msg->show_text('TXT_TEXT');
              if( $this->id!=NULL ) $this->Err!=NULL ? $type=$this->type : $type=$mas['type'];
              else $type=$this->type;
              $this->Form->Select( $arr_v, 'type', $type);
              ?>
             </td>
             <td width="20"></td>
             <?
             if( $this->id!=NULL ) $this->Err!=NULL ? $is_img=$this->is_img : $is_img=$mas['is_img'];
             else $is_img=$this->is_img;
             ?>
             <td><INPUT class='checkbox' TYPE=checkbox NAME='is_img' <?if ($is_img=='1') echo 'CHECKED';?>> <?=$this->Msg->show_text('FLD_INFLUENCE_ON_IMAGE');?></td>
            </tr>
            <tr>
             <td></td>
             <td></td>
             <td></td>
             <?
             if( $this->id!=NULL ) $this->Err!=NULL ? $modify=$this->modify : $modify=$mas['modify'];
             else $modify=$this->modify;
             ?>
             <td><INPUT class='checkbox' TYPE=checkbox NAME='modify' <?if ($modify=='1') echo 'CHECKED';?>> <?=$this->Msg->show_text('FLD_CAN_BE_MODIFY_BY_USER');?></td>
            </tr>
           </table>
          </td>
         </tr>
         <tr>
          <td colspan=2>
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

             echo "\n <tr>";
             echo "\n <td><b>".$this->Msg->show_text('FLD_NAME').':</b><br><span style="font-size:9px;">('.$this->Msg->show_text('FLD_PARAM_NAME').")</span>";
             echo "\n <td valign='top'>";
             $row = $this->Spr->GetByCod( TblModCatalogParamsSprName, $mas['id'], $lang_id );
             if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->name[$lang_id] : $val=$row[$lang_id];
             else $val=$this->name[$lang_id];
             $this->Form->TextBox( 'name['.$lang_id.']', stripslashes($val), 40 );

             if($this->is_descr==1){
                 echo "\n <tr>";
                 echo "\n <td><b>".$this->Msg->show_text('FLD_DESCRIPTION').":</b>";
                 echo "\n <td>";
                 $row = $this->Spr->GetByCod( TblModCatalogParamsSprDescr, $mas['id'], $lang_id );
                 if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->descr[$lang_id] : $val=$row[$lang_id];
                 else $val=$this->descr[$lang_id];
                 //$this->Form->TextBox( 'descr['.$lang_id.']', stripslashes($val), 40 );
                 $this->Form->SpecialTextArea(NULL, 'descr['.$lang_id.']', stripslashes($val), 15, 70, NULL, $lang_id);
             }

             echo "\n <tr>";
             echo "\n <td><b>".$this->Msg->show_text('FLD_PREFIX').":</b>";
             echo "\n <td>";
             $row = $this->Spr->GetByCod( TblModCatalogParamsSprPrefix, $mas['id'], $lang_id );
             if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->prefix[$lang_id] : $val=$row[$lang_id];
             else $val=$this->prefix[$lang_id];
             $this->Form->TextBox( 'prefix['.$lang_id.']', stripslashes($val), 20 );

             echo "\n <tr>";
             echo "\n <td><b>".$this->Msg->show_text('FLD_SUFIX').":</b>";
             echo "\n <td>";
             $row = $this->Spr->GetByCod( TblModCatalogParamsSprSufix, $mas['id'], $lang_id );
             if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->sufix[$lang_id] : $val=$row[$lang_id];
             else $val=$this->sufix[$lang_id];
             $this->Form->TextBox( 'sufix['.$lang_id.']', stripslashes($val), 20 );
             echo   "\n </table>";

             if($this->is_meta==1){
                 echo "\n<fieldset title='".$this->Msg->show_text('TXT_META_DATA')."'> <legend><img src='images/icons/meta.png' alt='".$this->Msg->show_text('TXT_META_DATA')."' title='".$this->Msg->show_text('TXT_META_DATA')."' border='0' /> ".$this->Msg->show_text('TXT_META_DATA')." </legend>";
                 echo "\n <table border=0 class='EditTable'>";
                 echo "\n <tr>";
                 echo "\n <td><b>".$this->Msg->show_text('FLD_PAGES_TITLE').":</b>";
                 echo "\n <br>";
                 echo '<span class="help">'.$this->Msg->show_text('HELP_MSG_PAGE_TITLE').'</span>';
                 echo "\n <br>";
                 $row = $this->Spr->GetByCod( TblModCatalogParamsSprMTitle, $mas['id'], $lang_id );
                 if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->mtitle[$lang_id] : $val=$row[$lang_id];
                 else $val=$this->mtitle[$lang_id];
                 $this->Form->TextBox( 'mtitle['.$lang_id.']', stripslashes($val), 70 );

                 echo "\n <tr>";
                 echo "\n <td><b>".$this->Msg->show_text('FLD_PAGES_DESCR').":</b>";
                 echo "\n <br>";
                 echo '<span class="help">'.$this->Msg->show_text('HELP_MSG_PAGE_DESCRIPTION').'</span>';
                 echo "\n <br>";
                 $row = $this->Spr->GetByCod( TblModCatalogParamsSprMDescr, $mas['id'], $lang_id );
                 if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->mdescr[$lang_id] : $val=$row[$lang_id];
                 else $val=$this->mdescr[$lang_id];
                 $this->Form->TextArea( 'mdescr['.$lang_id.']', stripslashes($val), 3, 70 );

                 echo "\n <tr>";
                 echo "\n <td><b>".$this->Msg->show_text('FLD_KEYWORDS').":</b>";
                 echo "\n <br>";
                 echo '<span class="help">'.$this->Msg->show_text('HELP_MSG_PAGE_KEYWORDS').'</span>';
                 echo "\n <br>";
                 $row = $this->Spr->GetByCod( TblModCatalogParamsSprMKeywords, $mas['id'], $lang_id );
                 if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->mkeywords[$lang_id] : $val=$row[$lang_id];
                 else $val=$this->mkeywords[$lang_id];
                 $this->Form->TextArea( 'mkeywords['.$lang_id.']', stripslashes($val),3, 70 );
                 echo "\n<tr><td><table><tr><td><img src='images/icons/info.png' alt='' title='' border='0' /></td><td class='info'>".$this->Msg->show_text('HELP_MSG_META_TAGS_POSITION')."</td></tr></table>";
                 echo "\n </table>";
                 echo "</fieldset>";
             }
             $Panel->WriteItemFooter();
        }
        $Panel->WritePanelFooter();

        echo '<TR><TD COLSPAN=2 ALIGN=left>';

        AdminHTML::PanelSimpleF();
        $this->Form->WriteSavePanel( $this->script );
        $this->Form->WriteCancelPanel( $this->script );
        AdminHTML::PanelSubF();

        $this->Form->WriteFooter();
        return true;
    } // end of function edit()

    // ================================================================================================
    // Function : save
    // Version : 1.0.0
    // Date : 14.04.2006
    //
    // Parms :
    // Returns : true,false / Void
    // Description : Store data to the table
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 14.04.2006
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function save()
    {
        $q="SELECT * FROM `".TblModCatalogParams."` WHERE `id`='".$this->id."'";
        $res = $this->Right->Query( $q, $this->user_id, $this->module );
        if( !$this->Right->result ) return false;
        $rows = $this->Right->db_GetNumRows();

        if($rows>0)
        {
          $q="UPDATE `".TblModCatalogParams."` SET
              `id_cat`='".$this->id_cat."',
              `type`='".$this->type."',
              `is_img`='".$this->is_img."',
              `modify`='".$this->modify."'";
          $q=$q." where id='".$this->id."'";
          $res = $this->Right->Query( $q, $this->user_id, $this->module );
          //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;
          if( !$res OR !$this->Right->result ) return false;
        }
        else
        {
          $q="SELECT MAX(`move`) as maxx FROM `".TblModCatalogParams."` WHERE 1";
          $res = $this->Right->Query( $q, $this->user_id, $this->module );
          //echo '<br>$q='.$q.' $res='.$res;
          $row = $this->Right->db_FetchAssoc();
          $maxx=$row['maxx']+1;

          $q="INSERT INTO `".TblModCatalogParams."` SET
              `id_cat`='".$this->id_cat."',
              `type`='".$this->type."',
              `is_img`='".$this->is_img."',
              `modify`='".$this->modify."',
              `move`='".$maxx."'";
          $res = $this->Right->Query( $q, $this->user_id, $this->module );
          //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;
          if( !$res OR !$this->Right->result) return false;
        }

        if ( empty($this->id) ){
          $this->id = $this->Right->db_GetInsertID();
          //echo '<br>$this->id='.$this->id;
        }

        //---- Save fields on different languages ----
        $res=$this->Spr->SaveNameArr( $this->id, $this->name, TblModCatalogParamsSprName );
        if( !$res ) return false;
        $res=$this->Spr->SaveNameArr( $this->id, $this->prefix, TblModCatalogParamsSprPrefix );
        if( !$res ) return false;
        $res=$this->Spr->SaveNameArr( $this->id, $this->sufix, TblModCatalogParamsSprSufix );
        if( !$res ) return false;
        $res=$this->Spr->SaveNameArr( $this->id, $this->descr, TblModCatalogParamsSprDescr );
        if( !$res ) return false;

        //---------------- save META DATA START -------------------
        $res=$this->Spr->SaveNameArr( $this->id, $this->mtitle, TblModCatalogParamsSprMTitle );
        if( !$res ) return false;
        $res=$this->Spr->SaveNameArr( $this->id, $this->mdescr, TblModCatalogParamsSprMDescr );
        if( !$res ) return false;
        $res=$this->Spr->SaveNameArr( $this->id, $this->mkeywords, TblModCatalogParamsSprMKeywords );
        if( !$res ) return false;
        //---------------- save META DATA END ---------------------

        /*
        if ( $this->type=='3' || $this->type=='4' ) {
            $res=$this->CreateParamTable();
            //echo '<br> $res='.$res;
            if( !$res ) return false;
        }
        */

        //--------- Delete from the table where store parameters depends from image ----------
        if ($this->is_img==0) {
            $res = $this->DelParamFromImg($this->id);
            //echo '<br> $res='.$res;
            if( !$res ) return false;
        }

        return true;

    } // end of function save()

    /*
    // ================================================================================================
    // Function : CreateParamTable
    // Version : 1.0.0
    // Date : 14.04.2006
    //
    // Parms :
    // Returns : true,false / Void
    // Description : Remove data from the table
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 14.04.2006
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function CreateParamTable()
    {
      $tblname = $this->BuildNameOfValuesTable($this->id_cat, $this->id);

      $tmp_db = new DB();
      $res = $tmp_db->db_ListTables();
      //echo '<br> $res='.$res.' $tblname='.$tblname;
      if( !$res ) return false;
      $rows = $tmp_db->db_GetNumRows();
      //echo '<br> $rows='.$rows;
      while ( $row = $tmp_db->db_FetchRow() ) {
        //echo "<br>Table: $row[0]\n";
        if ( $row[0]==$tblname ) return true;
      }

      if( defined("DB_TABLE_CHARSET")) $this->tbl_charset = DB_TABLE_CHARSET;
      else $this->tbl_charset = 'cp1251';

      $q = "CREATE TABLE `".$tblname."` (
          `id` int(4) unsigned NOT NULL auto_increment,
          `cod` int(4) unsigned NOT NULL default '0',
          `lang_id` int(2) unsigned NOT NULL default '0',
          `name` varchar(255) NOT NULL default '',
          `move` int(11) unsigned default NULL,
          ";
      if($this->is_meta_for_spr==1){
          $q .="
              `mtitle` VARCHAR( 255 ),
              `mdescr` VARCHAR( 255 ),
              `mkeywords` VARCHAR( 255 ),
              ";
      }
      $q .="
          PRIMARY KEY  (`id`),
          KEY `cod` (`cod`),
          KEY `lang_id` (`lang_id`),
          KEY `move` (`move`)
        ) ENGINE=MyISAM DEFAULT CHARSET=".$this->tbl_charset;
      $res = $this->Right->Query( $q, $this->user_id, $this->module );
      //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;
      if( !$res OR !$this->Right->result ) return false;
      return true;

    } // end of function CreateParamTable()

    // ================================================================================================
    // Function : DeleteParamTable
    // Version : 1.0.0
    // Date : 17.04.2006
    //
    // Parms :
    // Returns : true,false / Void
    // Description : Remove data from the table
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 17.04.2006
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function DeleteParamTable($id)
    {
       $tblname = $this->BuildNameOfValuesTable($this->id_cat, $id);
       $q = "DROP TABLE `$tblname` ";
       $res = $this->Right->Query( $q, $this->user_id, $this->module );
       //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;
       if( !$res ) return false;

       //delete directories with images
       $res = $this->DeleteDir(Spr_Img_Path.$tblname);
       if( !$res ) return false;
       return true;

    } // end of function DeleteParamTable()
    */

   // ================================================================================================
   // Function : DeleteDir()
   // Version : 1.0.0
   // Date : 09.07.2009
   // Parms :
   // Returns : true,false / Void
   // Description : delete direcory with files
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 09.07.2009
   // Reason for change : Reason Description / Creation
   // Change Request Nbr:
   // ================================================================================================
   function DeleteDir( $fulldir )
   {
       if (is_dir($fulldir)) {
           if ($dh = opendir($fulldir)) {
               //echo '<br> $dh='.$dh ;
               $i=0;
               $j=0;
               while (($file = readdir($dh)) !== false) {
                 //echo '<br>$file='.$file.' filetype('.$fulldir.'/'.$file.')='.filetype($fulldir.'/'.$file);
                 if ($file=='.' || $file=='..') continue;
                 if ( filetype($fulldir.'/'.$file)=='dir' ) {
                     $res = $this->DeleteDir( $fulldir.'/'.$file );
                     if($res) $j++;
                 }
                 else {
                     $res = unlink($fulldir.'/'.$file );
                     //echo '<br>$res='.$res;
                     if($res) $j++;
                 }
                 $i++;
               }
               closedir($dh);
           }
           $res = rmdir($fulldir);
           if(!$res) return false;
           //echo '<br>$i='.$i.' $j='.$j;
           if( $i!=$j ) return false;
       }
       return true;
   }//end of function DeleteDir()

    // ================================================================================================
    // Function : DelParamFromImg
    // Version : 1.0.0
    // Date : 27.07.2006
    //
    // Parms : $id_param   / id of the parameter
    // Returns : true,false / Void
    // Description : Remove data from the table where store parameters influence of images
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 27.07.2006
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function DelParamFromImg($id_param)
    {
       //$tblname = $this->BuildNameOfValuesTable($this->id_cat, $id_param);
       $q = "DELETE FROM ".TblModCatalogParamsPropImg." where `id_param`='$id_param'";
       $res = $this->Right->Query( $q, $this->user_id, $this->module );
       //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;
       if( !$res ) return false;
       if( !$this->Right->result ) return false;
       return true;

    } // end of function DelParamFromImg()

    // ================================================================================================
    // Function : del
    // Version : 1.0.0
    // Date : 14.04.2006
    //
    // Parms :
    // Returns : true,false / Void
    // Description : Remove data from the table
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 14.04.2006
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function del( $id_del )
    {
    $del = 0;
    $kol = count( $id_del );
    for( $i=0; $i<$kol; $i++ )
    {
     $u=$id_del[$i];

      $param_type = $this->GetTypeOfParam( $u );
      //echo '<br> $param_type='.$param_type;
      if ( $param_type=='3' || $param_type=='4' ) {
        //$res = $this->DeleteParamTable($u);
        $q = "DELETE FROM `".TblModCatalogParamsVal."` WHERE `id_cat`='".$this->id_cat."' AND `id_param`='".$u."'";
        $res = $this->Right->Query( $q, $this->user_id, $this->module );
        //echo '<br>$q='.$q.' $res='.$res;
        if( !$res ) return false;
      }

      //--------- Delete from the table where store parameters depends from image ----------
      $res = $this->DelParamFromImg($u);
      //echo '<br> $res='.$res;
      if( !$res ) return false;

      $q = "DELETE FROM ".TblModCatalogParams." WHERE id='".$u."'";
      $res = $this->Right->Query( $q, $this->user_id, $this->module );
      if( !$res ) return false;
      $res = $this->Spr->DelFromSpr( TblModCatalogParamsSprName, $u );
      if( !$res ) return false;
      $res = $this->Spr->DelFromSpr( TblModCatalogParamsSprPrefix, $u );
      if( !$res ) return false;
      $res = $this->Spr->DelFromSpr( TblModCatalogParamsSprSufix, $u );
      if( !$res ) return false;
      $res = $this->DelParamsValuesOfPropByIdparam( $u );
      if( !$res ) return false;
      if ( $res )
       $del=$del+1;
      else
       return false;
    }

     return $del;
    } // end of function del()

    // ================================================================================================
    // Function : up()
    // Version : 1.0.0
    // Date : 11.02.2005
    // Parms :
    // Returns :      true,false / Void
    // Description :  Up position
    // ================================================================================================
    // Programmer :  Andriy Lykhodid
    // Date : 11.02.2005
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================

    function up_param($table)
    {
     $q="select * from `$table` where `move`='$this->move'";
     $res = $this->Right->Query( $q, $this->user_id, $this->module );
     //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;
     if( !$res )return false;
     $rows = $this->Right->db_GetNumRows();
     $row = $this->Right->db_FetchAssoc();
     $move_down = $row['move'];
     $id_down = $row['id'];


     $q="select * from `$table` where `move`<'$this->move' AND `id_cat`='$this->id_cat' order by `move` desc";
     $res = $this->Right->Query( $q, $this->user_id, $this->module );
     //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;
     if( !$res )return false;
     $rows = $this->Right->db_GetNumRows();
     $row = $this->Right->db_FetchAssoc();
     $move_up = $row['move'];
     $id_up = $row['id'];

     //echo '<br> $move_down='.$move_down.' $id_down ='.$id_down.' $move_up ='.$move_up.' $id_up ='.$id_up;
     if( $move_down!=0 AND $move_up!=0 )
     {
     $q="update `$table` set
         `move`='$move_down' where id='$id_up'";
     $res = $this->Right->Query( $q, $this->user_id, $this->module );
     //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;

     $q="update `$table` set
         `move`='$move_up' where id='$id_down'";
     $res = $this->Right->Query( $q, $this->user_id, $this->module );
     //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;

     }
    } // end of function up()


    // ================================================================================================
    // Function : down()
    // Version : 1.0.0
    // Date : 11.02.2005
    // Parms :
    // Returns :      true,false / Void
    // Description :  Down position
    // ================================================================================================
    // Programmer :  Andriy Lykhodid
    // Date : 11.02.2005
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function down_param($table)
    {
     $q="select * from `$table` where `move`='$this->move'";
     $res = $this->Right->Query( $q, $this->user_id, $this->module );
     //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;
     if( !$res )return false;
     $rows = $this->Right->db_GetNumRows();
     $row = $this->Right->db_FetchAssoc();
     $move_up = $row['move'];
     $id_up = $row['id'];


     $q="select * from `$table` where `move`>'$this->move' AND `id_cat`='$this->id_cat' order by `move` asc";
     $res = $this->Right->Query( $q, $this->user_id, $this->module );
     //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;
     if( !$res )return false;
     $rows = $this->Right->db_GetNumRows();
     $row = $this->Right->db_FetchAssoc();
     $move_down = $row['move'];
     $id_down = $row['id'];

     //echo '<br> $move_down='.$move_down.' $id_down ='.$id_down.' $move_up ='.$move_up.' $id_up ='.$id_up;
     if( $move_down!=0 AND $move_up!=0 )
     {
         $q="update `$table` set
             `move`='$move_down' where id='$id_up'";
         $res = $this->Right->Query( $q, $this->user_id, $this->module );
         //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;

         $q="update `$table` set
             `move`='$move_up' where id='$id_down'";
         $res = $this->Right->Query( $q, $this->user_id, $this->module );
         //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;
     }
    } // end of function down()

    // ================================================================================================
    // Function : CheckParamsFields()
    // Version : 1.0.0
    // Date : 14.04.2006
    //
    // Parms :        $id - id of the record in the table
    // Returns :      true,false / Void
    // Description :  Checking all fields for filling and validation
    // ================================================================================================
    // Programmer :  Igor Trokhymchuk
    // Date : 14.04.2006
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function CheckParamsFields($id = NULL)
    {
        $this->Err=NULL;

        if (empty( $this->type )) {
            $this->Err = $this->Err.$this->Msg->show_text('MSG_FLD_TYPE_EMPTY').'<br>';
        }

        if (empty( $this->name[_LANG_ID] )) {
            $this->Err = $this->Err.$this->Msg->show_text('MSG_FLD_PARAM_NAME_EMPTY').'<br>';
        }
        return $this->Err;
    } //end of fuinction CheckParamsFields()

    // ================================================================================================
    // Function : ShowValues()
    // Version : 1.0.0
    // Date : 14.04.2006
    // Parms :
    //           $id = 0  - level of menu  (0 - first level)
    // Returns : true,false / Void
    // Description : Link to sys_spr finctions to fill values of parameters
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 14.04.2006
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function ShowValues( )
    {
       //$this->settings = $this->GetSettings();
       //if( !isset( $pg ) ) $pg = new PageAdmin();
       $this->Spr->module = 6;
       $this->Spr->module_name = $this->Spr->GetNameByCod( TblModCatalogParamsSprName, $this->id );
       //$this->Spr->spr = $this->BuildNameOfValuesTable($this->id_cat, $this->id);
       $this->Spr->spr = TblModCatalogParamsVal;
       $this->Spr->root_script = $this->parent_script;
       $this->Spr->parent_script = str_replace('&', '_AND_', $this->script);
       $this->Spr->parent_id = $this->module;
       $this->Spr->display=20;
       $this->Spr->sort=NULL;
       $this->Spr->start=0;
       $this->Spr->fln = _LANG_ID;
       $this->Spr->uselevels = 0;
       if($this->is_meta_for_spr==1){ $this->Spr->usemeta = 1; }
       else{ $this->Spr->usemeta = 0; }
       $this->Spr->useshort = 0;
       $this->Spr->useimg = 0;
       $this->Spr->usemove = 0;
       $this->Spr->script = "/admin/index.php?module=".$this->Spr->module."&spr=".$this->Spr->spr."&id_cat=".$this->id_cat."&id_param=".$this->id."&uselevels=".$this->Spr->uselevels."&usemeta=".$this->Spr->usemeta."&useshort=".$this->Spr->useshort."&useimg=".$this->Spr->useimg."&usemove=".$this->Spr->usemove."&display=".$this->Spr->display."&start=".$this->Spr->start."&sort=".$this->Spr->sort."&fln=".$this->Spr->fln.'&module_name='.$this->Spr->module_name.'&root_script='.$this->Spr->root_script.'&parent_script='.$this->Spr->parent_script.'&parent_id='.$this->Spr->parent_id;
       // echo '<br> $this->Spr->parent_script='.$this->Spr->root_script;
       echo "<script>window.location.href='".$this->Spr->script."';</script>";
       //$this->Spr->show( $this->user_id, $this->Spr->module, $this->Spr->display, $this->Spr->sort, $this->Spr->start, $this->Spr->spr );
    } //end of function  ShowValues()

    // ================================================================================================
    // Function : ShowFormGetCopyOfParams()
    // Version : 1.0.0
    // Date : 18.04.2008
    // Parms :
    // Returns : true,false / Void
    // Description : show form for copy parameters from one category to another
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 18.04.2008
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function ShowFormGetCopyOfParams( $id_del=NULL )
    {
       ?>
       <script type="text/javascript">
        var form = "";
        var submitted = false;
        var error = false;
        var error_message = "";

        function check_select(field_name, field_default, message) {
          if (form.elements[field_name] && (form.elements[field_name].type != "hidden")) {
            var field_value = form.elements[field_name].value;
            if (field_value == field_default) {
              error_message = error_message + "* " + message + "\n";
              error = true;
            }
          }
        }

        function check_form_move(form_name) {
          error_message = '';
          if (submitted == true) {
            alert("<?=$this->Msg->show_text('MSG_FRONT_ERR_FORM_ALREADY_SUBMITED');?>");
            return false;
          }

          error = false;
          form = form_name;

          check_select("id_cat_move_from", '', "<?=$this->Msg->show_text('ERR_EMPTY_CATEGORY_COPY_PARAMS_FROM');?>");
          check_select("id_cat_move_to", '', "<?=$this->Msg->show_text('ERR_EMPTY_CATEGORY_COPY_PARAMS_TO');?>");

          if (error == true) {
            alert(error_message);
            return false;
          } else {
            submitted = true;
            return true;
          }
        }
        </script>
        <?

        //phpinfo();
        /* Write Form Header */
        $this->Form->WriteHeader( $this->script, 'onsubmit="return check_form_move(this);"' );
        $this->Form->Hidden( 'id', $this->id );
        $this->Form->Hidden( 'move', $this->move );
        $this->Form->Hidden( 'group', $this->group );
        $this->Form->Hidden( 'id_cat', $this->id_cat );
        $this->Form->Hidden( 'display', $this->display );
        $this->Form->Hidden( 'start', $this->start );
        $this->Form->Hidden( 'sort', $this->sort );
        $this->Form->Hidden( 'fln', $this->fln );
        $this->Form->Hidden( 'srch', $this->srch );
        $this->Form->Hidden( 'fltr', $this->fltr );
        $this->Form->Hidden( 'fltr2', $this->fltr2 );
        $this->Form->Hidden( 'task', 'copy_params_to_category' );

        $arr_categs = $this->GetCatalogInArray(NULL, $this->Msg->show_text('TXT_SELECT_CATEGORY'), NULL, NULL, 0, 'back', 1, 0, 1);
        //print_r($arr_categs);

        AdminHTML::PanelSubH( $this->Msg->show_text('TXT_COPY_PARAMS_FROM_CATEGORY_TO_CATEGORY' ) );
        AdminHTML::PanelSimpleH();
        ?>
        <table border="0" cellspacing="1" cellpading="0" class="EditTable">
         <tr>
          <td valign="top"><b><?=$this->Msg->show_text('TXT_SELECT_CATEGORY_FROM_COPY');?>:</b></td>
          <td>
           <?
           //if( !isset($this->id_cat_move_from) OR empty($this->id_cat_move_from) ) $this->id_cat_move_from = $this->id_cat;
           $this->Form->Select( $arr_categs, 'id_cat_move_from', 'categ='.$this->id_cat_move_from );
           ?>
           <br>
           <?$this->Form->CheckBox('use_parent_params', '1', '1');?><?=$this->Msg->show_text('TXT_COPY_PARAMS_FROM_PARENT_CATEGORY');?>
          </td>
         </tr>
         <tr>
          <td>
           <div name="debug" id="debug">
           <?
           for($i=0;$i<count($id_del);$i++){
               $this->Form->Hidden( 'id_del[]', $id_del[$i] );
               echo ($i+1).'. '.$this->Spr->GetNameByCod(TblModCatalogPropSprName, $id_del[$i]);?><br/><?
           }//end for
           ?>
           </div>
          </td>
         </tr>
         <tr>
          <td><b><?=$this->Msg->show_text('TXT_SELECT_CATEGORY_TO_COPY');?>:</b></td>
          <td>
           <?
           if( !isset($this->id_cat_move_to) OR empty($this->id_cat_move_to) ) $this->id_cat_move_to = $this->id_cat;
           $this->Form->Select( $arr_categs, 'id_cat_move_to', 'categ='.$this->id_cat_move_to );
           ?>
          </td>
         </tr>
         <tr>
          <td></td>
          <td>
           <?=$this->Form->Button('submit', $this->Msg->show_text('BTN_COPY'), 50);?>
          </td>
         </tr>
        </table>
        <?
        AdminHTML::PanelSimpleF();
        AdminHTML::PanelSubF();
        $this->Form->WriteFooter();
    } //end of function ShowFormGetCopyOfParams()

    // ================================================================================================
    // Function : CopyParamsToCateg()
    // Version : 1.0.0
    // Date : 18.04.2008
    // Parms :
    // Returns : true,false / Void
    // Description : Copy parameters from one category to another
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 18.04.2008
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================
    function CopyParamsToCateg( $id_del=NULL )
    {
       $params = $this->IsParams($this->id_cat_move_from, $this->use_parent_params);
       if( $params==0 ) return false;
       $params_row = $this->GetParams($this->id_cat_move_from, $this->use_parent_params);
       //echo '<br>$this->use_parent_params='.$this->use_parent_params.' $params_row=';print_r($params_row);
       $current_id_cat = $this->id_cat;
       for ($i=0;$i<count($params_row);$i++){
           $this->id='';
           $this->id_cat = $this->id_cat_move_to;
           $this->type = $params_row[$i]['type'];
           $this->is_img = $params_row[$i]['is_img'];
           $this->modify = $params_row[$i]['modify'];
           $this->name = $this->Spr->GetByCod(TblModCatalogParamsSprName, $params_row[$i]['id']);
           $this->prefix = $this->Spr->GetByCod(TblModCatalogParamsSprPrefix, $params_row[$i]['id']);
           $this->sufix = $this->Spr->GetByCod(TblModCatalogParamsSprSufix, $params_row[$i]['id']);
           //echo '<br>$this->name='.$this->name; print_r($this->name);
           $res = $this->save();
           //echo '<br />$res='.$res;
           if( !$res) return false;
           //copy possible varians of values for parameters
           if ( $this->type=='3' || $this->type=='4' ) {
               //$tblname_old = $this->BuildNameOfValuesTable($params_row[$i]['id_categ'], $params_row[$i]['id']);
               //$tblname_new = $this->BuildNameOfValuesTable($this->id_cat, $this->id);
               //$q = "INSERT INTO `".$tblname_new."` SELECT * FROM `".$tblname_old."`";
               $q = "SELECT *
                     FROM `".TblModCatalogParamsVal."`
                     WHERE `id_cat`='".$params_row[$i]['id_categ']."'
                     AND `id_param`='".$params_row[$i]['id']."'
                    ";
               $res = $this->Right->Query($q, $this->user_id, $this->module);
               //echo '<br>$q='.$q.' $res='.$res.' $this->Right->result='.$this->Right->result;
               $rows = $this->Right->db_GetNumRows();
               for($j=0;$j<$rows;$j++){
                    $param_vals[$j] = $this->Right->db_FetchAssoc();
               }
               for($j=0;$j<$rows;$j++){
                    $row = $param_vals[$j];
                    $q = "SELECT MAX(cod) as max FROM `".TblModCatalogParamsVal."` WHERE 1 ";
                    $res = $this->Right->Query($q, $this->user_id, $this->module);
                    //echo '<br><br><br>$q='.$q.' $res='.$res.' $this->Right->result='.$this->Right->result;
                    $row_max = $this->Right->db_FetchAssoc();
                    if(!empty($row_max['max']))
                        $max = $row_max['max']+1;
                    else
                        $max = 1;
                    $q = "INSERT INTO `".TblModCatalogParamsVal."` SET
                          `cod`='".$max."',
                          `lang_id`='".$row['lang_id']."',
                          `name`='".addslashes($row['name'])."',
                          `move`='".$row['move']."',
                          `id_cat`='".$this->id_cat."',
                          `id_param`='".$this->id."'
                         ";
                    if( isset($row['mtitle'])) $q.=", `mtitle`='".addslashes($row['mtitle'])."'";
                    if( isset($row['mdescr'])) $q.=", `mdescr`='".addslashes($row['mdescr'])."'";
                    if( isset($row['mkeywords'])) $q.=", `mkeywords`='".addslashes($row['mkeywords'])."'";
                    if( isset($row['img'])) $q.=", `img`='".$row['img']."'";

                    $res = $this->Right->Query($q, $this->user_id, $this->module);
                    //echo '<br>$q='.$q.' $res='.$res.' $this->Right->result='.$this->Right->result;
                    if( !$res OR !$this->Right->result) return false;
               }
               /*
               //copy images from spr, if they exists
               $fulldir = Spr_Img_Path.$tblname_old;
               if (is_dir($fulldir)) {
                   $newdir = Spr_Img_Path.$tblname_new;
                   mkdir($newdir, 0777);
                   $this->CopyDir($fulldir, $newdir);
               }
               */

           }

       }//end for
       $this->id_cat = $current_id_cat;
       //echo '<br>count($params_row)='.count($params_row).' $i='.$i;
       if(count($params_row)!=$i) return false;
       return $i;
    }//end of function CopyParamsToCateg()

   // ================================================================================================
   // Function : CopyDir()
   // Version : 1.0.0
   // Date : 09.07.2009
   // Parms :
   // Returns : true,false / Void
   // Description : Copy direcory with files
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 09.07.2009
   // Reason for change : Reason Description / Creation
   // Change Request Nbr:
   // ================================================================================================
   function CopyDir( $fulldir, $newdir )
   {
       if (is_dir($fulldir)) {
           if ($dh = opendir($fulldir)) {
               //echo '<br> $dh='.$dh ;
               while (($file = readdir($dh)) !== false) {
                 //echo '<br>$file='.$file.' filetype('.$fulldir.'/'.$file.')='.filetype($fulldir.'/'.$file);
                 if ($file=='.' || $file=='..') continue;
                 if ( filetype($fulldir.'/'.$file)=='dir' ) {
                     $newdir2 = $newdir.'/'.$file;
                     mkdir($newdir2, 0777);
                     $this->CopyDir( $fulldir.'/'.$file, $newdir2 );
                 }
                 else copy($fulldir.'/'.$file, $newdir.'/'.$file );
               }
               closedir($dh);
           }
       }
   }//end of function CopyDir()

} // end of class CatalogParams
