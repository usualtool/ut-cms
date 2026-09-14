<?php
use library\UsualToolInc\UTInc;
use library\UsualToolData\UTData;
/**
 * 写入数据
 */
$app->Runin("datalist",UTData::QueryData("cms_admin","","","addtime desc","")["querydata"]);
/**
 * 载入模板
 */
$app->Open("index.cms");
if($_GET["do"]=="update"){
    $id=UTInc::SqlCheck($_GET["id"]);
    $state=UTInc::SqlCheck($_GET["state"]);
    if(UTData::UpdateData("cms_admin",array("state"=>$state),"id='$id'")):
        UTInc::GoUrl("?m=ut-power","更新状态成功!");
    else:
        UTInc::GoUrl("-1","更新状态失败!");
    endif;
}