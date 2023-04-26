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
            echo "<script>alert('创建成功!');window.location.href='?m=ut-power&p=role'</script>";
        else:
            echo "<script>alert('创建失败!');window.location.href='?m=ut-power&p=rolex'</script>";
        endif;
}
if($do=="mon"){
    $id=UTInc::SqlCheck($_POST["id"]);
        if(UTData::UpdateData("cms_admin_role",array(
            "role"=>UTInc::SqlCheck($_POST["role"]),
            "module"=>UTInc::SqlCheck(implode(",",$_POST["module"]))),"id='$id'")):
            echo "<script>alert('编辑成功!');window.location.href='?m=ut-power&p=role'</script>";
        else:
            echo "<script>alert('编辑失败!');window.location.href='?m=ut-power&p=rolex&id=$id'</script>";
        endif;
}
if($do=="del"){
    if($id==1):
        echo "<script>alert('删除失败,第一条记录不可删除!');window.location.href='?m=ut-power&p=role'</script>";
    else:
        if(UTData::DelData("cms_admin_role","id='$id'")):
            echo "<script>alert('删除成功!');window.location.href='?m=ut-power&p=role'</script>";
        else:
            echo "<script>alert('删除失败!');window.location.href='?m=ut-power&p=role'</script>";
        endif;
    endif;
}