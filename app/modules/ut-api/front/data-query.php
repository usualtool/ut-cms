<?php
use library\UsualToolInc\UTInc;
use library\UsualToolData\UTData;
$endstr=substr($config["APPURL"],strlen($config["APPURL"])-1);
$weburl=$endstr=="/" ? substr($config["APPURL"],0,strlen($config["APPURL"])-1) : $config["APPURL"];
$lg=$_GET["lang"]==true ? 1 : 0;
$auth=UTInc::SqlCheck($_GET["auth"]);
$table=UTInc::SqlCheck($_GET["table"]);
$field=UTInc::SqlCheck($_GET["field"]);
$where=str_replace("%27","'",UTInc::SqlCheck(str_replace("'","%27",str_replace("]","",str_replace("[","",$_GET["where"])))));
$limit=UTInc::SqlCheck($_GET["limit"]);
$order=UTInc::SqlCheck($_GET["order"]);
$class=UTInc::SqlCheck($_GET["class"]);
if($config["UTCODE"]==$auth):
    if(UTData::ModTable($table)):
        $data=UTData::QueryData(
            $table,
            $field,
            $where,
            $order,
            $limit,
            $lg
        );
        $querydata=$data["querydata"];
        if($class==1):
            for($i=0;$i<count($querydata);$i++):
                $catname=UTData::QueryData($table."_cat","","id='".$querydata[$i]['catid']."'","","")["querydata"][0]['name'];
                $catnames[]=array("catname"=>$catname);
            endfor;
            UTInc::ArrayMerge($querydata,$catnames);
        endif;
        $jsondata=str_replace("http:","https:",str_replace(',assets\/images',','.$weburl.'\/assets\/images',str_replace('"assets\/images','"'.$weburl.'\/assets\/images',json_encode($querydata,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT))));
        echo$jsondata;
        else:
            echo'[{"error":1}]';
        endif;    
else:
    echo'[{"error":1}]';
endif;