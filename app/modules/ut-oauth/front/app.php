<?php
use library\UsualToolInc\UTInc;
use library\UsualToolData\UTData;
require_once 'session.php';
$role=UTData::QueryData("oauth_scopes","","")["querydata"];
$pagelink="?m=".$m."&p=".$p;
$page=empty($_GET["page"]) ? 1 : $_GET["page"];
$pagenum=10;
$minid=$pagenum*($page-1);
$data=UTData::QueryData("oauth_clients","","user_id='$oauth_uid'","id desc","$minid,$pagenum");
$querynum=$data["querynum"];
$querydata=$data["querydata"];
$totalpage=ceil($querynum/$pagenum);
$app->Runin(
    array("role","total","curpage","listnum","pagelink","data"),
    array($role,$totalpage,$page,$pagenum,$pagelink,$querydata)
);
$app->Open("app.cms");
if($_GET["do"]=="creat"):
    $client_id=UTInc::SqlCheck($_POST["client_id"]);
    $redirect_uri=$_POST["redirect_uri"];
    $scope=implode(" ",$_POST["scope"]);
    if(empty($client_id) || empty($redirect_uri)):
        UTInc::GoUrl("-1","必填项不能为空");
    endif;
    if(UTData::QueryData("oauth_clients","","client_id='$client_id'")["querynum"]>0):
        UTInc::GoUrl("-1","应用ID已存在");
    else:
        if(UTData::InsertData("oauth_clients",array(
            "user_id"=>$oauth_uid,
            "client_id"=>$client_id,
            "client_secret"=>UTInc::GetRandomString(16),
            "redirect_uri"=>$redirect_uri,
            "scope"=>$scope
        ))):
            UTInc::GoUrl("?m=ut-oauth&p=app","创建成功");
        else:
            UTInc::GoUrl("-1","创建失败");
        endif;
    endif;
endif;