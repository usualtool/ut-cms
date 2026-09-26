<?php
header('Content-Type: application/json;charset=utf-8');
use usualtool\Lib\Inc;
use usualtool\Lib\Data;
$headers=$_SERVER;
$origin=isset($headers['HTTP_ORIGIN']) ? $headers['HTTP_ORIGIN'] : '';
$referer=$headers["HTTP_REFERER"];
$api=Data::QueryData("cms_api_set","","","","1")["querydata"][0];
$white=explode(",",$api["white"]);
$opentable=explode(",",$api["opentable"]);
$authtable=$api["authtable"];
$authquery=Inc::DeSqlCheck($api["authquery"]);
if(Inc::Contain(parse_url($origin,PHP_URL_HOST),$white)):
    header('Access-Control-Allow-Origin: '.$origin);
endif;
if(!Inc::Contain($config["APPURL"],$referer) && !Inc::Contain(parse_url($referer,PHP_URL_HOST),$white)):
    Inc::GoUrl('','[{"error":"Origin Error"}]');
endif;
if(!isset($headers["HTTP_TOKEN"])):
    Inc::GoUrl('','[{"error":"Token Empty"}]');
endif;
if(!isset($headers["HTTP_TIME"])):
    Inc::GoUrl('','[{"error":"Time Empty"}]');
endif;
$token=$headers["HTTP_TOKEN"];
$time=$headers["HTTP_TIME"];
$vtoken=md5(md5($config["UTCODE"]).strtotime($time));
if($token!=$vtoken):
    Inc::GoUrl('','[{"error":"Token Error"}]');
endif;
if($_POST["action"]=="add" || $_POST["action"]=="mon" || $_POST["action"]=="del"):
    if(!isset($headers["HTTP_SECRET"])):
        Inc::GoUrl('','[{"error":"Secret Empty"}]');
    endif;
    $secret=explode(",",Inc::SqlCheck($headers["HTTP_SECRET"]));
    for($i=0;$i<count($secret);$i++):
        $authquery=str_replace("[".$i."]","'".$secret[$i]."'",$authquery);
    endfor;
    $data=Data::QueryData($authtable,"",$authquery);
    if($data["querynum"]<=0):
        Inc::GoUrl('','[{"error":"Secret Error"}]');
    endif;
endif;
if(!empty($_POST["table"]) || !empty($_GET["table"])):
    $table=empty($_GET["table"]) ? Inc::SqlCheck($_POST["table"]) : Inc::SqlCheck($_GET["table"]);
    if(!in_array($table,$opentable)):
        Inc::GoUrl('','[{"error":"Not Open Table"}]');
    endif;
endif;