<?php
/**
 * Created by PhpStorm.
 * User: bogdan
 * Date: 28.02.14
 * Time: 12:09
 */

$params = '';
if($is_mandatory){
    $params .= ' class="'.$type_validate.'" ';
}
?><div class="floatContainer">
    <?
if(!$is_place_label){
    $placeholder_name = '';
    ?>
    <div class="width25 floatToLeft"><?=$label_name;?>:<?
    if($is_mandatory){
        ?> <span class="red">*</span><?
    }?></div><?
}else{
    $placeholder_name = $label_name;
    if($is_mandatory){
        $placeholder_name .= ' *';
    }
}?>
    <div class="width75 floatToRight"><?
        switch($type_field){
            case 'text':
                $Feedback->Form->TextBox($name, $value,$params,$name,$placeholder_name);
                break;
            case 'textarea':
                $Feedback->Form->TextArea($name, $value, 6, 38,$params,$name,$placeholder_name);
                break;
        }?></div>
</div><?