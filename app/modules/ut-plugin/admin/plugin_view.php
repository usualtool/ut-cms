<?php
use library\UsualToolInc\UTInc;
use library\UsualToolData\UTData;
$pid=UTInc::SqlCheck($_GET["pid"]);
/**
 * 插件信息
 */
$app->Runin("plugin",UTData::QueryData("cms_plugin","","pid='$pid'","","")["querydata"]);
/**
 * 插件后台转化，兼容2018版本插件
 */
$plugin=file_get_contents(APP_ROOT."/plugins/".$pid."/usualtool.config");
$plugin_code=UTInc::StrSubstr("<plugincode><![CDATA[","]]></plugincode>",$plugin);
$app->Runin("plugin_code",$plugin_code);
/**
 * 载入模板
 */
$app->Open("plugin_view.cms");