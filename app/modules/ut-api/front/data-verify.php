<?php
use library\UsualToolInc\UTInc;
use library\UsualToolData\UTData;
$api=UTData::QueryData("cms_api_set","","","","1")["querydata"][0];
$white=explode(",",$api["white"]);
$opentable=explode(",",$api["opentable"]);
$authtable=$api["authtable"];
$authquery=UTInc::DeSqlCheck($api["authquery"]);
$headers=$_SERVER;
if(!isset($headers["HTTP_TOKEN"])):
    UTInc::GoUrl('','[{"error":"Token Empty"}]');
endif;
$token=$headers["HTTP_TOKEN"];
$time=$headers["HTTP_TIME"];
$vtoken=md5(md5($config["UTCODE"]).strtotime($time));
if($token!=$vtoken):
    UTInc::GoUrl('','[{"error":"Token Error"}]');
endif;
$referer=$headers["HTTP_REFERER"];
if(!UTInc::Contain($config["APPURL"],$referer) && !UTInc::Contain($referer,$white)):
    UTInc::GoUrl('','[{"error":"Origin Error"}]');
endif;
if(!empty($_POST["table"]) || !empty($_GET["table"])):
    $table=empty($_GET["table"]) ? UTInc::SqlCheck($_POST["table"]) : UTInc::SqlCheck($_GET["table"]);
    if(!in_array($table,$opentable)):
        UTInc::GoUrl('','[{"error":"Not Open Table"}]');
    endif;
endif;
if($_POST["action"]=="add" || $_POST["action"]=="mon" || $_POST["action"]=="del"):
    if(!isset($headers["HTTP_SECRET"])):
        UTInc::GoUrl('','[{"error":"Secret Empty"}]');
    endif;
    $secret=explode(",",UTInc::SqlCheck($headers["HTTP_SECRET"]));
    for($i=0;$i<count($secret);$i++):
        $authquery=str_replace("[".$i."]","'".$secret[$i]."'",$authquery);
    endfor;
    $data=UTData::QueryData($authtable,"",$authquery);
    print_r($data);
    if($data["querynum"]<=0):
        UTInc::GoUrl('','[{"error":"Secret Error"}]');
    endif;
endif;