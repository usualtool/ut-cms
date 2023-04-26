<?php
header('content-type:application/json;charset=utf8');
use library\UsualToolInc\UTInc;
use library\UsualToolData\UTData;
$key=UTInc::SqlCheck($_POST["key"]);
$data=UTData::QueryData("cms_module","mid,modname,modurl","mid<>'ut-frame' and (mid like '%$key%' or modname like '%$key%' or backitem like '%$key%')","","")["querydata"];
echo json_encode($data);