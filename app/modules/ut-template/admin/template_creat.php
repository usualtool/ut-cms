<?php
use usualtool\Lib\Inc;
use usualtool\Lib\Data;
$do=$_GET["do"];
/**
 * 获取已安装模块
 */
$app->Runin("modules",Data::QueryData("cms_module","","bid>0","id asc","")["querydata"]);
/**
 * 载入模板
 */
$app->Open("template_creat.cms");
if($do=="save"){
    $module=Inc::SqlCheck($_POST["module"]);
    $skin=Inc::SqlCheck($_POST["skin"]);
    $page=Inc::SqlCheck($_POST["page"]);
    $content=htmlspecialchars_decode($_POST["content"]);
    file_put_contents(APP_ROOT."/modules/".$module."/skin/".$skin."/".$page,$content);
    Inc::GoUrl("?m=ut-template&p=template_creat","创建模板成功!");
}