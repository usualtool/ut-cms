<?php
use usualtool\Lib\Inc;
use usualtool\Lib\Data;
/**
 * 写入模块数据
 */
$app->Runin("module",Data::QueryData("cms_module","","bid>0","","")["querydata"]);
/**
 * 传递参数过程
 */
$id=Inc::SqlCheck($_GET["id"]);
$do=Inc::SqlCheck($_GET["do"]);
if(!empty($id)){
    /**
     * 写入参数
     */
    $app->Runin("id",$id);
    /**
     * 写入数据
     */
    $app->Runin("data",Data::QueryData("cms_admin_role","","id='$id'","","")["querydata"]);
}
/**
 * 载入模板
 */
$app->Open("rolex.cms");
/**
 * 操作数据
 */
if($do=="add"){
    if(Data::InsertData("cms_admin_role",array(
        "role"=>Inc::SqlCheck($_POST["role"]),
        "module"=>Inc::SqlCheck(implode(",",$_POST["module"]))))):
        Inc::GoUrl("?m=ut-power&p=role","创建成功!");
    else:
        Inc::GoUrl("-1","创建失败!");
    endif;
}
if($do=="mon"){
    $id=Inc::SqlCheck($_POST["id"]);
    if(Data::UpdateData("cms_admin_role",array(
        "role"=>Inc::SqlCheck($_POST["role"]),
        "module"=>Inc::SqlCheck(implode(",",$_POST["module"]))),"id='$id'")):
        Inc::GoUrl("?m=ut-power&p=role","编辑成功!");
    else:
        Inc::GoUrl("-1","编辑失败!");
    endif;
}
if($do=="del"){
    if($id==1):
        Inc::GoUrl("?m=ut-power&p=role","删除失败,第一条记录不可删除!");
    else:
        if(Data::DelData("cms_admin_role","id='$id'")):
            Inc::GoUrl("?m=ut-power&p=role","删除成功!");
        else:
            Inc::GoUrl("-1","删除失败!");
        endif;
    endif;
}