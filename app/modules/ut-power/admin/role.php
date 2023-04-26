<?php
use library\UsualToolData\UTData;
/**
 * 写入数据
 */
$app->Runin("datalist",UTData::QueryData("cms_admin_role","","","id desc","")["querydata"]);
/**
 * 载入模板
 */
$app->Open("role.cms");