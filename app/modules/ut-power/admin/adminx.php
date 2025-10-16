<?php
use library\UsualToolInc\UTInc;
use library\UsualToolData\UTData;
/**
 * 写入角色数据
 */
$app->Runin("role",UTData::QueryData("cms_admin_role","","","","")["querydata"]);
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
    $app->Runin("data",UTData::QueryData("cms_admin","","id='$id'","","")["querydata"]);
}
/**
 * 载入模板
 */
$app->Open("adminx.cms");
/**
 * 操作数据
 */
if($do=="add"){
    $password=UTInc::SqlCheck($_POST["password"]);
    $passwords=UTInc::SqlCheck($_POST["passwords"]);
    $salts=UTInc::GetRandomString(5);
    if($password==$passwords):
        $passwordx=sha1($salts.$password);
        if(UTData::InsertData("cms_admin",array(
            "roleid"=>UTInc::SqlCheck($_POST["roleid"]),
            "username"=>UTInc::SqlCheck($_POST["username"]),
            "password"=>$passwordx,
            "salts"=>$salts,
            "avatar"=>UTInc::SqlCheck($_POST["avatar"]),
            "addtime"=>date('Y-m-d H:i:s',time())))):
            echo "<script>alert('创建成功!');window.location.href='?m=ut-power&p=admin'</script>";
        else:
            echo "<script>alert('创建失败!');window.location.href='?m=ut-power&p=adminx'</script>";
        endif;
    else:
        echo "<script>alert('两次密码不一致!');window.location.href='?m=ut-power&p=adminx'</script>";
    endif;
}
if($do=="mon"){
    $id=UTInc::SqlCheck($_POST["id"]);
    $password=UTInc::SqlCheck($_POST["password"]);
    $passwords=UTInc::SqlCheck($_POST["passwords"]);
    $salts=UTInc::GetRandomString(5);
    if($password==$passwords):
        $passwordx=sha1($salts.$password);
        if(UTData::UpdateData("cms_admin",array(
            "roleid"=>UTInc::SqlCheck($_POST["roleid"]),
            "username"=>UTInc::SqlCheck($_POST["username"]),
            "password"=>$passwordx,
            "salts"=>$salts,
            "avatar"=>UTInc::SqlCheck($_POST["avatar"])),"id='$id'")):
            echo "<script>alert('编辑成功!');window.location.href='?m=ut-power&p=admin'</script>";
        else:
            echo "<script>alert('编辑失败!');window.location.href='?m=ut-power&p=adminx&id=$id'</script>";
        endif;
    else:
        echo "<script>alert('两次密码不一致!');window.location.href='?m=ut-power&p=adminx&id=$id'</script>";
    endif;
}
if($do=="del"){
    $adminnum=UTData::QueryData("cms_admin","","","","","0")["querynum"];
    if($adminnum==1):
        echo "<script>alert('删除失败,已经是最后一条记录!');window.location.href='?m=ut-power&p=admin'</script>";
    else:
        if(UTData::DelData("cms_admin","id='$id'")):
            echo "<script>alert('删除成功!');window.location.href='?m=ut-power&p=admin'</script>";
        else:
            echo "<script>alert('删除失败!');window.location.href='?m=ut-power&p=admin'</script>";
        endif;
    endif;
}