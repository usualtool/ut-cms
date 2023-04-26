<?php
use library\UsualToolInc\UTInc;
use library\UsualToolData\UTData;
$do=$_GET["do"];
/**
 * 载入已安装插件
 */
$app->Runin("plugin",UTData::QueryData("cms_plugin","","","id asc","")["querydata"]);
/**
 * 载入模板
 */
$app->Open("index.cms");
if($do=="uninstall"){
    $pid=str_replace(".","",UTInc::SqlCheck($_GET["pid"]));
    $pconfig=APP_ROOT."/plugins/".$pid."/usualtool.config";
    $plugins=file_get_contents($pconfig);
    $uninstallsql=UTInc::StrSubstr("<uninstallsql><![CDATA[","]]></uninstallsql>",$plugins);
    UTData::DelData("cms_plugin","pid='$pid'");
    if($uninstallsql=='0'):
        UTInc::DelDir(APP_ROOT."/plugins/".$pid);
        echo"<script>alert('成功卸载插件!');window.location.href='?m=ut-plugin'</script>";
    else:
        if(UTData::RunSql($uninstallsql)):
            UTInc::DelDir(APP_ROOT."/plugins/".$pid);
            echo"<script>alert('成功卸载插件!');window.location.href='?m=ut-plugin'</script>";
        else:
            echo"<script>alert('插件卸载失败!');window.location.href='?m=ut-plugin'</script>";
        endif;   
    endif;
}