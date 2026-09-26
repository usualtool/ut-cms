<?php
use usualtool\Lib\Inc;
use usualtool\Lib\Data;
$do=$_GET["do"];
/**
 * 载入已安装模块
 */
$app->Runin("module_",Data::QueryData("cms_module","","bid>0","id asc","")["querydata"]);
/**
 * 载入模板
 */
$app->Open("index.cms");
if($do=="uninstall"){
    $mid=str_replace(".","",Inc::SqlCheck($_GET["mid"]));
    $modconfig=APP_ROOT."/modules/".$mid."/usualtool.config";
    $mods=file_get_contents($modconfig);
    $uninstallsql=Inc::StrSubstr("<uninstallsql><![CDATA[","]]></uninstallsql>",$mods);
    Data::DelData("cms_module","mid='$mid'");
    $role=Data::QueryData("cms_admin_role","","","","")["querydata"];
    foreach($role as $rows):
        $role_range=Data::QueryData("cms_admin_role","","id='".$rows["id"]."'","","")["querydata"][0]["module"];
        $new_range=rtrim(str_replace(",,",",",str_replace($mid,"",$role_range)),",");
        Data::UpdateData("cms_admin_role",array("module"=>$new_range),"id='".$rows["id"]."'");
    endforeach;
    if($uninstallsql=='0'):
        Inc::DelDir(APP_ROOT."/modules/".$mid);
            if(is_dir(OPEN_ROOT."/assets/modules/".$mid)):
            Inc::DelDir(OPEN_ROOT."/assets/modules/".$mid);
                endif;
            Inc::GoUrl("?m=ut-module","成功卸载模块!");
    else:
        if(Data::RunSql($uninstallsql)):
            Inc::DelDir(APP_ROOT."/modules/".$mid);
                if(is_dir(OPEN_ROOT."/assets/modules/".$mid)):
                Inc::DelDir(OPEN_ROOT."/assets/modules/".$mid);
                    endif;
                Inc::GoUrl("?m=ut-module","成功卸载模块!");
        else:
                Inc::GoUrl("-1","模块卸载失败!");
        endif;   
    endif;
}