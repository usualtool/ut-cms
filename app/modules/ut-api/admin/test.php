<?php
$do=empty($_GET["do"]) ? "handle" : $_GET["do"];
$app->Runin(array("do","code"),array($do,$config["UTCODE"]));
$app->Open("test.cms");