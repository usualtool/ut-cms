<?php
use usualtool\Oauth\Oauth;
use library\UsualToolData\UTData;
$scope=$_POST['scope'];
$where=$_POST['where'];
$order=$_POST['order'];
$limit=$_POST['limit'];
if(!$scope):
    http_response_code(400);
    echo json_encode(['error' => '没有传递必要参数scope']);
    exit;
endif;
$oauth=new Oauth();
$token=$oauth->Validate();
$scope_arr=isset($token['scope']) ? explode(' ',$token['scope']) : [];
if(!in_array($scope,$scope_arr)):
    http_response_code(403);
    echo json_encode(['error' => 'Insufficient scope: "'.$scope.'" required']);
    exit;
else:
    $dbtable=UTData::QueryData("oauth_scopes","","scope='$scope'")["querydata"][0]["dbtable"];
    $data=UTData::QueryData($dbtable,"",$where,$order,$limit);
endif;
echo json_encode($data);