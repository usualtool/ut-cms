<?php
use usualtool\Lib\Inc;
use usualtool\Lib\Data;
require_once 'session.php';
$app->Runin("data",$oauth["querydata"]);
$app->Open("index.cms");
if($_GET["do"]=="update"):
    if(empty($_POST["password"])):
        Inc::GoUrl("-1","新密码不能为空");
    endif;
    if(Data::UpdateData("oauth_users",array("password"=>md5($_POST["password"])),"id='$oauth_uid'")):
        Inc::GoUrl("?m=ut-oauth","更新密码成功");
    else:
        Inc::GoUrl("-1","更新密码失败");
    endif;
endif;