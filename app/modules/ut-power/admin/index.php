<?php
use usualtool\Lib\Inc;
use usualtool\Lib\Data;
/**
 * 写入数据
 */
$app->Runin("datalist",Data::QueryData("cms_admin","","","addtime desc","")["querydata"]);
/**
 * 载入模板
 */
$app->Open("index.cms");
if($_GET["do"]=="update"){
    $id=Inc::SqlCheck($_GET["id"]);
    $state=Inc::SqlCheck($_GET["state"]);
    if(Data::UpdateData("cms_admin",array("state"=>$state),"id='$id'")):
        Inc::GoUrl("?m=ut-power","更新状态成功!");
    else:
        Inc::GoUrl("-1","更新状态失败!");
    endif;
}