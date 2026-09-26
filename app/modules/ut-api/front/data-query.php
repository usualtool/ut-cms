<?php
use usualtool\Lib\Inc;
use usualtool\Lib\Data;
require'data-verify.php';
$table=Inc::SqlCheck($_POST["table"]);
$field=empty($_POST["field"]) ? "" : Inc::SqlCheck($_POST["field"]);
$where=empty($_POST["where"]) ? "" : $_POST["where"];
$limit=empty($_POST["limit"]) ? "" : Inc::SqlCheck($_POST["limit"]);
$order=empty($_POST["order"]) ? "" : Inc::SqlCheck($_POST["order"]);
$lg=empty($_POST["lang"]) ? 0 : Inc::SqlCheck($_POST["lang"]);
if(Data::ModTable($table)):
    $data=Data::QueryData(
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