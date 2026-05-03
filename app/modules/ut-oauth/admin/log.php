<?php
use library\UsualToolInc\UTInc;
use library\UsualToolData\UTData;
$pagelink="?m=".$m."&p=".$p;
$page=empty($_GET["page"]) ? 1 : $_GET["page"];
$pagenum=10;
$minid=$pagenum*($page-1);
$data=UTData::QueryData("oauth_access_tokens","","","id desc","$minid,$pagenum");
$querynum=$data["querynum"];
$querydata=$data["querydata"];
$totalpage=ceil($querynum/$pagenum);
$app->Runin(array("total","curpage","listnum","pagelink","data"),array($totalpage,$page,$pagenum,$pagelink,$querydata));
$app->Open("log.cms");