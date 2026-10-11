<?php
use usualtool\Lib\Inc;
use usualtool\Lib\Data;
$pid=Inc::SqlCheck($_GET["pid"]);
/**
 * 插件信息
 */
$app->Runin("plugin",Data::QueryData("cms_plugin","","pid='$pid'","","")["querydata"]);
/**
 * 插件后台转化
 */
$plugin_dir=APP_ROOT."/plugins/".$pid;
$plugin_file=$plugin_dir."/admin.php";
$config_file=$plugin_dir."/usualtool.config";
$code=is_file($config_file) ? Inc::StrSubstr("<code><![CDATA[","]]></code>",file_get_contents($config_file)) : "";
if(!empty($code) && $code!="0"){
    $code=preg_replace(array('/^\s*\?>/','/^\r?\n/'),'',$code);
    $sign=md5($code);
    $text=is_file($plugin_file) ? file_get_contents($plugin_file) : "";
    $marked=preg_match('/\/\*'.$pid.'-md5:([0-9a-f]{32})\*\//',$text,$m) ? $m[1] : "";
    if($text==="" || ($marked!=="" && $marked!==$sign)){
        @file_put_contents($plugin_file,"<?php /*".$pid."-md5:".$sign."*/?>\r\n".$code);
    }
}elseif(!is_file($plugin_file)){
    $plugin_file="";
}
$app->Runin(
    array("pid","plugin_file"),
    array($pid,$plugin_file)
);
$app->Open("plugin_view.cms");