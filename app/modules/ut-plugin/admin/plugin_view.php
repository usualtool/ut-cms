<?php
use usualtool\Lib\Inc;
use usualtool\Lib\Data;
$pid=Inc::SqlCheck($_GET["pid"]);
/**
 * 插件信息
 */
$app->Runin("plugin",Data::QueryData("cms_plugin","","pid='$pid'","","")["querydata"]);
/**
 * 插件后台转化，兼容2018版本插件
 */
$plugin=file_get_contents(APP_ROOT."/plugins/".$pid."/usualtool.config");
$code=Inc::StrSubstr("<code><![CDATA[","]]></code>",$plugin);
if(empty($code) || $code=="0"){
    $plugin_code="此插件无管理端<br/>This plugin has no management end";
}else{
    $plugin_code=$code;
}
$app->Runin("plugin_code",$plugin_code);
/**
 * 载入模板
 */
$app->Open("plugin_view.cms");