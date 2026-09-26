<?php
header('content-type:application/json;charset=utf8');
use usualtool\Lib\Inc;
use usualtool\Lib\Data;
$key=Inc::SqlCheck($_POST["key"]);
$data=Data::QueryData("cms_module","mid,modname,modurl","mid<>'ut-frame' and (mid like '%$key%' or modname like '%$key%' or backitem like '%$key%')","","")["querydata"];
echo json_encode($data);