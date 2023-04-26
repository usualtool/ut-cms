<?php
use library\UsualToolInc\UTInc;
use library\UsualToolData\UTData;
$weburl=substr($config["APPURL"],0,-1);
$auth=UTInc::SqlCheck($_GET["auth"]);
$keyword=UTInc::SqlCheck($_GET["keyword"]);
if($config["UTCODE"]==$auth):
    if(UTData::ModTable("cms_search")):
        $searchdata=UTData::SearchData($keyword);
        $jsondata=str_replace("http:","https:",str_replace(',images',','.$weburl.'\/images',str_replace('"images','"'.$weburl.'\/images',json_encode($searchdata,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT))));
        echo$jsondata;        
    else:
        echo'[{"error":1}]';
    endif;
else:
    echo'[{"error":1}]';
endif;