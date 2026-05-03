<?php
use library\UsualToolInc\UTInc;
use library\UsualToolData\UTData;
require'data-verify.php';
$keyword=UTInc::SqlCheck($_GET["keyword"]);
if(UTData::ModTable("cms_search")):
    $searchdata=UTData::SearchData($keyword);
    echo json_encode($searchdata,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);        
else:
    echo'[{"error":1}]';
endif;