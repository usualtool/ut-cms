<?php
use usualtool\Lib\Data;
/**
 * 写入数据
 */
$app->Runin("datalist",Data::QueryData("cms_admin_role","","","id desc","")["querydata"]);
/**
 * 载入模板
 */
$app->Open("role.cms");