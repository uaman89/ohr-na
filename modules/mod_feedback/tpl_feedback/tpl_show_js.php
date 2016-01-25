<?php
/**
 * Created by JetBrains PhpStorm.
 * User: bogdan
 * Date: 14.06.13
 * Time: 16:15
 * To change this template use File | Settings | File Templates.
 */
?>
    <script type="text/javascript">
        $(document).ready(function() {
            $("#form_mod_feedback").validationEngine();
        });
        function emailCheck (emailStr) {
            if (emailStr=="") return true;
            var emailPat=/^(.+)@(.+)$/;
            var matchArray=emailStr.match(emailPat);
            if (matchArray==null)
            {
                return false;
            }
            return true;
        }

        function verify(is_ajax_send) {
            if(is_ajax_send==1){
                if(!$("#form_mod_feedback").validationEngine('validate')) return false;
                save_order();
                return false;
            }
            return true;
        }

        function verify2() {
            var themessage = "<?=$Feedback->multi['_TXT_CHECK'].'\n';?>";
            if (document.forms.feedback.name.value=="") {
                themessage = themessage + " - <?=$Feedback->multi['_TXT_CHECK_FIO'].'\n';?>";
            }
            if ((!emailCheck(document.forms.feedback.e_mail.value))||(document.forms.feedback.e_mail.value=='')) {
                themessage = themessage + " - <?=$Feedback->multi['_TXT_CHECK_EMAIL'].'\n';?>";
            }
            if (document.forms.feedback.tel.value=="") {
                themessage = themessage + " - <?=$Feedback->multi['_TXT_CHECK_TEL'].'\n';?>";
            }
            if (document.forms.feedback.question.value=="") {
                themessage = themessage + " - <?=$Feedback->multi['MSG_EMPTY_QUESTION'].'\n';?>";
            }

            if (themessage == "<?=$Feedback->multi['_TXT_CHECK'].'\n';?>")
            {
                save_order_left();
                return true;
            }
            else
                alert(themessage);
            return false;
        }

        function save_order(){
            $.ajax({
                type: "POST",
                data: $("#form_mod_feedback").serialize() ,
                success: function(msg){
                    //alert(msg);
                    $("#feedback").html( msg );
                },
                beforeSend : function(){
                    $("#feedback").html('<div style="text-align:center;"><img src="/images/design/popup/ajax-loader.gif" alt="" title="" /></div>');
                }
            });
        }

        function save_order_left(){
            $.ajax({
                type: "POST",
                data: $("#feedback").serialize() ,
                url: "<?=_LINK;?>feedback_ajax/",
                success: function(msg){
                    //alert(msg);
                    $("#container_feedback").html( msg );
                },
                beforeSend : function(){
                    //$("#sss").html("");
                    $("#rez").html('<div style="text-align:center;"><img src="/images/design/popup/ajax-loader.gif" alt="" title="" /></div>');
                }
            });
        }
    </script>
<?