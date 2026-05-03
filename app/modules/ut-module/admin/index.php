<?php
use library\UsualToolInc\UTInc;
use library\UsualToolData\UTData;
$do=$_GET["do"];
/**
 * 载入已安装模块
 */
$app->Runin("module_",UTData::QueryData("cms_module","","bid>0","id asc","")["querydata"]);
/**
 * 载入模板
 */
$app->Open("index.cms");
if($do=="uninstall"){
    $mid=str_replace(".","",UTInc::SqlCheck($_GET["mid"]));
    $modconfig=APP_ROOT."/modules/".$mid."/usualtool.config";
    $mods=file_get_contents($modconfig);
    $uninstallsql=UTInc::StrSubstr("<uninstallsql><![CDATA[","]]></uninstallsql>",$mods);
    UTData::DelData("cms_module","mid='$mid'");
    $role=UTData::QueryData("cms_admin_role","","","","")["querydata"];
    foreach($role as $rows):
        $role_range=UTData::QueryData("cms_admin_role","","id='".$rows["id"]."'","","")["querydata"][0]["module"];
        $new_range=rtrim(str_replace(",,",",",str_replace($mid,"",$role_range)),",");
        UTData::UpdateData("cms_admin_role",array("module"=>$new_range),"id='".$rows["id"]."'");
    endforeach;
    if($uninstallsql=='0'):
        UTInc::DelDir(APP_ROOT."/modules/".$mid);
		    if(is_dir(OPEN_ROOT."/assets/modules/".$mid)):
            UTInc::DelDir(OPEN_ROOT."/assets/modules/".$mid);
				endif;
		    UTInc::GoUrl("?m=ut-module","成功卸载模块!");
    else:
        if(UTData::RunSql($uninstallsql)):
            UTInc::DelDir(APP_ROOT."/modules/".$mid);
		        if(is_dir(OPEN_ROOT."/assets/modules/".$mid)):
                UTInc::DelDir(OPEN_ROOT."/assets/modules/".$mid);
				    endif;
		        UTInc::GoUrl("?m=ut-module","成功卸载模块!");
        else:
		        UTInc::GoUrl("-1","模块卸载失败!");
        endif;   
    endif;
}