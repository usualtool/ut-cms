<?php
use usualtool\Lib\Data;
$do=empty($_GET["do"]) ? "handle" : $_GET["do"];
$api=Data::QueryData("cms_admin","","","id asc","1")["querydata"][0];
$app->Runin(
    array("do","key","secret"),
    array($do,md5($config["UTCODE"]),$api["username"].",".$api["password"])
);
$app->Open("test.cms");