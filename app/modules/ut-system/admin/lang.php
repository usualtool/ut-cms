<?php
use usualtool\Lib\Inc;
use usualtool\Lib\Lang;
$do=$_GET["do"];
/**
 * 语言数组
 */
$app->Runin("lang",Lang::GetLang());
/**
 * 默认语言
 */
$app->Runin("lang_default",$config["LANG"]);
/**
 * 语言选项
 */
$app->Runin("lang_option",explode(",",$config["LANG_OPTION"]));
/**
 * 载入模板
 */
$app->Open("lang.cms");
if($do=="setup"){
    $lang_default = Inc::SqlCheck($_POST["lang_default"]);
    $lang_option = Inc::SqlCheck(implode(",",$_POST["lang_option"]));
    $info = file_get_contents(UTF_ROOT."/.ut.config");
    $info = preg_replace("/LANG=(.*)/","LANG={$lang_default}",$info);
    $info = preg_replace("/LANG_OPTION=(.*)/","LANG_OPTION={$lang_option}",$info);
    file_put_contents(UTF_ROOT."/.ut.config",$info);
    Inc::GoUrl("?m=ut-system&p=lang","保存配置成功!");
}