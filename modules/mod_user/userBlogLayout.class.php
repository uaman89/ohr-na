<?

class userBlogLayout extends User{

       var $db=NULL;
       var $Msg=NULL;
       var $logon=NULL;
       var $Spr=NULL;
       var $Form = NULL;

       var $whattodo = NULL;
       var $referer_page = NULL;
       var $TextMessages = NULL;
       var $Catalog = NULL;


    function __construct($session_id=NULL, $user_id=NULL){
        ( $session_id   !="" ? $this->session_id  = $session_id   : $this->session_id  = NULL );
                ( $user_id      !="" ? $this->user_id     = $user_id      : $this->user_id     = NULL );

                if (empty($this->db))  $this->db = Singleton::getInstance('DB');
                if (empty($this->Msg)) $this->Msg = new ShowMsg();
                $this->Msg->SetShowTable(TblModUserSprTxt);
                if (empty($this->Logon)) $this->Logon = new  UserAuthorize();
                if (empty($this->Spr)) $this->Spr = new  SysSpr();
                if (empty($this->Form)) $this->Form = new FrontForm('form_mod_user');
                $this->multiUser = $this->Msg->GetMultiTxtInArr(TblModUserSprTxt);
                if(empty($this->Catalog)) $this->Catalog = Singleton::getInstance('Catalog');
    }



    function show_JS(){
        ?>
         <script type="text/javascript">
         function verify() {
                var themessage = "<div style='text-align: left;'>Перевірте правильність заповнення полів реестрації.<br/> Зверніть увагу на слідуючі помилки:<br/><br/><span style='color:red;'>";
                document.getElementById('tiny').value = tinyMCE.get('tiny').getContent();
                if (document.forms.newBlog.headerBlog.value=="" ) {
                    themessage = themessage + " - Ви не ввели заголовок публікації!<br/>";
                }

                if (document.forms.newBlog.headerBlog.value=="" ) {
                    themessage = themessage + " - Введіть саму публікацію!<br/>";
                }

                if (themessage == "<div style='text-align: left;'>Перевірте правильність заповнення полів реестрації.<br/> Зверніть увагу на слідуючі помилки:<br/><br/><span style='color:red;'>")
                {

                    SaveForm();
                    return true;
                }
                else
                   $.fancybox(themessage+"</span></div>");
                return false;
            }
         function SaveForm(){
              $.ajax({
                   type: "POST",
                   data: $("#newBlog").serialize() ,
                   url: "<?=_LINK;?>saveNewBlogRecord",
                   beforeSend : function(){
                       $("#CatformAjaxLoader").width($("#catalogBody").width()).height($("#catalogBody").height()+20).fadeTo("fast", 0.4);
                        //$(Did).show("fast");
                    },
                   success: function(html){
                       $("#CatformAjaxLoader").fadeOut("fast",function(){
                           //$("#contentBox").html(html);
                           $(".headerBlogAddNew").html("Редагувати запис:"+html)
                       });


                   }
              });
         }
         </script>
         <?
    }

    function addNewRecordShowRedactor(){
        $this->show_TinyMCE();
        $this->show_JS();
        $SysGroup = new SysUser();
    // echo 'this->login = '.$this->login ;
     //echo '<br/>this->Logon->login = '.$this->Logon->login ;
     $q="SELECT * FROM `".TblModUser."`,`".TblSysUser."` WHERE `".TblModUser."`.`sys_user_id`=".$this->Logon->user_id." AND `".TblSysUser."`.id=".$this->Logon->user_id."";
     $res = $this->db->db_Query($q);
     //echo '<br>'.$q.' $res='.$res.' $this->db->result='.$this->db->result;
     if ( !$res OR !$this->db->result ) return false;
     $mas = $this->db->db_FetchAssoc();
     $update=false;
     if($this->recordId!=NULL){
         $update=true;
         $q="SELECT * FROM ".TblModUserBlog." WHERE `id_user`='".$this->Logon->user_id."' AND `id`='".$this->recordId."'";
         $ress = $this->db->db_Query($q);
         $rowcount=$this->db->db_GetNumRows();
         $val = $this->db->db_FetchAssoc();
         if($rowcount==1){
             $this->headerBlog=$val['title'];
             $this->blogContent=$val['content'];
         }
     }
     $_SESSION['sys_user_id']=$this->Logon->user_id;
        ?>

        <div id="catalogBox">
            <?if($this->recordId!=NULL){ ?>
            <span class="MainHeaderText">Редагувати запис</span>
            <?}else{  ?>
            <span class="MainHeaderText">Додати запис</span>
            <?}?>



            <div id="profileMenuHandler">
                <div id="leftProfileMenuPart">
                    <? if(is_file($_SERVER['DOCUMENT_ROOT']."/images/mod_blog/".$this->Logon->user_id."/".$mas['discount'])){?>
                    <br/>
                           <img class="avatarImage profileAvatar" src="<?=$this->Catalog->ShowCurrentImageExSize("/images/mod_blog/".$this->Logon->user_id."/".$mas['discount'], 70, 70, 'center', 'center', '85', NULL, NULL, NULL, true);?>" alt="" />
                            <?}else{?>
                      <br/><img class="avatarImage profileAvatar" width="70" height="70" src="/images/design/noAvatar.gif"/>
                           <?}?>
                    <?if(empty($mas['name'])){?>
                    <span class="profileName"><?=$mas['login']?></span>
                    <?}else{?>
                    <span class="profileName"><?=$mas['name']." ".$mas['country']?></span>
                    <?}?>
                </div>
                <div id="centerProfileMenuPart">
                    <a class="blogBtnUserProfile selectedPunktClass" href="/myaccount/blog/">Блог</a>
                    <a class="editProfile" href="/myaccount/">Редагувати Профіль</a>
                    <a class="commentsProfile" href="/myaccount/comments/">Коментарі</a>
                </div>
                <div id="rightProfileMenuPart"></div>
            </div>


            <div id="catalogBody" style="background: #fafafa">
                <div id="CatformAjaxLoader"></div>
                <?if($this->recordId!=NULL){ ?>
            <span  class="headerBlogAddNew">Редагувати запис:</span><br/>
            <input type="button" style="float: right" class="btnCatalogImgUpload" onclick="location.href='http://1ztua.seotm.biz/myaccount/blog/'" name="save_reg_data" value="Новий запис"/>
            <?}else{?>
            <span  class="headerBlogAddNew">Додати запис:</span>
            <?}?>
            <span class="zagolovok">Заголовок запису:</span>

            <form method="post" action="#" name="newBlog" id="newBlog">
                <?if($this->recordId!=NULL){ ?>
                <input type="HIDDEN" name="recordId" value="<?=$this->recordId?>"/>
                <?}?>
                  <input class="headerOfSingleBlogInput" type="text" name="headerBlog" value="<?=$this->headerBlog;?>"/><br style="clear: both;"/>
                  <textarea name="blogContent" style="width:100%" class="tiny" id="tiny"><?=$this->blogContent?></textarea>
            </form>
            <?if($this->recordId!=NULL){ ?>
            <input type="button" style="float: left" class="btnCatalogImgUpload" onclick="verify()" name="save_reg_data" class="submitBtn<?=_LANG_ID?>" value="Зберігти" />
            <?}else{?>
            <input type="button" style="float: left" class="btnCatalogImgUpload" onclick="verify()" name="save_reg_data" class="submitBtn<?=_LANG_ID?>" value="Публікація" />
            <?}?>
        </div>
        </div>
        <?
    }

    function saveNewBlogRecord(){

        if($this->recordId!=NULL){
            $q="UPDATE `".TblModUserBlog."` SET
                `title`='".$this->headerBlog."',
                `content`='".$this->blogContent."',
                `dttm`='".date("Y-m-d")." ".date("G:i:s")."',
                `is_comment`=1,
                `visible`=1
                WHERE `id`='".$this->recordId."' AND `id_user`='".$this->Logon->user_id."'";
            $res = $this->db->db_Query($q);
            if($res)
            echo "<span style='float:right; color:green;margin-left:10px;'>Ваш запис успішно змінений</span>";
            else echo "<span style='float:right; color:red;'>Під час внесення змін виникла помилка</span>";
        }else{
            $q="INSERT INTO `".TblModUserBlog."` SET
                `id_user`='".$this->Logon->user_id."',
                `title`='".$this->headerBlog."',
                `content`='".$this->blogContent."',
                `dttm`='".date("Y-m-d")." ".date("G:i:s")."',
                `is_comment`=1,
                `visible`=1";
            $res = $this->db->db_Query($q);
            $blogId=$this->db->db_GetInsertID();
            if($res){?>
            <span style='float:right; color:green;'>Ваш запис успішно опублікований</span>
                <br/><script type="text/javascript">location.href='<?=$this->link($this->Logon->user_id,$blogId)?>'</script>
            <?}else echo "<span style='float:right; color:red;'>Під час публікації виникла помилка</span>";
        }

    }

    function showUserBlogRecords(){
        $q="SELECT * FROM `".TblModUser."`,`".TblSysUser."` WHERE `".TblModUser."`.`sys_user_id`=".$this->userId." AND `".TblSysUser."`.id=".$this->userId."";
        $res = $this->db->db_Query($q);
        $mas = $this->db->db_FetchAssoc();//die(mysql_error());
        $userImage="";

        if(isset($mas['discount'])) $userImage=$mas['discount'];

        $userBlogArr=$this->getUsersBlogArr();//print_r($userBlogArr);
        $rowsCount=count($userBlogArr);
       // $mainContent=strpos($mas[''],"<!-- pagebreak -->");
         ?>

        <div id="catalogBox">
            <?if($mas['group_id']!=7){?>
            <div id="profileMenuHandlerBlog" style="background: none; border: none;">
                <div id="leftProfileMenuPartBlog"  style="width: 409px;">
                    <? if(is_file($_SERVER['DOCUMENT_ROOT']."/images/mod_blog/".$this->userId."/".$userImage)){?>
                    <br/>
                    <img class="avatarImage profileAvatarBlog" src="<?=$this->Catalog->ShowCurrentImageExSize("/images/mod_blog/".$this->userId."/".$userImage, 102, 102, 'center', 'center', '85', NULL, NULL, NULL, true);?>" alt="" />
                           <?if(empty($mas['name'])){?>
                    <span class="profileNameBlog"><?=$mas['login']?><p style="color: #585858; font-size: 14px;line-height: 0px;">блоги</p>
                           </span>
                    <?}else{?>
                    <span class="profileNameBlog"><?=$mas['name']." ".$mas['country']?><p style="color: #585858; font-size: 14px;line-height: 0px;">блоги</p>
                           </span>
                    <?}?>
                            <?}else{?>
                      <br/><img class="avatarImage profileAvatar" width="70" height="70" src="/images/design/noAvatar.gif"/>
                           <?}?>

                </div>
                <div id="centerProfileMenuPartBlog" style="width: 188px;">
                    <a class="blogBtnUserProfileBlog selectedPunktClassBlog"  href="/blog/<?=$this->userId?>/">Блог</a>
                    <a class="editProfileBlog" style="width: 112px;" href="/blog/user/<?=$this->userId?>/about/">Про мене</a>
                </div>
                <div id="rightProfileMenuPartBlog" style="width: 89px;"></div>
            </div>
            <?}else{//=============expert club begin?>
             <div id="profileMenuHandlerBlogExpert" style="<?
             if(is_file($_SERVER['DOCUMENT_ROOT']."/images/mod_blog/".$this->userId."/".$mas['expertImg'])){
                 echo "background:url('/images/mod_blog/".$this->userId."/".$mas['expertImg']."') no-repeat bottom left;";
             }else{
                 echo "background: none;";
                 }
             ?>border: none;">
              <span class="profileNameBlogExpert" style="position: absolute;margin-left: 321px; margin-top: 67px;"><?=$mas['name']." ".$mas['country']?>
                           <p style="color: #585858;text-align: right; font-size: 14px;line-height: 0px; font-style: italic;font-weight: normal">експертний клуб</p>
                           </span>
                <div id="leftProfileMenuPartBlogExpert"  style="width: 409px;">

                </div>
                <div id="centerProfileMenuPartBlogExpert" style="width: 188px;">
                    <a class="blogBtnUserProfileBlogExpert selectedPunktClassBlogExpert" style="width: 76px;"  href="/blog/<?=$this->userId?>/">Блог</a>
                    <a class="editProfileBlogExpert" style="width: 112px;" href="/blog/user/<?=$this->userId?>/about/">Про мене</a>
                </div>
                <div id="rightProfileMenuPartBlogExpert" style="width: 89px;"></div>
            </div>
            <?}//===========expert club end?>

            <div id="catalogBody" style="padding-left: 0px; padding-right: 0px; background: none;width: 100%;">
<!--                <span class="pageHandler">-->
                <?$rows_all = count($this->getUsersBlogArr('nolimit'));
                $link = $this->link($this->userId);
                ?>
                 <?if($rowsCount>$this->display){?>
                <div class="PageNaviBack">

                <div class="page-navi-class" style="padding-top: 15px;height: 40px;"><?$this->Form->WriteLinkPagesStatic( $link, $rows_all, $this->display, $this->start, $this->sort, $this->page);?></div>
                </div>
<!--                </span>-->
                <?}?>
                <?
                if($rowsCount==0) echo "<h1 style='text-align: center;line-height: 200px;'>У даного користувача блоги відсутні</h1> ";
                $items=0;
                for( $i=0; $i<$rowsCount; $i++ )
                {
                  $row=$userBlogArr[$i];
                  if($i==0)
                    $items = $row['id'];
                  else
                    $items = $items.', '.$row['id'];
                }
               //echo $q,'<br/> res= '.$res;

                /*$ModulesPlug = new ModulesPlug();
                $id_module = $ModulesPlug->GetModuleIdByPath( '/modules/mod_article/article.backend.php' );*/
                $id_module = 87;
                if(!isset($this->Comments))
                    $this->Comments = new FrontComments($id_module);
                // Масив кількості коментарів на блог
                $commentCount = $this->Comments->GetCommentsCount($items);

                for($i=0;$i<$rowsCount;$i++){
                    $row=$userBlogArr[$i];
                    $shortContentEnd=strpos($row['content'],"<!-- pagebreak -->");
                     if($shortContentEnd==0){
                          $mainContent=$row['content'];
                     }else $mainContent=substr($row['content'],0,$shortContentEnd);
                    $day=$row['dttm'][5].$row['dttm'][6];
                    $month=$row['dttm'][8].$row['dttm'][9];
                    $year=$row['dttm'][0].$row['dttm'][1].$row['dttm'][2].$row['dttm'][3];
                    $time=$row['dttm'][11].$row['dttm'][12].$row['dttm'][13].$row['dttm'][14].$row['dttm'][15];
                    ?>
                <div class="newsColumnLast" style="float: left; display: block;width: 100%;">
                    <span class="blogData"><?=$day.".".$month.".".$year." - ".$time?></span>
                    <span class="blogSingleHeader"><?=$row['title']?></span>
                    <div class="singleBlogContent"><?=$mainContent?></div>
                    <div class="news_colum1_1_footer">
                        <div class="news_colum1_1_footer_text"><img src="/images/design/oblako.png" alt="" />
                        <?$link = "/blog/".$this->userId."/entry/".$row['id'].'#commentsBlock';?>
                        <a href="<?=$link;?>">Коментарів - <?
                            if(isset($commentCount[$row['id']]))
                                echo $commentCount[$row['id']];
                            else
                                echo '0';
                                ?></a>

                        <? if($this->userId==$this->Logon->user_id) echo "<a class='editLink' href='"."/myaccount/blog/edit/".$row['id']."/'>Редагувати запис</a><a style='margin-left:10px' href='/blog/".$row['id']."/user/".$this->Logon->user_id."/delete'>Видалити запис</a>";?></div>
                        <a class="news_colum1_1_footer_but" style="text-decoration: none" href="<?="/blog/".$this->userId."/entry/".$row['id']?>">Читати</a>
                    </div>
                </div>
                    <?
                }

                $link = $this->link($this->userId);
                ?><div class="page-navi-class" style="padding-top: 10px;"><?$this->Form->WriteLinkPagesStatic( $link, $rows_all, $this->display, $this->start, $this->sort, $this->page);?></div>
            </div>
        </div>

         <?
    }

    function getUsersBlogArr($limit='limit'){
        $q="SELECT * FROM ".TblModUserBlog." WHERE `id_user`='".$this->userId."' AND `visible`=1 ORDER BY `dttm` DESC";
        if($limit=='limit') $q = $q." LIMIT ".$this->start.", ".($this->display);
                $res = $this->db->db_Query($q);
        if( !$res or !$this->db->result) return false;
        $rows = $this->db->db_GetNumRows($res);
        //echo '<br>$rows='.$rows;
        $arr=array();
        for($i=0;$i<$rows;$i++){
            $row = $this->db->db_FetchAssoc();
            $arr[$i]=$row;
        }
        //print_r($arr);
        return $arr;
    }

    function ShowUserInfo(){
        $q="SELECT * FROM `".TblModUser."`,`".TblSysUser."` WHERE `".TblModUser."`.`sys_user_id`=".$this->userId." AND `".TblSysUser."`.id=".$this->userId."";
        $res = $this->db->db_Query($q);
        $mas = $this->db->db_FetchAssoc();
        $userImage="";
        $day=$mas['city'][5].$mas['city'][6];
        $month=$mas['city'][8].$mas['city'][9];
        $year=$mas['city'][0].$mas['city'][1].$mas['city'][2].$mas['city'][3];
        if(isset($mas['discount'])) $userImage=$mas['discount'];
        ?>
         <div id="catalogBox">


             <?//=$this->makeImageGrey($userImage,$_SERVER['DOCUMENT_ROOT']."/images/mod_blog/".$this->userId."/")?>

         <?if($mas['group_id']!=7){?>
            <div id="profileMenuHandlerBlog" style="background: none; border: none;">
                <div id="leftProfileMenuPartBlog"  style="width: 409px;">
                    <? if(is_file($_SERVER['DOCUMENT_ROOT']."/images/mod_blog/".$this->userId."/".$userImage)){?>
                    <br/>
                    <img class="avatarImage profileAvatarBlog" src="<?=$this->Catalog->ShowCurrentImageExSize("/images/mod_blog/".$this->userId."/".$userImage, 102, 102, 'center', 'center', '85', NULL, NULL, NULL, true);?>" alt="" />
                           <?if(empty($mas['name'])){?>
                    <span class="profileNameBlog"><?=$mas['login']?><p style="color: #585858; font-size: 14px;line-height: 0px;">блоги</p>
                           </span>
                    <?}else{?>
                    <span class="profileNameBlog"><?=$mas['name']." ".$mas['country']?><p style="color: #585858; font-size: 14px;line-height: 0px;">блоги</p>
                           </span>
                    <?}?>

                            <?}else{?>
                      <br/><img class="avatarImage profileAvatar" width="70" height="70" src="/images/design/noAvatar.gif"/>
                           <?}?>

                </div>
                <div id="centerProfileMenuPartBlog" style="width: 188px;">
                    <a class="blogBtnUserProfileBlog"  href="/blog/<?=$this->userId?>/">Блог</a>
                    <a class="editProfileBlog selectedPunktClassBlog" style="width: 112px;" href="/blog/user/<?=$this->userId?>/about/">Про мене</a>
                </div>
                <div id="rightProfileMenuPartBlog" style="width: 89px;"></div>
            </div>
            <?}else{//=============expert club begin?>
             <div id="profileMenuHandlerBlogExpert" style="<?
             if(is_file($_SERVER['DOCUMENT_ROOT']."/images/mod_blog/".$this->userId."/".$mas['expertImg'])){
                 echo "background:url('/images/mod_blog/".$this->userId."/".$mas['expertImg']."') no-repeat bottom left;";
             }else{
                 echo "background: none;";
                 }
             ?>border: none;">
              <span class="profileNameBlogExpert" style="position: absolute;margin-left: 321px; margin-top: 67px;"><?=$mas['name']." ".$mas['country']?>
                           <br style="clear: both"/><p style="color: #585858;text-align: right; font-size: 14px;line-height: 0px; font-style: italic;font-weight: normal">експертний клуб</p>
                           </span>
                <div id="leftProfileMenuPartBlogExpert"  style="width: 409px;">

                </div>
                <div id="centerProfileMenuPartBlogExpert" style="width: 188px;">
                    <a class="blogBtnUserProfileBlogExpert " style="width: 76px;"  href="/blog/<?=$this->userId?>/">Блог</a>
                    <a class="editProfileBlogExpert selectedPunktClassBlogExpert" style="width: 112px;" href="/blog/user/<?=$this->userId?>/about/">Про мене</a>
                </div>
                <div id="rightProfileMenuPartBlogExpert" style="width: 89px;"></div>
            </div>
            <?}//===========expert club end?>
         <div id="catalogBody" style="padding-left: 0px; padding-right: 0px; background: none;width: 100%;">
             <ul class="CatFormUl" style="padding-left: 35px;width: 250px;float: left;">
                 <?if(!empty($mas['country'])){?>
                 <li>Прізвище:
                      <span id="nameOfPred" class="aboutMeSpan"><?=$mas['country']?> </span>
                  </li>
                  <?}?>
                  <?if(!empty($mas['name'])){?>
                  <li>Ім'я:
                      <span id="nameOfPred" class="aboutMeSpan"><?=$mas['name']?></span>
                  </li>
                  <?}?>
                  <li>
                      Стать:
                          <span id="nameOfPred" class="aboutMeSpan"><?if($mas['state']=='m') echo "Чоловіча";else echo "Жіноча";?></span>
                  </li>
                  <?if(!empty($day) && !empty($month) && !empty($year)){?>
                  <li>
                      Дата народження:<span id="nameOfPred" class="aboutMeSpan"><?=$day.".".$month.".".$year?></span>
                  </li>
                  <?}?>
                  <?if(!empty($mas['www'])){?>
                  <li>Сайт:
                      <span id="nameOfPred" class="aboutMeSpan"><?=$mas['www']?></span>
                  </li>
                  <?}?>
                  <?if(!empty($mas['phone'])){?>
                  <li>Телефон:
                      <span id="nameOfPred" class="aboutMeSpan"><?=$mas['phone']?></span>
                  </li>
                  <?}?>
                  <?if(!empty($mas['phone_mob'])){?>
                  <li>Facebook:
                      <span id="nameOfPred" class="aboutMeSpan" ><?=$mas['phone_mob']?></span>
                  </li>
                  <?}?>
                  <?if(!empty($mas['fax'])){?>
                  <li>Вконтакті:
                      <span id="nameOfPred" class="aboutMeSpan"><?=$mas['fax']?></span>
                  </li>
                  <?}?>
                  <?if(!empty($mas['bonuses'])){?>
                  <li>Twitter:
                      <span id="nameOfPred" class="aboutMeSpan"><?=$mas['bonuses']?></span>
                  </li>
                  <?}?>
                </ul>
             <?if(!empty($mas['aboutMe'])){?>
             <div class="aboutMeTexBox">
                 <span style="font-weight: bold;">Коротко про мене:</span><br/><br/>
                          <?=$mas['aboutMe']?></div>
             <?}?>
        </div>
         </div>
        <?
    }

    function ShowCurrentArticle(){
        if(isset($this->recordId)){
            $q="SELECT * FROM ".TblModUserBlog." WHERE `id`='".$this->recordId."' AND `visible`=1";
            $res = $this->db->db_Query($q);
            $row = $this->db->db_FetchAssoc();

            $q="SELECT * FROM `".TblModUser."`,`".TblSysUser."` WHERE `".TblModUser."`.`sys_user_id`=".$this->userId." AND `".TblSysUser."`.id=".$this->userId."";
             $res = $this->db->db_Query($q);
            $mas = $this->db->db_FetchAssoc();
            $userImage="";

        if(isset($mas['discount'])) $userImage=$mas['discount'];

        $userBlogArr=$this->getUsersBlogArr();//print_r($userBlogArr);
       // $mainContent=strpos($mas[''],"<!-- pagebreak -->");
         ?>

        <div id="catalogBox">
             <?if($mas['group_id']!=7){?>
            <div id="profileMenuHandlerBlog" style="background: none; border: none;">
                <div id="leftProfileMenuPartBlog"  style="width: 409px;">
                    <? if(is_file($_SERVER['DOCUMENT_ROOT']."/images/mod_blog/".$this->userId."/".$userImage)){?>
                   <br/>
                    <img class="avatarImage profileAvatarBlog" src="<?=$this->Catalog->ShowCurrentImageExSize("/images/mod_blog/".$this->userId."/".$userImage, 102, 102, 'center', 'center', '85', NULL, NULL, NULL, true);?>" alt="" />
                           <?if(empty($mas['name'])){?>
                    <span class="profileNameBlog"><?=$mas['login']?><p style="color: #585858; font-size: 14px;line-height: 0px;">блоги</p>
                           </span>
                    <?}else{?>
                    <span class="profileNameBlog"><?=$mas['name']." ".$mas['country']?><p style="color: #585858; font-size: 14px;line-height: 0px;">блоги</p>
                           </span>
                    <?}?>
                            <?}else{?>
                      <br/><img class="avatarImage profileAvatar" width="70" height="70" src="/images/design/noAvatar.gif"/>
                           <?}?>

                </div>
                <div id="centerProfileMenuPartBlog" style="width: 188px;">
                    <a class="blogBtnUserProfileBlog selectedPunktClassBlog"  href="/blog/<?=$this->userId?>/">Блог</a>
                    <a class="editProfileBlog" style="width: 112px;" href="/blog/user/<?=$this->userId?>/about/">Про мене</a>
                </div>
                <div id="rightProfileMenuPartBlog" style="width: 89px;"></div>
            </div>
            <?}else{//=============expert club begin?>
             <div id="profileMenuHandlerBlogExpert" style="<?
             if(is_file($_SERVER['DOCUMENT_ROOT']."/images/mod_blog/".$this->userId."/".$mas['expertImg'])){
                 echo "background:url('/images/mod_blog/".$this->userId."/".$mas['expertImg']."') no-repeat bottom left;";
             }else{
                 echo "background: none;";
                 }
             ?>border: none;">
              <span class="profileNameBlogExpert" style="position: absolute;margin-left: 321px; margin-top: 67px;"><?=$mas['name']." ".$mas['country']?>
                           <p style="color: #585858;text-align: right; font-size: 14px;line-height: 0px; font-style: italic;font-weight: normal">експертний клуб</p>
                           </span>
                <div id="leftProfileMenuPartBlogExpert"  style="width: 409px;">

                </div>
                <div id="centerProfileMenuPartBlogExpert" style="width: 188px;">
                    <a class="blogBtnUserProfileBlogExpert selectedPunktClassBlogExpert" style="width: 76px;"  href="/blog/<?=$this->userId?>/">Блог</a>
                    <a class="editProfileBlogExpert" style="width: 112px;" href="/blog/user/<?=$this->userId?>/about/">Про мене</a>
                </div>
                <div id="rightProfileMenuPartBlogExpert" style="width: 89px;"></div>
            </div>
            <?}//===========expert club end?>


            <div id="catalogBody" style="padding-left: 0px; padding-right: 0px; background: none;width: 100%;">
                <?

                    $day=$row['dttm'][5].$row['dttm'][6];
                    $month=$row['dttm'][8].$row['dttm'][9];
                    $year=$row['dttm'][0].$row['dttm'][1].$row['dttm'][2].$row['dttm'][3];
                    $time=$row['dttm'][11].$row['dttm'][12].$row['dttm'][13].$row['dttm'][14].$row['dttm'][15];
                    ?>
                <div class="newsColumnLast" style="float: left; display: block;width: 100%;">
                    <span class="blogData"><?=$day.".".$month.".".$year." - ".$time?></span>
                    <span class="blogSingleHeader"><?=$row['title']?></span>
                    <div class="singleBlogContent"><?=$row['content']?></div>

                </div>
                  <? if($this->userId==$this->Logon->user_id) echo "<a class='editLink' href='"."/myaccount/blog/edit/".$row['id']."/'>Редагувати запис</a><a class='editLink' style='margin-left:10px' href='/blog/".$row['id']."/user/".$this->Logon->user_id."/delete'>Видалити запис</a>";?>
            </div>
        </div>

            <?


        //if( $Page->News->is_comments==1){
            if(!isset($this->Comments))
                $this->Comments = new FrontComments($this->module, $this->recordId);
             $this->Comments->ShowCommentsByModuleAndItem();
             ?> <div style="margin-left: 10px;"><?
             $this->Comments->FacebookComments();
              ?></div> <?

        }
    }
    function showLastBlogsUser($expert=false){
        $q="SELECT `".TblModUser."`.*,
            `".TblSysUser."`.`login`,
            `".TblModUserBlog."`.`dttm`,
            `".TblModUserBlog."`.`title`,
            `".TblModUserBlog."`.`id_user`,
            `".TblModUserBlog."`.`id` AS `ArticlId`
            FROM   `".TblModUserBlog."` INNER JOIN ( select max(id) as id from `".TblModUserBlog."` group by `id_user` ) dts ON dts.id=`".TblModUserBlog."`.id,
              `".TblModUser."`,
              `".TblSysUser."`
            WHERE `".TblModUserBlog."`.`id_user`=`".TblSysUser."`.`id`";
        $q.=" AND `".TblModUser."`.`sys_user_id`=`".TblSysUser."`.`id`";
        if(!empty($this->letter))
        $q.=" AND (`".TblModUser."`.`country` LIKE '".$this->letter."%' OR `".TblSysUser."`.`login` LIKE '".$this->letter."%')";
        if($expert) $q.=" AND `".TblSysUser."`.`group_id`=7";
        else $q.=" AND `".TblSysUser."`.`group_id`=5";
        $q.="
                ORDER BY `".TblModUserBlog."`.`dttm` DESC";
        $q = $q." LIMIT 5";
        $res = $this->db->db_Query($q);
       // echo '<br>$q='.$q;
        $rows = $this->db->db_GetNumRows();
        if($rows>0){
            //echo $q;
            ?>
            <div id="best_blogs">
                <div id="best_blogs_title">Останні блоги</div>
            <?
            for($i=0;$i<$rows;$i++){
                $row=$this->db->db_FetchAssoc();
                ?>
                <div class="blog_items">
                    <a href="<?=$this->link($row['sys_user_id'])?>">
                    <img src="<?=$this->Catalog->ShowCurrentImageExSize("/images/mod_blog/".$row['sys_user_id']."/".$row['discount'], 41, 41, 'center', 'center', '85', NULL, NULL, NULL, true);?>" alt="" />
                    </a>
                    <div class="blog_item_name"><?=$row['name']." ".$row['country']?></div>
                    <div class="blog_item_date"><?=$row['dttm']?></div>
                    <a style="text-align: left; margin-right: 20px;" href="<?=$this->link($row['id_user'],$row['ArticlId'])?>" class="blog_item_thema"><?=$row['title']?></a>
                </div>
                <?
                if($i%2!=0){
                    ?>
                <div style="float: left;display: block;width: 280px;"></div>
                    <?
                }
            }
            ?><br style="clear: both;"/><a class="more_blog" style="text-align: right; margin-right: 20px;" href="/users/">Переглянути всіх<img src="/images/design/down.png" alt="" /></a>
            </div><?
         }
    }
    function showBestBlogs($experts=false){
        // AND `visible`=1 LIMIT 5
        $module=87;


         $q="SELECT
                `id_item`,
                count(id_item) as `count`,
                `".TblModUserBlog."`.*,
                `".TblModUser."`.*,
                `".TblSysUser."`.`group_id`,
                `".TblModUserBlog."`.`id` as `ArticlId`
                FROM `".TblSysModComments."`,`".TblModUserBlog."`,`".TblModUser."`,`".TblSysUser."`
                WHERE `id_module`='".$module."'
                    AND `".TblModUserBlog."`.`id`=`id_item`
                    AND `".TblModUser."`.`sys_user_id`=`".TblModUserBlog."`.`id_user`
                    AND `".TblSysUser."`.`id`=`".TblModUserBlog."`.`id_user`";
         if($experts)
         $q.=" AND `".TblSysUser."`.`group_id`=7 ";
         else $q.=" AND `".TblSysUser."`.`group_id`=5 ";
               $q.= " GROUP BY id_item
                ORDER BY `count` DESC
                ";

            $res = $this->db->db_Query($q);
            $rows = $this->db->db_GetNumRows();
            if($rows>0){
            //echo $q;
            ?>
            <div id="best_blogs">
                <div id="best_blogs_title"><?if($experts) echo "Експертний клуб";else echo "Найкращі блоги";?></div>
            <?
            for($i=0;$i<$rows;$i++){
                $row=$this->db->db_FetchAssoc();
                ?>
                <div class="blog_items">
                    <a href="<?=$this->link($row['sys_user_id'])?>">
                    <img src="<?=$this->Catalog->ShowCurrentImageExSize("/images/mod_blog/".$row['sys_user_id']."/".$row['discount'], 41, 41, 'center', 'center', '85', NULL, NULL, NULL, true);?>" alt="" />
                    </a>
                    <div class="blog_item_name"><?=$row['name']." ".$row['country']?></div>
                    <div class="blog_item_date"><?=$row['dttm']?></div>
                    <a style="text-align: left; margin-right: 20px;" href="<?=$this->link($row['id_user'],$row['ArticlId'])?>" class="blog_item_thema"><?=$row['title']?></a>
                </div>
                <?
                if($i%2!=0){
                    ?>
                <div style="float: left;display: block;width: 280px;"></div>
                    <?
                }
            }
            ?><br style="clear: both;"/><a class="more_blog" style="text-align: right; margin-right: 20px;" href="/users/"><?if(!$experts) echo "Переглянути більше блогів";else echo "Переглянути всіх";?> <img src="/images/design/down.png" alt="" /></a>
            <?if(!$experts){?>
            <div class="WhantBlog">
            <span class="WhantBlogText">Бажаєте вести блог?<a class="news_colum1_1_footer_but" style="text-decoration: none;margin-top: -2px;margin-left: 14px;" href="/registration/">Приєднатись</a></span>
            </div>
            <?}?>
            </div>

            <?
         }
    }

    function showBestExperts(){
        $module=87;
        if(empty($this->CatalogLayout)) $this->CatalogLayout = Singleton::getInstance('Catalog');

        $q="SELECT
                `id_item`,
                count(id_item) as `count`,
                `".TblModUserBlog."`.*,
                `".TblModUser."`.*,
                `".TblSysUser."`.`group_id`,
                `".TblModUserBlog."`.`id` as `ArticlId`
                FROM `".TblSysModComments."`,`".TblModUserBlog."`,`".TblModUser."`,`".TblSysUser."`
                WHERE `id_module`='".$module."'
                    AND `".TblModUserBlog."`.`id`=`id_item`
                    AND `".TblModUser."`.`sys_user_id`=`".TblModUserBlog."`.`id_user`
                    AND `".TblSysUser."`.`id`=`".TblModUserBlog."`.`id_user`
                    AND `".TblSysUser."`.`group_id`=7
                GROUP BY id_item
                ORDER BY `count` DESC
                ";
            $res = $this->db->db_Query($q);
            $rows = $this->db->db_GetNumRows();
            if($rows>0){
            ?>
            <div id="expert_club">
                <div id="expert_club_title">Експертний клуб</div>
            <?
            for($i=0;$i<$rows;$i++){
                $row=$this->db->db_FetchAssoc();
                ?>
                <div class="expert_club_items">
                    <a href="<?=$this->link($row['sys_user_id'])?>">
                    <img src="<?=$this->CatalogLayout->ShowCurrentImageExSize("/images/mod_blog/".$row['sys_user_id']."/".$row['discount'], 41, 41, 'center', 'center', '85', NULL, NULL, NULL, true);?>" alt="" />
                    </a>
                    <div class="expert_club_item_name"><?=$row['name']." ".$row['country']?></div>
                    <div class="expert_club_item_thema"><?=$row['dttm']?></div>
                    <a href="<?=$this->link($row['id_user'],$row['ArticlId'])?>" class="blog_item_thema"><?=$row['title']?></a>
                </div>
                <?
                if($i%2!=0){
                    ?>
                <div style="float: left;display: block;width: 280px;"></div>
                    <?
                }
            }
            ?><br style="clear: both;"/><a class="more_blog" style="text-align: right; margin-right: 20px;" href="/experts/">Переглянути всіх<img src="/images/design/down.png" alt="" /></a>
            </div><?
            }
    }
    function countOfAllUsers($expert=NULL){
        $q="SELECT count(*) as count
            FROM `".TblModUser."`,`".TblSysUser."`
            WHERE ";
        $q.=" `".TblModUser."`.`sys_user_id`=`".TblSysUser."`.`id`";
        if($expert) $q.=" AND `".TblSysUser."`.`group_id`=7";
        else $q.=" AND `".TblSysUser."`.`group_id`=5";
        $q.=" ORDER BY `".TblModUser."`.`country` ASC";
        $res = $this->db->db_Query($q);
        $row=$this->db->db_FetchAssoc();
        return $row['count'];
    }
    function showAllUsers($expert=NULL){
         $rows_all=$this->countOfAllUsers($expert);
         $this->display=40;
        $q="SELECT `".TblModUser."`.*,`".TblSysUser."`.`login`,`".TblModUserBlog."`.`dttm`
            FROM `".TblModUserBlog."` INNER JOIN ( select max(id) as id from `".TblModUserBlog."` group by `id_user` ) dts ON dts.id=`".TblModUserBlog."`.id,
              `".TblModUser."`,
              `".TblSysUser."`
            WHERE `".TblModUserBlog."`.`id_user`=`".TblSysUser."`.`id`";
        $q.=" AND `".TblModUser."`.`sys_user_id`=`".TblSysUser."`.`id`";
        if(!empty($this->letter))
        $q.=" AND (`".TblModUser."`.`country` LIKE '".$this->letter."%' OR `".TblSysUser."`.`login` LIKE '".$this->letter."%')";
        if($expert) $q.=" AND `".TblSysUser."`.`group_id`=7";
        else $q.=" AND `".TblSysUser."`.`group_id`=5";
        $q.=" GROUP BY `".TblModUser."`.`sys_user_id`
                ORDER BY `".TblModUserBlog."`.`dttm` DESC";
        if(empty($this->letter)) $q = $q." LIMIT ".$this->start.", ".($this->display);
        $res = $this->db->db_Query($q);
        //echo '<br>$q='.$q;
        $rows = $this->db->db_GetNumRows();
        //echo $rows;

        if(isset($mas['discount'])) $userImage=$mas['discount'];
        ?>
         <div id="catalogBox">
            <span class="MainHeaderText" style="width: 100%"><?if(!$expert) echo "Користувачі порталу <a href='/experts/' style='float: right;margin-right: 90px;color:#0950A5'>Експерти</a>"; else echo "Експерти порталу <a href='/users/' style='float: right;margin-right: 90px;color:#0950A5'>Користувачі</a>";?></span>

            <div class="alphavitBox">
                <span class="alafaBoxes" style="margin-left: 5px;">
                    <?if($expert){?>
                    <a <?if(empty($this->letter))  echo "class='letterSelected'";?> style="margin-left: 5px;margin-right: 5px;" href="/experts/">Всі</a>
                    <?}else{?>
             <a <?if(empty($this->letter))  echo "class='letterSelected'";?> style="margin-left: 5px;margin-right: 5px;" href="/users/">Всі</a>
             <?}?>
             <a <?if($this->letter=="А") echo "class='letterSelected'";?> href="?letter=А">А</a>
             <a <?if($this->letter=="Б") echo "class='letterSelected'";?> href="?letter=Б">Б</a>
             <a <?if($this->letter=="В") echo "class='letterSelected'";?> href="?letter=В">В</a>
             <a <?if($this->letter=="Г") echo "class='letterSelected'";?> href="?letter=Г">Г</a>
             <a <?if($this->letter=="Д") echo "class='letterSelected'";?> href="?letter=Д">Д</a>
             <a <?if($this->letter=="Е") echo "class='letterSelected'";?> href="?letter=Е">Е</a>
             <a <?if($this->letter=="Ж") echo "class='letterSelected'";?> href="?letter=Ж">Ж</a>
             <a <?if($this->letter=="З") echo "class='letterSelected'";?> href="?letter=З">З</a>
             <a <?if($this->letter=="И") echo "class='letterSelected'";?> href="?letter=И">И</a>
             <a <?if($this->letter=="Й") echo "class='letterSelected'";?> href="?letter=Й">Й</a>
             <a <?if($this->letter=="К") echo "class='letterSelected'";?> href="?letter=К">К</a>
             <a <?if($this->letter=="Л") echo "class='letterSelected'";?> href="?letter=Л">Л</a>
             <a <?if($this->letter=="М") echo "class='letterSelected'";?> href="?letter=М">М</a>
             <a <?if($this->letter=="Н") echo "class='letterSelected'";?> href="?letter=Н">Н</a>
             <a <?if($this->letter=="О") echo "class='letterSelected'";?> href="?letter=О">О</a>
             <a <?if($this->letter=="П") echo "class='letterSelected'";?> href="?letter=П">П</a>
             <a <?if($this->letter=="Р") echo "class='letterSelected'";?> href="?letter=Р">Р</a>
             <a <?if($this->letter=="С") echo "class='letterSelected'";?> href="?letter=С">С</a>
             <a <?if($this->letter=="Т") echo "class='letterSelected'";?> href="?letter=Т">Т</a>
             <a <?if($this->letter=="У") echo "class='letterSelected'";?> href="?letter=У">У</a>
             <a <?if($this->letter=="Ф") echo "class='letterSelected'";?> href="?letter=Ф">Ф</a>
             <a <?if($this->letter=="Х") echo "class='letterSelected'";?> href="?letter=Х">Х</a>
             <a <?if($this->letter=="Ц") echo "class='letterSelected'";?> href="?letter=Ц">Ц</a>
             <a <?if($this->letter=="Ч") echo "class='letterSelected'";?> href="?letter=Ч">Ч</a>
             <a <?if($this->letter=="Ш") echo "class='letterSelected'";?> href="?letter=Ш">Ш</a>
             <a <?if($this->letter=="Щ") echo "class='letterSelected'";?> href="?letter=Щ">Щ</a>
             <a <?if($this->letter=="Э") echo "class='letterSelected'";?> href="?letter=Э">Э</a>
             <a <?if($this->letter=="Ю") echo "class='letterSelected'";?> href="?letter=Ю">Ю</a>
             <a <?if($this->letter=="Я") echo "class='letterSelected'";?> href="?letter=Я">Я</a>
                </span><span class="alafaBoxes">
             <a <?if($this->letter=="A") echo "class='letterSelected'";?> href="?letter=A">A</a>
             <a <?if($this->letter=="B") echo "class='letterSelected'";?> href="?letter=B">B</a>
             <a <?if($this->letter=="C") echo "class='letterSelected'";?> href="?letter=C">C</a>
             <a <?if($this->letter=="D") echo "class='letterSelected'";?> href="?letter=D">D</a>
             <a <?if($this->letter=="E") echo "class='letterSelected'";?> href="?letter=E">E</a>
             <a <?if($this->letter=="F") echo "class='letterSelected'";?> href="?letter=F">F</a>
             <a <?if($this->letter=="G") echo "class='letterSelected'";?> href="?letter=G">G</a>
             <a <?if($this->letter=="H") echo "class='letterSelected'";?> href="?letter=H">H</a>
             <a <?if($this->letter=="I") echo "class='letterSelected'";?> href="?letter=I">I</a>
             <a <?if($this->letter=="J") echo "class='letterSelected'";?> href="?letter=J">J</a>
             <a <?if($this->letter=="K") echo "class='letterSelected'";?> href="?letter=K">K</a>
             <a <?if($this->letter=="L") echo "class='letterSelected'";?> href="?letter=L">L</a>
             <a <?if($this->letter=="M") echo "class='letterSelected'";?> href="?letter=M">M</a>
             <a <?if($this->letter=="N") echo "class='letterSelected'";?> href="?letter=N">N</a>
             <a <?if($this->letter=="O") echo "class='letterSelected'";?> href="?letter=O">O</a>
             <a <?if($this->letter=="P") echo "class='letterSelected'";?> href="?letter=P">P</a>
             <a <?if($this->letter=="Q") echo "class='letterSelected'";?> href="?letter=Q">Q</a>
             <a <?if($this->letter=="R") echo "class='letterSelected'";?> href="?letter=R">R</a>
             <a <?if($this->letter=="S") echo "class='letterSelected'";?> href="?letter=S">S</a>
             <a <?if($this->letter=="T") echo "class='letterSelected'";?> href="?letter=T">T</a>
             <a <?if($this->letter=="U") echo "class='letterSelected'";?> href="?letter=U">U</a>
             <a <?if($this->letter=="V") echo "class='letterSelected'";?> href="?letter=V">V</a>
             <a <?if($this->letter=="W") echo "class='letterSelected'";?> href="?letter=W">W</a>
             <a <?if($this->letter=="X") echo "class='letterSelected'";?> href="?letter=X">X</a>
             <a <?if($this->letter=="Y") echo "class='letterSelected'";?> href="?letter=Y">Y</a>
             <a <?if($this->letter=="Z") echo "class='letterSelected'";?> href="?letter=Z">Z</a>
                </span>
         </div>

            <div id="catalogBody" style="padding-left: 0px;width: 671px;padding-right: 15px;border-left: 1px solid #AFAFAF;
    border-right: 1px solid #AFAFAF;border-bottom: 1px solid #AFAFAF;">
                <div class="letterUser letterUserie">
                    <span class="letterUser" style="margin-top: -31px;margin-left: -15px;"><?=$this->letter?></span>
                </div>
                <div class="usersBox">

                <?$cheker=0;
                if($rows==0) echo "<h1 style='text-align: center;margin-top: 200px;margin-bottom: 200px;'>Вибачте. На літеру '".$this->letter."' немає жодного користувача!</h1> ";
                for($i=0;$i<$rows;$i++){
                $row=$this->db->db_FetchAssoc();
                 $flag=false;
                if(!empty($this->letter)){
                $familia=ucfirst($row['country']);

                 if(strlen($this->letter)==2){
                     if($familia[1]!=$this->letter[1]) $flag=true;
                 }else if($familia[0]!=$this->letter) $flag=true;
                }
                if(isset($row['country']) && !empty($row['country']) && $flag && !empty($this->letter)){
                    $cheker++;
                    if($cheker==$rows && $rows!=1) echo "<h1 style='text-align: center;margin-top: 200px;margin-bottom: 200px;'>Вибачте. На літеру '".$this->letter."' немає жодного користувача!</h1> ";
                    if($rows==1){
                        echo "<h1 style='text-align: center;margin-top: 200px;margin-bottom: 200px;'>Вибачте. На літеру '".$this->letter."' немає жодного користувача!</h1> ";
                        continue;
                    }
                    else
                    continue;
                }
                    ?>

                <div class="userBoxSingleItem">
                    <a class="" style="text-decoration: none;font-size: 12px;" href="<?=$this->link($row['sys_user_id'])?>">
                        <?if(is_file($_SERVER['DOCUMENT_ROOT']."/images/mod_blog/".$row['sys_user_id']."/".$row['discount'])){?>
                        <img class="avatarImage profileAvatar" src="<?=$this->Catalog->ShowCurrentImageExSize("/images/mod_blog/".$row['sys_user_id']."/".$row['discount'], 70, 70, 'center', 'center', '85', NULL, NULL, NULL, true);?>" alt="" />
                        <?}else{?>
                        <img class="avatarImage profileAvatar" width="70" height="70" src="/images/design/noAvatar.gif"/>
                        <?}?>
                    </a>
                    <div class="userProfileLink">
                        <?if($row['name']=="" || $row['country']==""){ ?>
                          <a class="" style="text-decoration: none;font-size: 12px;" href="<?=$this->link($row['sys_user_id'])?>"><?=$row['login']?></a>

                        <?}else{?>
                          <a class="" style="text-decoration: none;font-size: 12px;" href="<?=$this->link($row['sys_user_id'])?>"><?=$row['name']."<br/>".$row['country']?></a>
                        <?}
                        ?>

                    </div>
                </div>

                    <?
                }?>
                </div>
                <div class="page-navi-class" style="padding-top: 15px;height: 40px;"><?$this->Form->WriteLinkPagesStatic( "/users/", $rows_all, $this->display, $this->start, $this->sort, $this->page);?></div>
            </div>
        </div>


         <?
    }

    function link($userId,$IdOfrecord=NULL){
        if($IdOfrecord==NULL){
            return "/blog/".$userId."/";
        }else{
            return "/blog/".$userId."/entry/".$IdOfrecord."/";
        }
    }

    function showExpertsUsersInHeaderRandom(){

        $q="SELECT * FROM  `".TblModUser."`,`".TblSysUser."`,`".TblModUserBlog."`
                WHERE `".TblModUser."`.`sys_user_id`=`".TblSysUser."`.`id`
                    AND  `".TblModUser."`.`ShowInTop`='1'
                    AND `".TblModUser."`.`sys_user_id`=`".TblSysUser."`.`id`
                    AND `".TblModUserBlog."`.`id_user`=`".TblSysUser."`.`id`
                        GROUP BY `".TblModUser."`.`sys_user_id`";

        $res = $this->db->db_Query($q);
        $rowsUser=$this->db->db_GetNumRows();
        $genUserArr=array();
        if($rowsUser>0){
        for ($i = 0; $i < $rowsUser; $i++) {
            $rowUser=$this->db->db_FetchAssoc();
            $genUserArr[$i]=$rowUser;
        }
        $generatedRow=mt_rand(0,$rowsUser-1);
        if($rowsUser>0){
        $q="SELECT `".TblModUser."`.`name`,
                   `".TblModUserBlog."`.`id` AS `RecordId`,
                   `".TblModUser."`.`country`,
                   `".TblModUser."`.`expertImgHeader`,
                   `".TblModUser."`.`sys_user_id`,
                   `".TblModUserBlog."`.`title`,
                   `".TblModUser."`.`expertTitle`
            FROM  `".TblModUser."`,`".TblSysUser."`,`".TblModUserBlog."`
                WHERE `".TblModUser."`.`sys_user_id`=`".TblSysUser."`.`id`
                    AND  `".TblSysUser."`.`id`=".$genUserArr[$generatedRow]['sys_user_id']."
                    AND `".TblModUser."`.`sys_user_id`=`".TblSysUser."`.`id`
                    AND `".TblModUserBlog."`.`id_user`=`".TblSysUser."`.`id`
                        ORDER BY `".TblModUserBlog."`.`dttm` DESC
                   ";

        $res = $this->db->db_Query($q);
        $rowArt=$this->db->db_FetchAssoc();
        $rows=$this->db->db_GetNumRows();

        //echo $generatedRow;
        if($rows>0){
            if(!isset($this->Crypt))
            $this->Crypt=new Crypt();
        ?>
        <div id="header_part2">
                    <div id="headerUserExpertFoto">
                        <?if(isset ($rowArt['expertImgHeader']) && !empty($rowArt['expertImgHeader'])){
                            ?>
                        <a class="expertTopHeaderLinkImg" href="<?=$this->link($rowArt['sys_user_id'])?>">
                            <img width="166" height="150" onmouseover="$(this).attr('src','<?="/images/mod_blog/".$rowArt['sys_user_id']."/".$rowArt['expertImgHeader']?>')" onmouseout="$(this).attr('src','<?=$this->makeImageGrey($rowArt['expertImgHeader'], "/images/mod_blog/".$rowArt['sys_user_id']."/")?>')" src="<?=$this->makeImageGrey($rowArt['expertImgHeader'], "/images/mod_blog/".$rowArt['sys_user_id']."/")?>"/>
                        </a>
                        <?}?>
                    </div>
                      <div id="say">
                        <div id="say1">
                            <?=$rowArt['name']." ".$rowArt['country'].":"?><br/>
                            <span style="font-size: 10px; color:#0f7cc4;height: 11px;display: block;width: 100%"><?=$rowArt['expertTitle']?></span>
                            <span style="font-weight: normal;font-style: normal;margin-top: 4px;display: block;width:160px;height: 28px; font-size: 11px;">
                        <?=$this->Crypt->TruncateStr(strip_tags(stripslashes($rowArt['title'])),90);?>
                            </span>
                            <a class="expertTopHeaderLink" href="<?=$this->link($rowArt['sys_user_id'], $rowArt['RecordId'])?>">Обговорення</a>
                            </div>
                </div></div>

        <?
        }
        }
        }
    }

    function deleteBlog(){
        $q="SELECT `id_user` FROM `".TblModUserBlog."` WHERE `id`=".$this->idOfDelete."";
        $res = $this->db->db_Query($q);
        $row=$this->db->db_FetchAssoc();
        $rows=$this->db->db_GetNumRows();
        $userFromTqable=$row['id_user'];
        if($this->userId==$this->Logon->user_id && $userFromTqable==$this->Logon->user_id && $rows>0){
         $q="DELETE FROM `".TblModUserBlog."`
            WHERE `id`=".$this->idOfDelete."
            ";
         $res = $this->db->db_Query($q);
         $row=$this->db->db_FetchAssoc();
         $rows=$this->db->db_GetNumRows();
         if($res){
             ?><h1 style="color: green;text-align: center;margin-top: 50px;">Ваш запис успішно видалений</h1><?
         }else{
             ?><h1 style="color: #ff0000;text-align: center;margin-top: 50px;">Під час видалення запису виникла помилка. Якщо помилка повториться зверніться до Адміністрації.</h1><?
         }
        }
        ?><script type="text/javascript">
            timer=setTimeout(function(){
                location.href="<?=$this->link($this->userId);?>";
            },5000);
                    </script><?
    }


    function makeImageGrey($filenameFull,$path){
    //Получаем размеры изображения
        $linkPath=$path;
        $path=SITE_PATH.$path;
       $ext=$this->getExtension($filenameFull);
       $nameFullLen=strlen($filenameFull);
       $extLen=strlen($ext);
       $filename=substr($filenameFull,0,$nameFullLen-$extLen-1);
       $filenameFullPath=$path.$filenameFull;
       if(!is_file($path.$filename."_grey.".$ext)){
          $img_size = GetImageSize($filenameFullPath);
          $width = $img_size[0];
          $height = $img_size[1];
          //Создаем новое изображение с такмими же размерами
          $img = imageCreate($width,$height);
          //Задаем новому изображению палитру "оттенки серого" (grayscale)
          for ($c = 0; $c < 256; $c++) {
            ImageColorAllocate($img, $c,$c,$c);
          }
          //Содаем изображение из файла Jpeg
          $img2 = ImageCreateFromJpeg($filenameFullPath);
          //Объединяем два изображения
          ImageCopyMerge($img,$img2,0,0,0,0, $width, $height, 100);
          //Сохраняем полученное изображение
          imagejpeg($img, $path.$filename."_grey.".$ext);
         //Освобождаем память, занятую изображением
          imagedestroy($img);

       }
       return $linkPath.$filename."_grey.".$ext;
}
}
?>