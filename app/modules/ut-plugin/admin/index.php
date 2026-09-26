<?php
use usualtool\Lib\Inc;
use usualtool\Lib\Data;
$do=$_GET["do"];
/**
 * 载入已安装插件
 */
$app->Runin("plugin",Data::QueryData("cms_plugin","","","id asc","")["querydata"]);
/**
 * 载入模板
 */
$app->Open("index.cms");
if($do=="uninstall"){
    $pid=str_replace(".","",Inc::SqlCheck($_GET["pid"]));
    $pconfig=APP_ROOT."/plugins/".$pid."/usualtool.config";
    $plugins=file_get_contents($pconfig);
    $uninstallsql=Inc::StrSubstr("<uninstallsql><![CDATA[","]]></uninstallsql>",$plugins);
    Data::DelData("cms_plugin","pid='$pid'");
    if($uninstallsql=='0'):
        Inc::DelDir(APP_ROOT."/plugins/".$pid);
        if(is_dir(OPEN_ROOT."/assets/plugins/".$pid)):
            Inc::DelDir(OPEN_ROOT."/assets/plugins/".$pid);
        endif;
        Inc::GoUrl("?m=ut-plugin","成功卸载插件!");
    else:
        if(Data::RunSql($uninstallsql)):
            Inc::DelDir(APP_ROOT."/plugins/".$pid);
            if(is_dir(OPEN_ROOT."/assets/plugins/".$pid)):
                Inc::DelDir(OPEN_ROOT."/assets/plugins/".$pid);
            endif;
            Inc::GoUrl("?m=ut-plugin","成功卸载插件!");
        else:
            Inc::GoUrl("-1","插件卸载失败!");
        endif;   
    endif;
}