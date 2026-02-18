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
		    if(is_dir(OPEN_ROOT."/assets/plugins/".$pid)):
            UTInc::DelDir(OPEN_ROOT."/assets/plugins/".$pid);
				endif;
        UTInc::GoUrl("?m=ut-plugin","成功卸载插件!");
    else:
        if(UTData::RunSql($uninstallsql)):
            UTInc::DelDir(APP_ROOT."/plugins/".$pid);
		        if(is_dir(OPEN_ROOT."/assets/plugins/".$pid)):
                UTInc::DelDir(OPEN_ROOT."/assets/plugins/".$pid);
				    endif;
            UTInc::GoUrl("?m=ut-plugin","成功卸载插件!");
        else:
            UTInc::GoUrl("-1","插件卸载失败!");
        endif;   
    endif;
}