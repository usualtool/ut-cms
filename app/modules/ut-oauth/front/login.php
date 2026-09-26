<?php
use usualtool\Lib\Inc;
use usualtool\Lib\Data;
$redirect=empty($_GET['redirect']) ? '?m=ut-oauth' : urldecode($_GET['redirect']);
$app->Runin("redirect",$redirect);
$app->Open("login.cms");
if($_GET["do"]=="login"):
    $gourl=urldecode($_POST["gourl"]);
    $username=Inc::SqlCheck($_POST["username"]);
    $password=md5(Inc::SqlCheck($_POST["password"]));
    if(!empty($username)&&!empty($password)):
        $data=Data::QueryData("oauth_users","","username='$username' and password='$password'");
        if($data["querynum"]==1):
            $_SESSION['oauth_uid']=$data["querydata"][0]["id"];
            session_regenerate_id(TRUE);
            Inc::GoUrl($gourl,"登录成功");
        else:
            Inc::GoUrl("-1","账户或密码不匹配");
        endif;
    else:
        Inc::GoUrl("-1","账户或密码不能为空");
    endif;
endif;
if($_GET["do"]=="out"):
    unset($_SESSION['oauth_uid']);
    Inc::GoUrl("?m=ut-oauth","退出成功!");
endif;