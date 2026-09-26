<?php
use usualtool\Lib\Inc;
use usualtool\Lib\Data;
require'data-verify.php';
$keyword=Inc::SqlCheck($_GET["keyword"]);
if(Data::ModTable("cms_search")):
    $searchdata=Data::SearchData($keyword);
    echo json_encode($searchdata,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);        
else:
    echo'[{"error":1}]';
endif;