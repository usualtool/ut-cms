<?php
use usualtool\Lib\Inc;
use usualtool\Lib\Data;
$list=Data::JoinQuery("SHOW TABLES");
$table=array_column($list['querydata'],'Tables_in_'.$config["MYSQL_DB"]);
$data=Data::QueryData("oauth_scopes","","")["querydata"];
$app->Runin(
    array("table","data"),
    array($table,$data)
);
$app->Open("role.cms");
if($_GET["do"]=="creat"):
    $title=Inc::SqlCheck($_POST["title"]);
    $scope=Inc::SqlCheck($_POST["scope"]);
    $dbtable=Inc::SqlCheck($_POST["dbtable"]);
    if(empty($title) || empty($scope)):
        Inc::GoUrl("-1","必填项不能为空");
    endif;
    if(Data::QueryData("oauth_scopes","","title='$title' or scope='$scope' or dbtable='$dbtable'")["querynum"]>0):
        Inc::GoUrl("-1","中英文权限名称或数据表已使用过");
    else:
        if(Data::InsertData("oauth_scopes",array(
            "title"=>$title,
            "scope"=>$scope,
            "dbtable"=>$dbtable)
        )):
            Inc::GoUrl("?m=ut-oauth&p=role","新增成功");
        else:
            Inc::GoUrl("-1","新增失败");
        endif;
    endif;
endif;