<?php
use library\UsualToolInc\UTInc;
use library\UsualToolData\UTData;
$pagelink="?m=".$m."&p=".$p;
$page=empty($_GET["page"]) ? 1 : $_GET["page"];
$pagenum=10;
$minid=$pagenum*($page-1);
$data=UTData::QueryData("oauth_users","","","addtime desc","$minid,$pagenum");
$querynum=$data["querynum"];
$querydata=$data["querydata"];
$totalpage=ceil($querynum/$pagenum);
$app->Runin(array("total","curpage","listnum","pagelink","data"),array($totalpage,$page,$pagenum,$pagelink,$querydata));
$app->Open("user.cms");
if($_GET["do"]=="creat"):
    $username=UTInc::SqlCheck($_POST["username"]);
    $password=md5(UTInc::SqlCheck($_POST["password"]));
    $fullname=UTInc::SqlCheck($_POST["fullname"]);
    $email=UTInc::SqlCheck($_POST["email"]);
    $scope=UTInc::SqlCheck($_POST["scope"]);
    if(empty($username) || empty($password) || empty($email)):
        UTInc::GoUrl("-1","必填项不能为空");
    endif;
    if(UTData::QueryData("oauth_users","","username='$username' or email='$email'")["querynum"]>0):
        UTInc::GoUrl("-1","用户名或邮件已存在");
    else:
        if(UTData::InsertData("oauth_users",array(
            "username"=>$username,
            "password"=>$password,
            "fullname"=>$fullname,
            "email"=>$email,
            "addtime"=>date('Y-m-d H:i:s',time()))
        )):
            UTInc::GoUrl("?m=ut-oauth&p=user","创建用户成功");
        else:
            UTInc::GoUrl("-1","创建用户失败");
        endif;
    endif;
endif;