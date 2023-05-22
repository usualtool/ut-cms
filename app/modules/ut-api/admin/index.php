<?php
use library\UsualToolInc\UTInc;
use library\UsualToolData\UTData;
$app->Runin("apikey",md5($config["UTCODE"]));
$app->Runin("data",UTData::QueryData("cms_api_set","","","","1")["querydata"]);
$app->Open("index.cms");
if($_GET["do"]=="config"):
    $id=UTInc::SqlCheck($_POST["id"]);
    $white=UTInc::SqlCheck($_POST["white"]);
    $opentable=UTInc::SqlCheck($_POST["opentable"]);
    $authtable=UTInc::SqlCheck($_POST["authtable"]);
    $authquery=UTInc::SqlCheck($_POST["authquery"]);
    if(UTData::UpdateData("cms_api_set",array(
        "white"=>$white,
        "opentable"=>$opentable,
        "authtable"=>$authtable,
        "authquery"=>$authquery,
    ),"id='$id'")):
        UTInc::GoUrl("?m=ut-api","保存配置成功!");
    else:
        UTInc::GoUrl("-1","保存配置失败!");
    endif;
endif;