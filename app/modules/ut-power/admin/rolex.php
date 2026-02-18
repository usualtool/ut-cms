<?php
use library\UsualToolInc\UTInc;
use library\UsualToolData\UTData;
/**
 * 写入模块数据
 */
$app->Runin("module",UTData::QueryData("cms_module","","bid>0","","")["querydata"]);
/**
 * 传递参数过程
 */
$id=UTInc::SqlCheck($_GET["id"]);
$do=UTInc::SqlCheck($_GET["do"]);
if(!empty($id)){
    /**
     * 写入参数
     */
    $app->Runin("id",$id);
    /**
     * 写入数据
     */
    $app->Runin("data",UTData::QueryData("cms_admin_role","","id='$id'","","")["querydata"]);
}
/**
 * 载入模板
 */
$app->Open("rolex.cms");
/**
 * 操作数据
 */
if($do=="add"){
        if(UTData::InsertData("cms_admin_role",array(
            "role"=>UTInc::SqlCheck($_POST["role"]),
            "module"=>UTInc::SqlCheck(implode(",",$_POST["module"]))))):
            UTInc::GoUrl("?m=ut-power&p=role","创建成功!");
        else:
            UTInc::GoUrl("-1","创建失败!");
        endif;
}
if($do=="mon"){
    $id=UTInc::SqlCheck($_POST["id"]);
        if(UTData::UpdateData("cms_admin_role",array(
            "role"=>UTInc::SqlCheck($_POST["role"]),
            "module"=>UTInc::SqlCheck(implode(",",$_POST["module"]))),"id='$id'")):
            UTInc::GoUrl("?m=ut-power&p=role","编辑成功!");
        else:
            UTInc::GoUrl("-1","编辑失败!");
        endif;
}
if($do=="del"){
    if($id==1):
        UTInc::GoUrl("?m=ut-power&p=role","删除失败,第一条记录不可删除!");
    else:
        if(UTData::DelData("cms_admin_role","id='$id'")):
            UTInc::GoUrl("?m=ut-power&p=role","删除成功!");
        else:
            UTInc::GoUrl("-1","删除失败!");
        endif;
    endif;
}