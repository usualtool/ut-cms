<?php
use library\UsualToolInc\UTInc;
use library\UsualToolData\UTData;
$do=$_GET["do"];
/**
 * 获取已安装模块
 */
$app->Runin("modules",UTData::QueryData("cms_module","","bid>0","id asc","")["querydata"]);
/**
 * 载入模板
 */
$app->Open("template_creat.cms");
if($do=="save"){
    $module=UTInc::SqlCheck($_POST["module"]);
    $skin=UTInc::SqlCheck($_POST["skin"]);
    $page=UTInc::SqlCheck($_POST["page"]);
    $content=htmlspecialchars_decode($_POST["content"]);
    file_put_contents(APP_ROOT."/modules/".$module."/skin/".$skin."/".$page,$content);
    UTInc::GoUrl("?m=ut-template&p=template_creat","创建模板成功!");
}