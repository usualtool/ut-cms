<?php
use library\UsualToolInc\UTInc;
use library\UsualToolData\UTData;
/**
 * 在建项目
 */
$app->Runin("task",array(UTInc::Auth($config["UTCODE"],$config["UTFURL"],"task")));
/**
 * AD
 */
$app->Runin("ad",explode("^",explode("|",UTInc::Auth($config["UTCODE"],$config["UTFURL"],"upapi"))[1]));
/**
 * 载入模板
 */
$app->Open("task.cms");