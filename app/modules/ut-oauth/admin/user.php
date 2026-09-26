<?php
use usualtool\Lib\Inc;
use usualtool\Lib\Data;
$pagelink="?m=".$m."&p=".$p;
$page=empty($_GET["page"]) ? 1 : $_GET["page"];
$pagenum=10;
$minid=$pagenum*($page-1);
$data=Data::QueryData("oauth_users","","","addtime desc","$minid,$pagenum");
$querynum=$data["querynum"];
$querydata=$data["querydata"];
$totalpage=ceil($querynum/$pagenum);
$app->Runin(array("total","curpage","listnum","pagelink","data"),array($totalpage,$page,$pagenum,$pagelink,$querydata));
$app->Open("user.cms");
if($_GET["do"]=="creat"):
    $username=Inc::SqlCheck($_POST["username"]);
    $password=md5(Inc::SqlCheck($_POST["password"]));
    $fullname=Inc::SqlCheck($_POST["fullname"]);
    $email=Inc::SqlCheck($_POST["email"]);
    $scope=Inc::SqlCheck($_POST["scope"]);
    if(empty($username) || empty($password) || empty($email)):
        Inc::GoUrl("-1","必填项不能为空");
    endif;
    if(Data::QueryData("oauth_users","","username='$username' or email='$email'")["querynum"]>0):
        Inc::GoUrl("-1","用户名或邮件已存在");
    else:
        if(Data::InsertData("oauth_users",array(
            "username"=>$username,
            "password"=>$password,
            "fullname"=>$fullname,
            "email"=>$email,
            "addtime"=>date('Y-m-d H:i:s',time()))
        )):
            Inc::GoUrl("?m=ut-oauth&p=user","创建用户成功");
        else:
            Inc::GoUrl("-1","创建用户失败");
        endif;
    endif;
endif;