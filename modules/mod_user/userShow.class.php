<?php
// ================================================================================================
// System : CMS
// Module : userShow.class.php
// Date : 22.02.2011
// Licensed To: Yaroslav Gyryn
// Purpose : Class definition For display interface of External users
// ================================================================================================

include_once(SITE_PATH . '/modules/mod_user/user.defines.php');

/**
 * Class User
 * Class definition for all Pages - user actions
 * @author Igor Trokhymchuk  <ihor@seotm.com>
 * @version 1.1, 22.02.2011
 * @property ShareLayout $Share
 * @property FrontendPages $FrontendPages
 * @property UserAuthorize $Logon
 * @property UserShow $UserShow
 * @property OrderLayout $Order
 * @property FrontSpr $Spr
 * @property FrontForm $Form
 * @property db $db
 * @property TblFrontMulti $multi
 * @property CatalogLayout $Catalog
 */
class UserShow extends User
{
    var $db = NULL;
    var $Msg = NULL;
    var $logon = NULL;
    var $Spr = NULL;
    var $Form = NULL;

    var $whattodo = NULL;
    var $referer_page = NULL;
    var $TextMessages = NULL;

    var $inputData = array(
            'name'=>array(
                    'presence' =>1,
                    'type'=>'text',
                    'name'=>'ФИО'
            ),
            'phone_mob'=>array(
                    'presence' =>1,
                    'type' => 'text',
                    'name' => 'Телефон'
            ),
            'email'=>array(
                    'presence' =>0,
                    'type' => 'text',
                    'name' => 'Email'
            ),
            'password'=>array(
                    'presence' =>1,
                    'type' => 'text',
                    'name' => 'Пароль'
            ),
            'password2'=>array(
                    'presence' =>1,
                    'type' => 'text',
                    'name' => 'Повторите пароль'
            )
    );

    var $updateData =  array(
                    'name'=>array(
                        'presence' =>1,
                        'type'=>'text',
                        'name'=>'ФИО'
                    ),
                    'email'=>array(
                        'presence' =>1,
                        'type' => 'text',
                        'name' => 'Email'
                    )
    );

    // ================================================================================================
    //    Function          : UserShow (Constructor)
    //    Date              : 22.02.2011
    //    Parms             : session_id / id of the ssesion
    //                          user_id    / User ID
    //    Returns           : Error Indicator
    //    Description       : Init variables
    // ================================================================================================
    function UserShow($session_id = NULL, $user_id = NULL)
    {
        ($session_id != "" ? $this->session_id = $session_id : $this->session_id = NULL);
        ($user_id != "" ? $this->user_id = $user_id : $this->user_id = NULL);

        if (empty($this->db)) $this->db = DBs::getInstance();
        if (empty($this->Msg)) $this->Msg = check_init('ShowMsg', 'ShowMsg');
        if (empty($this->Logon)) $this->Logon = check_init('UserAuthorize', 'UserAuthorize');
        if (empty($this->Spr)) $this->Spr = check_init('FrontSpr', 'FrontSpr');
        if (empty($this->Form)) $this->Form = check_init('FrontForm', 'FrontForm');
        $this->multiUser = check_init_txt('TblFrontMulti', TblFrontMulti); //$this->Msg->GetMultiTxtInArr(TblModUserSprTxt);
        if (empty($this->Catalog)) $this->Catalog = Singleton::getInstance('Catalog');

    } // End of UserShow Constructor


    // ================================================================================================
    // Function : LoginPage
    // Date : 22.02.2011
    // Returns : true,false / Void
    // Description : Show form for logon of the user on the front-end
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function LoginPage()
    {
    ?>
        <style>
            .formError{ left: 135px!important; }
        </style>
        <?
        if (!$this->Logon->user_id) {
            if (empty($this->whattodo)) $this->whattodo = 2;
            $this->Form->WriteFrontHeader('Login', _LINK . 'login.html', NULL, NULL);
            //echo '<br>$this->referer_page='.$this->referer_page;
            if (!isset($this->referer_page) OR empty($this->referer_page)) {
                if (isset($_SERVER['HTTP_REFERER'])) {
                    $this->referer_page = str_replace('&', 'AND', $_SERVER['REQUEST_URI']);
                    $title = $this->multiUser['TXT_FRONT_PLEASE_LOGIN'];
                } else {
                    $this->referer_page = '/login.php?task=makelogon';
                    $title = $this->multiUser['TXT_TITLE_LOGIN_PAGE'];
                }

            } else {
                $title = $this->multiUser['TXT_TITLE_LOGIN_PAGE'];
            }
            $this->Form->Hidden('referer_page', "/myaccount/");
            $this->Form->Hidden('whattodo', $this->whattodo);

            ?>

            <div id="catalogBox">
                <span class="enter-title">Вход</span>

                <div id="catalogBody">
                    <? if (!empty($this->Err) || !empty($this->TextMessages)) { ?>
                        <div class="err" style="margin-top: 25px;">
                            <?
                            $this->showErr();
                            $this->ShowTextMessages();
                            ?>

                        </div>
                    <? } ?>

                    <table border="0" cellspacing="0" cellpadding="0" class="tblRegister" style="margin-top: 20px;margin-bottom: 20px;">
                        <tr align="right">
                            <td colspan="2" align="left"><span>Номер телефона:</span><br/><?= $this->Form->TextBox('login', $this->login, "id='logPhoneMob' class='validate[required, funcCall[check_phone_length] ]'"); ?></td>
                        </tr>
<!--                        <tr align="right">-->
<!--                            <td colspan="2" align="left"><span>Email</span><br/>--><?//= $this->Form->TextBox('login', $this->login, "class='validate[required]'"); ?><!--</td>-->
<!--                        </tr>-->
                        <tr align="right">
                            <td colspan="2" align="left"><span>Пароль</span><br/><?= $this->Form->Password('pass', '', 20,"class='validate[required]'"); ?></td>
                        </tr>
                        <tr>
                            <td><a href="<?= _LINK; ?>forgotpass.html"
                                   class="a02">Вспомнить пароль</a>
                            </td>
                            <td>
                                <input  type="submit" value="Вход"/>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2"></td>
                        </tr>
                    </table>

                    <script>
                        make_phone_mask('logPhoneMob');
                    </script>


                </div>
            </div>

            <?

            $this->Form->WriteFrontFooter();
        } else {
            echo "<script type='text/javascript'>location.href='/'</script>";
        }

    } //end of function LoginPage()


    // ================================================================================================
    // Function : LoginPageOrder
    // Date : 22.02.2011
    // Returns : true,false / Void
    // Description : Show form for logon of the user on the front-end
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function LoginPageOrder($referer_page)
    {
        if (!empty($referer_page)) $this->referer_page = $referer_page;
        if (empty($this->whattodo)) $this->whattodo = 2;
        ?>
        <h1><?= $this->multiUser['TXT_AUTHORIZATION']; ?></h1>
        <div class="body">
            <div class="orderFirstStepTxt">
                <?= $this->multiUser['TXT_SECOND_STEP']; ?>
            </div>

            <div class="rightHeader">
                <div class="orderStep">
                    <?= $this->multiUser['TXT_STEP_2']; ?>
                </div>
                <div class="orderStepImage">
                    <img src="/images/design/step2.gif">
                </div>
            </div>

            <?
            $this->Form->WriteFrontHeader('Login', _LINK . 'login.html', NULL, NULL);
            //echo '<br>$this->referer_page='.$this->referer_page;
            if (!isset($this->referer_page) OR empty($this->referer_page)) {
                if (isset($_SERVER['HTTP_REFERER']))
                    $this->referer_page = str_replace('&', 'AND', $_SERVER['REQUEST_URI']);
                else
                    $this->referer_page = '/login.php?task=makelogon';
            }
            //echo '<br>$this->referer_page='.$this->referer_page;
            $this->Form->Hidden('referer_page', $this->referer_page);
            $this->Form->Hidden('whattodo', $this->whattodo);

            if (!$this->Logon->user_id) {
                echo $this->showErr();
                echo $this->ShowTextMessages();

                ?>
                <div class="orderHelpText">
                    <?= $this->multiUser['TXT_HELP_NEW_USER']; ?>
                </div>

                <div class="registerLinks">
                    <a href="<?= _LINK; ?>registration/"
                       class="registerLink"><?= $this->multiUser['IMG_FRONT_SIGN_UP']; ?></a>
                </div>

                <div class="orderHelpText">
                    <?= $this->multiUser['TXT_FRONT_RETURNING_USER_DESCRIPTION']; ?>
                </div>

                <div id="content2Box">
                    <div class="subBody" align="left" style="padding-top:15px;">
                        <table border="0" cellspacing="2" cellpadding="0" class="regTable" width="100%">

                            <tr>
                                <td width="200">
                                    <?= $this->multiUser['FLD_LOGIN']; ?>
                                    &nbsp;
                                    <?= $this->Form->TextBox('login', $this->login, 'size="10"'); ?>
                                </td>
                                <td width="170">
                                    <?= $this->multiUser['FLD_PASSWORD']; ?>
                                    <?= $this->Form->Password('pass', '', 10); ?>
                                </td>
                                <td>
                                    <? $btnSubmit = $this->multiUser['BTN_SUBMIT']; ?>
                                    <input type="image" src="/images/design/submit.png" alt="<?= $btnSubmit; ?>"
                                           title="<?= $btnSubmit; ?>"/>
                                </td>
                            </tr>
                        </table>
                        <div style="float:right; margin: 0px 20px 10px 0px;"><a href="<?= _LINK; ?>forgotpass.html"
                                                                                class="registerLink"><?= $this->multiUser['TXT_FORGOT_PASS']; ?></a>
                        </div>

                    </div>
                </div>
            <?
            }
            /* else{
                  $title = 'Зайти в мой профайл';
             ?>
                  <div class="categoryTxt"><?=$title;?></div>
             </div>
             <div id="content2Box">
                 <div class="subBody">
                      Для Вашего компьютера уже создана сессия с логином <?=$this->Logon->login;?>. Вы можете <a href="<?=_LINK;?>myaccount/" title="перейти в профайл">перейти в свой профайл</a> или <a href="<?=_LINK;?>logout.html" title="завершить сеанс">завершить сеанс</a>.
                  </div>
             </div>
             <?
                }*/
            ?>

            <div class="orderHelpInfo" align="left">
                <?= $this->multiUser['TXT_HELP_INFO']; ?>:
                <div class="orderHelpText">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= $this->multiUser['TXT_HELP_FORGET_PSW']; ?>
                </div>

                <div class="orderHelpText">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= $this->multiUser['TXT_HELP_SECURITY']; ?>
                </div>
            </div>
            <? $this->Form->WriteFrontFooter(); ?>
        </div>
    <?
    } //end of function LoginPageOrder()


    // ================================================================================================
    // Function : ShowRegForm
    // Date : 22.02.2011
    // Parms : $new_stat_id - id of the new created records of user stat.
    // Returns : true,false / Void
    // Description : Show the second step of regidstration. This is the personal and contact information.
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function checkAjaxFields()
    {
        if (empty ($this->val)) {
            echo 3;
            return false;
        }
        switch ($this->wichField) {
            case "login":

                $q = "SELECT `login` FROM sys_user WHERE `login`='" . $this->val . "'";
                $this->db->db_Query($q);
                if ($this->db->db_GetNumRows() > 0) echo 1; else echo 0;
                break;
            case "email":
                $q = "SELECT `email` FROM mod_user WHERE `email`='" . $this->val . "'";
                $this->db->db_Query($q);
                if ($this->db->db_GetNumRows() > 0) echo 1; else echo 0;
                break;

            default:
                break;
        }
    }


    function ShowRegForm()
    {
        ?>
        <style>
            .formError{
                left: 150px!important;
            }
        </style>
        <div id="catalogBox">
            <span class="enter-title">Регистрация на сайте</span>

            <div id="CatformAjaxLoader"></div>
            <div id="catalogBody">

                <div class="registerBoxDiv reg-popup">

                    <div align="center"><? $this->showErr(); ?></div>
                    <?
                    $this->Form->WriteFrontHeader(NULL, "#", 'save_reg_data');
                    //$this->Form->Hidden( 'save_reg_data', 'save_reg_data' );
                    $this->Form->Hidden('subscr', $this->subscr);
                    $this->Form->Hidden('referer_page', $this->referer_page);
                    ?>

                    <ul class="CatFormUl">
                        <li>ФИО<span class="redStar"> *</span><br/>
                            <input id="nameOfPred" type="text" class="validate[required]" name="name" value="<?=$this->name?>"/>
                        </li>

                        <li>
                            Email<br/>
                            <input id="nameOfPred" type="text" class="validate[custom[email]]" name="email" value="<?=$this->email?>" />
                            <div id="resultofChek2"></div>
                        </li>

                        <li>Телефон<span class="redStar"> *</span><br/>
                            <input id="regPhoneMob" type="text" class="validate[required, funcCall[check_phone_length] ]" name="phone_mob" value="<?=$this->phone?>"/>
                        </li>

                        <li>Пароль<span class="redStar"> *</span><br/>
                            <input id="nameOfPred" type="password" class="validate[required]" name="password" value=""/>
                        </li>

                        <li>Повторите пароль<span class="redStar"> *</span><br/>
                            <input id="nameOfPred" type="password"  class="validate[required]" name="password2" value=""/>
                        </li>
                    </ul>
                    <input type="hidden" name="user_status" value="3"/>

                    <input type="submit" style="float: right" class="btnCatalogImgUpload" name="save_reg_data" onclick="SaveForm(); return false;" class="submitBtn<?= _LANG_ID ?>" value="Регистрация"/>
                    <? $this->Form->WriteFrontFooter(); ?>
                </div>
            </div>
        </div>

    <?

    } //end of function ShowRegForm()

    function showRegJS()
    {
        ?>

        <script language="JavaScript">

            $('#f1').validationEngine();
            make_phone_mask('regPhoneMob');

            function SaveForm() {
                $.ajax({
                    type: "POST",
                    data: $("#f1").serialize(),
                    url: "<?=_LINK;?>registration/result.html",
                    beforeSend: function () {


                    },
                    success: function (html) {

                        var data = JSON.parse(html);

                        //console.log(html);
                        if(data.error.length > 0){
                            $('#CatformAjaxLoader').html('<div class="error-box">Поля обьязательны для заполнени</div><br>'+data.error);
                        }else{
                            window.location.href= 'http://'+location.host+"/catalog/";
                        }


                    }
                });
            }




        </script>
    <?
    }
    // ================================================================================================
    // Function : ShowRegFinish
    // Date : 22.02.2001
    // Returns : true,false / Void
    // Description : Show finish of registraion
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function ShowRegFinish($res = NULL)
    {
        ?>
        <div><?
        if ($res) $this->ShowTextMessages($this->Msg->show_text('MSG_PROFILE_SENT_OK'));
        else $this->ShowTextMessages($this->Msg->show_text('MSG_PROFILE_NOT_SENT'));
        ?></div><?
    } //end of function ShowRegFinish()


    // ================================================================================================
    // Function : CheckFields()
    // Date : 22.02.2011
    // Parms :        $id - id of the record in the table
    // Returns :      true,false / Void
    // Description :  Checking all fields for filling and validation
    // Programmer :  Yaroslav Gyryn
    // ================================================================================================
    function CheckFields( $id = NULL )
    {
        $this->Err = NULL;
        //echo '$this->email ='.$this->email;
//        $q = "SELECT `login` FROM sys_user WHERE `login`='" . $this->email . "' OR `email` = '" . $this->email . "'";
//        $this->db->db_Query($q);
//        if ($this->db->db_GetNumRows() > 0) $this->Err .= "Пользователь с таким email-ом уже зарегистрирован";

        $q = "SELECT `login` FROM sys_user WHERE `login`='" . $this->phone_mob . "'";
        $this->db->db_Query($q);
        if ($this->db->db_GetNumRows() > 0) $this->Err .= "Пользователь с таким номером телефона уже зарегистрирован<br/>";
        else {
            $q = "SELECT `id` FROM mod_user WHERE `phone_mob`='" . $this->phone_mob . "'";
            $this->db->db_Query($q);
            if ($this->db->db_GetNumRows() > 0) $this->Err .= "Пользователь с таким номером телефона уже зарегистрирован<br/>";
        }
        //echo '<br>$this->Err='.$this->Err.' $this->Msg->table='.$this->Msg->table;
        return $this->Err;
    } //end of function CheckFields()


    function CheckPsw() {
        $this->errorMsg = '';

        if( isset($this->password)
            && isset($this->password2)
            && !empty($this->password)
            && !empty($this->password2)
            && $this->password === $this->password2
        ){

            return true;

        }else{

            $this->errorMsg = "Пароли должны совпадать";

        }



        return true;
    }

    function CheckInputData($arrName){
            $this->errorMsg = '';


           // print_r($this->inputData);

            foreach($this->$arrName as $k=>$v) {

                if( $v['presence']==1 ){

                    if( !isset($this->$k) || empty($this->$k) ){
                        $this->errorMsg .= $v['name'].'<br>';
                    }

                }


            }

        return true;
    }

    // ================================================================================================
    // Function : EditProfile
    // Date : 22.02.2001
    // Returns : true,false / Void
    // Description : Show the form for editig data of profile of the user on front-end.
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function Show_JS()
    {
        ?>

        <script language="JavaScript">
            var unikEmail = false;


            function chekFields(wichFiled, val, resultBox) {
                $.ajax({
                    type: "GET",
                    url: "<?=_LINK;?>checkReg?wichField=" + wichFiled + "&val=" + val,
                    beforeSend: function () {
                        $("#" + resultBox).html("");
                        $("#" + resultBox).css("background", "url('/images/design/ajax-loader.gif') no-repeat");
                    },
                    success: function (html) {
                        result = parseInt(html);
                        $("#" + resultBox).css("background", "none");
                        if (result == 1) {
                            if (wichFiled == "login") $("#" + resultBox).html("<span class='redStar'>Такий нікнейм вже існує!</span>");
                            else $("#" + resultBox).html("<span class='redStar'>Такий E-mail вже зареэстрований!</span>");
                        }
                        if (result == 3) {
                            $("#" + resultBox).html("<span class='redStar'>Це поле потрібно заповнити!</span>");
                        }
                        if (result == 0) {
                            $("#" + resultBox).html("");
                            if (wichFiled == "login") unikLogin = true;
                            if (wichFiled == "email") unikEmail = true;
                        }
                    }
                });
            }
            function emailCheck(emailStr) {
                if (emailStr == "") return true;
                var emailPat = /^(.+)@(.+)$/;
                var matchArray = emailStr.match(emailPat);
                if (matchArray == null) {
                    return false;
                }
                return true;
            }

            function makeRequest(url_use, param, msq_id){
                elsement=document.getElementById(msq_id);
                $.ajax({
                    type: "POST",
                    data: param,
                    url: url_use,

                    success:function(msg){
                        elsement.innerHTML=msg;
                        //elsement.style.display='block';
                    }
                });
            }

            function SaveForm() {
                $.ajax({
                    type: "POST",
                    data: $("#profile").serialize(),
                    url: "<?=_LINK;?>myaccount/update/",
                    beforeSend: function () {
                        $("#CatformAjaxLoader").width($("#catalogBody").width()).height($("#catalogBody").height() + 20).fadeTo("fast", 0.4);
                        //$(Did).show("fast");

                    },
                    success: function (html) {

                        var data = JSON.parse(html);

                        //console.log(html);

                        if(data.error.length > 0){
                            $('#CatformAjaxLoader').html('<div class="error-box">Поля обьязательны для заполнени</div>'+data.error);
                        }else{
                            location.reload();
                        }


                    }
                });
            }


        </script>
    <?
    }

    function EditProfile()
    {
        $SysGroup = new SysUser();

        $q = "SELECT * FROM `" . TblModUser . "`,`" . TblSysUser . "` WHERE `" . TblModUser . "`.`sys_user_id`=" . $this->Logon->user_id . " AND `" . TblSysUser . "`.id=" . $this->Logon->user_id . "";
        $res = $this->db->db_Query($q);

        if (!$res OR !$this->db->result) return false;
        $mas = $this->db->db_FetchAssoc();

        ?>

        <div class="h1main"><div class="line2"></div><span>Личный кабинет</span></div>
        <div class="user-profile">
            <div id="catalogBox">
                <div class="top-tabs">
                    <div class="btn-categ active-t">
                        <div class="wrapper-tab">
                            <div class="tab-item " data-item="0">
                                Информация пользователя               </div>
                        </div>
                    </div>
                    <div class="btn-categ">
                        <div class="wrapper-tab">
                            <div class="tab-item" data-item="1">История покупок</div>
                        </div>
                    </div>
                </div>
                <div class="categ-prop active-prod" id="p0">

                <div id="catalogBody">
                    <div id="CatformAjaxLoader"></div>
                    <div class="registerBoxDiv">
                        <?
                        $this->Form->WriteFrontHeader('profile', '#', 'update');
                        //$this->Form->Hidden( 'user_id', $mas['sys_user_id'] );
                        $this->Form->Hidden('user_status', $mas['user_status']);
                        //$this->Form->Hidden('email', $mas['email']);
                        $this->Form->Hidden('phone_mob', $mas['phone_mob']);
                        ?>

                        <ul class="user-data">
                            <li>Email<br/>
                                <input id="nameOfPred" type="text" class="CatinputFromForm" name="email"
                                       value="<?= $mas['email'] ?>" />
                            </li>
                            <li>ФИО<br/>
                                <input id="nameOfPred" type="text" class="CatinputFromForm" name="name"
                                       value="<?= $mas['name'] ?>"/>
                            </li>
                            <li>Телефон<br/>
                                <input id="phoneMob" type="text" class="CatinputFromForm" name="phone_mob"
                                       value="<?= $mas['phone_mob'] ?>" disabled/>
                            </li>
                            <li>
                                <input type="button" style="float: right" class="btnCatalogImgUpload" onclick="SaveForm()" name="save_reg_data" class="submitBtn<?= _LANG_ID ?>" value="Редактировать→"/>
                            </li>
                        </ul>

                        <? $this->Form->WriteFrontFooter(); ?>
                        <?$this->ShowChangeEmailPass($this->login);?>
                        <div class="text-data-profile"><?=$this->multiUser['_TXT_USER_DATA']?></div>
                    </div>
                </div>

                </div>
                <div class="categ-prop" id="p1">
                    <?$my = new OrderLayout($this->user_id);?>
                   <?$my->UserOrderHistory($this->user_id, false)?>
                </div>
            </div>
        </div>
    <?
    } //end of function EditProfile()


    // ================================================================================================
    // Function : ShowCommentsBlock
    // Date : 22.02.2001
    // Returns : true,false / Void
    // Description : Show the form for editig data of profile of the user on front-end.
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function ShowCommentsBlock()
    {
        $SysGroup = new SysUser();
        // echo 'this->login = '.$this->login ;
        //echo '<br/>this->Logon->login = '.$this->Logon->login ;
        $q = "SELECT * FROM `" . TblModUser . "`,`" . TblSysUser . "` WHERE `" . TblModUser . "`.`sys_user_id`=" . $this->Logon->user_id . " AND `" . TblSysUser . "`.id=" . $this->Logon->user_id . "";
        $res = $this->db->db_Query($q);
        //echo '<br>'.$q.' $res='.$res.' $this->db->result='.$this->db->result;
        if (!$res OR !$this->db->result) return false;
        $mas = $this->db->db_FetchAssoc();

        ?>
        <div>
            <div id="catalogBox">
                <span class="MainHeaderText">Редагування профілю</span>

                <div id="profileMenuHandler">
                    <div id="leftProfileMenuPart">
                        <? if (is_file($_SERVER['DOCUMENT_ROOT'] . "/images/mod_blog/" . $this->Logon->user_id . "/" . $mas['discount'])) { ?>
                            <br/>
                            <img class="avatarImage profileAvatar"
                                 src="<?= $this->Catalog->ShowCurrentImageExSize("/images/mod_blog/" . $this->Logon->user_id . "/" . $mas['discount'], 70, 70, 'center', 'center', '85', NULL, NULL, NULL, true); ?>"
                                 alt=""/>
                        <? } else { ?>
                            <br/><img class="avatarImage profileAvatar" width="70" height="70"
                                      src="/images/design/noAvatar.gif"/>
                        <? } ?>
                        <? if (empty($mas['name'])) { ?>
                            <span class="profileName"><?= $mas['login'] ?></span>
                        <? } else { ?>
                            <span class="profileName"><?= $mas['name'] . " " . $mas['country'] ?></span>
                        <? } ?>
                    </div>
                    <div id="centerProfileMenuPart">
                        <a class="blogBtnUserProfile" href="/myaccount/blog/">Блог</a>
                        <a class="editProfile" href="/myaccount/">Редагувати Профіль</a>
                        <a class="commentsProfile selectedPunktClass" href="#">Коментарі</a>
                    </div>
                    <div id="rightProfileMenuPart"></div>
                </div>


            </div>
        </div>
    <?
    } //end of function EditProfile()

    // ================================================================================================
    // Function : ShowChangeEmailPass()
    // Date : 22.02.2001
    // Returns :      true,false / Void
    // Description :  Show form for change password to the new one.
    // Programmer :  Yaroslav Gyryn
    // ================================================================================================
    function ShowChangeEmailPass()
    {
        $this->Form->WriteFrontHeader('ChangeEmailPass', _LINK . 'myaccount/changepassword/', 'set_new_email_pass', NULL);
        ?>


            <div align="center"><?= $this->showErr() ?></div>

            <ul class="user-password">
                <li>
                    <input id="nameOfPred" type="hidden" class="CatinputFromForm" name="email" value="ihor@seotm.com" disabled="">
                        <?= $this->multiUser['FLD_OLD_PASSWORD']; ?>
                        <span class="red_point">*</span><br>
                        <?$this->Form->Password('oldpass', stripslashes($this->oldpass), '40') ?>
                </li>





                <li>

                        <?= $this->multiUser['FLD_NEW_PASSWORD']; ?>
                        <span class="red_point">*</span><br>

                    <? $this->Form->Password('password', $this->password, 40) ?>
                </li>

                <li>

                        <?= $this->multiUser['FLD_CONFIRM_PASSWORD']; ?>
                        <span class="red_point">*</span>
                        <br>
                        <? $this->Form->Password('password2', $this->password2, 40) ?>
                 </li>
                <li>
                    <input type="submit" class="submitBtn<?= _LANG_ID ?>" value="Изменить→"/>
                </li>

            </ul>

        <?
        $this->Form->WriteFrontFooter();
        return true;
    } //end of function ShowChangeEmailPass()


    // ================================================================================================
    // Function : ForgotPass()
    // Date : 22.02.2001
    // Returns :      true,false / Void
    // Description :  Show fomr for sending nw passord to the user, who are forgot it.
    // Programmer :  Yaroslav Gyryn
    // ================================================================================================
    function ForgotPass()
    {
        $this->Form->WriteFrontHeader('forgot_pass', _LINK . 'forgotpass.html', 'send_pass', NULL);
        ?>
        <div class="h1main"><div class="line2"></div><h1>Восстановить пароль</h1></div>
        <div id="catalogBox">

            <div id="catalogBody" >
                <b>Для восстановления пароля введите номер телефона, который использовали при регистрации.</b>
                <br/>
                <?=$this->showErr() ?>
                    <div class="password-reload">
                      <?= $this->multiUser['FLD_PHONE']; ?><br>
                      <?// $this->Form->TextBox('email', stripslashes($this->email), '$size=30') ?>
                      <? $this->Form->TextBox('phone_mob', stripslashes($this->phone_mob), 'id="phoneMob" size=30') ?>
                            <div class="submit">
                                <input type="submit" value="ОК"/>
                            </div>
                    </div>
                <? //src="<?=$this->Spr->GetImageByCodOnLang(TblSysTxt, 'IMG_FRONT_SUBMIT', $this->lang_id)?>
            </div>
        </div>
        <?
        $this->Form->WriteFrontFooter();
        return true;
    } //end of function ForgotPass()


    // ================================================================================================
    // Function : ChangeLogin()
    // Date : 22.02.2001
    // Parms :   $old_login  / old login of the user
    //           $new_login  / new login of the user
    // Returns :      true,false
    // Description :  Change login for External user in the table sys_user
    // Programmer :  Yaroslav Gyryn
    // ================================================================================================
    function ChangeLogin($old_login = NULL, $new_login = NULL)
    {
        $q = "UPDATE `" . TblSysUser . "` set `login`='$new_login' WHERE `login`='$old_login'";
        $res = $this->db->db_Query($q);
        //echo '<br>$q='.$q.' $res='.$res.' $this->db->result='.$this->db->result;
        if (!$res OR !$this->db->result)
            return false;

        $q = "UPDATE `" . TblModUser . "` set `email`='$new_login' WHERE `email`='$old_login'";
        $res = $this->db->db_Query($q);
        if (!$res OR !$this->db->result)
            return false;

        return true;
    } //end of function ChangeLogin()


    // ================================================================================================
    // Function : ShowErr()
    // Date : 22.02.2011
    // Returns :      void
    // Description :  Show errors
    // Programmer :  Yaroslav Gyryn
    // ================================================================================================
    function showErr()
    {
        $this->Form->showErr($this->Err);
    } //end of function ShowErr()


    // ================================================================================================
    // Function : ShowTextMessages()
    // Date : 22.02.2001
    // Returns :      void
    // Description :  Show text messages
    // Programmer :  Yaroslav Gyryn
    // ================================================================================================
    function ShowTextMessages($txt = NULL)
    {
        if (!empty($txt)) $this->TextMessages = $txt;
        if ($this->TextMessages) {
            $this->Form->ShowTextMessages($this->TextMessages);
        }
    } //end of function ShowTextMessages()

    // ================================================================================================
    // Function : CheckEmailFields()
    // Date : 22.02.2001
    // Returns :      $this->Err
    // Description :  Check fields of email for validation
    // Programmer :  Yaroslav Gyryn
    // ================================================================================================
    function CheckEmailFields($source = NULL)
    {
        $this->Err = NULL;
        if (empty($this->email))
            $this->Err = $this->Err . $this->multiUser['MSG_FLD_EMAIL_EMPTY'] . '<br>';
//     else{
//         if ($source=='forgotpass'){
//
//         }
//        if ( $this->email!=$this->email2 )
//            $this->Err = $this->Err.$this->multiUser['MSG_NOT_MATCH_REENTER_EMAIL'].'<br>';
//        /*if (!ereg("^[a-zA-Z0-9_.\-]+@[a-zA-Z0-9.\-].[a-zA-Z0-9.\-]+$", $this->email))
//            $this->Err = $this->Err.$this->Msg->show_text('MSG_NOT_VALID_EMAIL').'<br>';*/
//        if ($source=='forgotpass') return $this->Err;
//
//        if ( $this->email!=$this->Logon->login AND !$this->unique_login($this->email) ) {
//           //$this->Err=$this->Err.$this->Msg->show_text('MSG_NOT_UNIQUE_LOGIN_1')." ".stripslashes($this->email)." ".$this->Msg->show_text('MSG_NOT_UNIQUE_LOGIN_2').'<br>';
//           $this->Err=$this->Err.$this->multiUser['MSG_NOT_UNIQUE_LOGIN'].'<br>';
//        }
//     }
        return $this->Err;
    } //end of function CheckEmailFields()


    // ================================================================================================
    // Function : ChangePass()
    // Date : 22.02.2001
    // Returns :      true,false / Void
    // Description :  Show form for change password to the new one.
    // Programmer :  Yaroslav Gyryn
    // ================================================================================================
    /*function ChangePass()
    {
     ?>
     <div align=center><h1>Изменение пароля</h1></div>
    <form action="<?=$_SERVER['PHP_SELF']?>" method=post>
       <table border=0 cellspacing=1 cellpadding=3>
        <tr><td colspan=2 align=center><H3><?=$this->Msg->show_text('TXT_CHANGE_PASS2');?></H3>
        <tr><td colspan=2 align=center class="UserErr"><?=$this->ShowErr()?>
        <tr><td>
        <tr>
         <td><?=$this->Msg->show_text('FLD_OLD_PASSWORD');?>:
         <td><?$this->Form->Password( 'oldpass', stripslashes($this->oldpass), $size=30 )?>
        <tr>
         <td><?=$this->Msg->show_text('FLD_NEW_PASSWORD');?>:
         <td><?$this->Form->Password( 'password', stripslashes($this->password), $size=30 )?>
        <tr>
         <td><?=$this->Msg->show_text('FLD_CONFIRM_PASSWORD');?>:
         <td><?$this->Form->Password( 'password2', stripslashes($this->password2), $size=30 )?>
        <tr>
         <td colspan=2 align=center>
          <INPUT TYPE="image" src="images/design/button_save.gif">
          <input type=hidden name=set_new_pass value=set_new_pass>
        <tr><td colspan=2 align=center>
       </table>
    </form>
     <?
     return true;
    } //end of function ChangePass()       */

} //end of class UserShow
?>