<?php
use library\UsualToolData\UTData;
/**
 * 获取数据
 */
$pagelink="?m=".$m."&p=".$p;
$page=empty($_GET["page"]) ? 1 : $_GET["page"];
$pagenum=10;
$minid=$pagenum*($page-1);
$data=UTData::QueryData("cms_admin_log","","","logintime desc","$minid,$pagenum");
$querynum=$data["querynum"];
$querydata=$data["querydata"];
$totalpage=ceil($querynum/$pagenum);
/**
 * 写入数据
 */
$app->Runin("datalist",$querydata);
$app->Runin(array("total","curpage","listnum","pagelink"),array($totalpage,$page,$pagenum,$pagelink));
/**
 * 载入模板
 */
$app->Open("log.cms");