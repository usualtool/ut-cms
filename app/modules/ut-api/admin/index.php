<?php
use usualtool\Lib\Inc;
use usualtool\Lib\Data;
$app->Runin("apikey",md5($config["UTCODE"]));
$app->Runin("data",Data::QueryData("cms_api_set","","","","1")["querydata"]);
$app->Open("index.cms");
if($_GET["do"]=="config"):
    $id=Inc::SqlCheck($_POST["id"]);
    $white=Inc::SqlCheck($_POST["white"]);
    $opentable=Inc::SqlCheck($_POST["opentable"]);
    $authtable=Inc::SqlCheck($_POST["authtable"]);
    $authquery=Inc::SqlCheck($_POST["authquery"]);
    if(Data::UpdateData("cms_api_set",array(
        "white"=>$white,
        "opentable"=>$opentable,
        "authtable"=>$authtable,
        "authquery"=>$authquery,
    ),"id='$id'")):
        Inc::GoUrl("?m=ut-api","保存配置成功!");
    else:
        Inc::GoUrl("-1","保存配置失败!");
    endif;
endif;