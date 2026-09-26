<?php
use usualtool\Lib\Inc;
use usualtool\Lib\Data;
/**
 * 在建项目
 */
$app->Runin("task",array(Inc::Auth($config["UTCODE"],$config["UTFURL"],"task")));
/**
 * AD
 */
$app->Runin("ad",explode("^",explode("|",Inc::Auth($config["UTCODE"],$config["UTFURL"],"upapi"))[1]));
/**
 * 载入模板
 */
$app->Open("task.cms");