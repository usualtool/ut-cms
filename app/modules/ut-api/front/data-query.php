<?php
use library\UsualToolInc\UTInc;
use library\UsualToolData\UTData;
require'data-verify.php';
$table=UTInc::SqlCheck($_POST["table"]);
$field=empty($_POST["field"]) ? "" : UTInc::SqlCheck($_POST["field"]);
$where=empty($_POST["where"]) ? "" : $_POST["where"];
$limit=empty($_POST["limit"]) ? "" : UTInc::SqlCheck($_POST["limit"]);
$order=empty($_POST["order"]) ? "" : UTInc::SqlCheck($_POST["order"]);
$lg=empty($_POST["lang"]) ? 0 : UTInc::SqlCheck($_POST["lang"]);
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
    echo json_encode($querydata,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);	
else:
	echo'[{"error":1}]';
endif;