<?php
use usualtool\Lib\Inc;
use usualtool\Lib\Data;
/**
 * 传递参数过程
 */
$do=Inc::SqlCheck($_GET["do"]);
/**
 * 载入模板
 */
$app->Open("sql.cms");
/**
 * SQL查询
 */
if($do=="sql"){
    $sql=$_POST['sql'];
    if(!empty($sql)){
        $res=Data::RunSql($sql);
        if($res){
            echo "<script>alert('SQL执行成功!');window.location.href='?m=ut-data&p=sql'</script>";	
        }else{
            echo "<script>alert('SQL执行失败!');window.location.href='?m=ut-data&p=sql'</script>";
        }
    }else{
        echo "<script>alert('SQL执行失败!关键语句为空!');window.location.href='?m=ut-data&p=sql'</script>";
    }
}