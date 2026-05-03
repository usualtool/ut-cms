<?php
use library\UsualToolInc\UTInc;
use library\UsualToolData\UTData;
$list=UTData::JoinQuery("SHOW TABLES");
$table=array_column($list['querydata'],'Tables_in_'.$config["MYSQL_DB"]);
$data=UTData::QueryData("oauth_scopes","","")["querydata"];
$app->Runin(
    array("table","data"),
    array($table,$data)
);
$app->Open("role.cms");
if($_GET["do"]=="creat"):
    $title=UTInc::SqlCheck($_POST["title"]);
    $scope=UTInc::SqlCheck($_POST["scope"]);
    $dbtable=UTInc::SqlCheck($_POST["dbtable"]);
    if(empty($title) || empty($scope)):
        UTInc::GoUrl("-1","必填项不能为空");
    endif;
    if(UTData::QueryData("oauth_scopes","","title='$title' or scope='$scope' or dbtable='$dbtable'")["querynum"]>0):
        UTInc::GoUrl("-1","中英文权限名称或数据表已使用过");
    else:
        if(UTData::InsertData("oauth_scopes",array(
            "title"=>$title,
            "scope"=>$scope,
            "dbtable"=>$dbtable)
        )):
            UTInc::GoUrl("?m=ut-oauth&p=role","新增成功");
        else:
            UTInc::GoUrl("-1","新增失败");
        endif;
    endif;
endif;