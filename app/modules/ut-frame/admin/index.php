<?php
use library\UsualToolInc\UTInc;
use library\UsualToolData\UTData;
/**
 * 写入服务器信息
 */
$app->Runin("sysinfo",UTInc::GetSystemInfo());
/**
 * 写入更新日志
 */
$app->Runin("updatelog",UTData::QueryData("cms_update","","","updatetime desc","0,5")["querydata"]);
/**
 * 写入登录日志
 */
$app->Runin("loginlog",UTData::QueryData("cms_admin_log","","","logintime desc","0,6")["querydata"]);
/**
 * 载入模板
 */
$app->Open("index.cms");