<?php
use usualtool\Lib\Inc;
use usualtool\Lib\Data;
/**
 * 写入角色数据
 */
$app->Runin("role",Data::QueryData("cms_admin_role","","","","")["querydata"]);
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
    $app->Runin("data",Data::QueryData("cms_admin","","id='$id'","","")["querydata"]);
}
/**
 * 载入模板
 */
$app->Open("admin.cms");
/**
 * 操作数据
 */
if($do=="add"){
    $password=Inc::SqlCheck($_POST["password"]);
    $passwords=Inc::SqlCheck($_POST["passwords"]);
    if($password==$passwords){
        $passwordx=password_hash($password,PASSWORD_BCRYPT,array('cost'=>12));
        if(Data::InsertData("cms_admin",array(
            "roleid"=>Inc::SqlCheck($_POST["roleid"]),
            "username"=>Inc::SqlCheck($_POST["username"]),
            "password"=>$passwordx,
            "avatar"=>Inc::SqlCheck($_POST["avatar"]),
            "addtime"=>date('Y-m-d H:i:s',time())))){
            Inc::GoUrl("?m=ut-power","创建成功!");
        }else{
            Inc::GoUrl("-1","创建失败!");
        }
    }else{
        Inc::GoUrl("-1","两次密码不一致!");
    }
}
if($do=="mon"){
    $id=Inc::SqlCheck($_POST["id"]);
    $password=Inc::SqlCheck($_POST["password"]);
    $passwords=Inc::SqlCheck($_POST["passwords"]);
    $update=array(
        "roleid"=>Inc::SqlCheck($_POST["roleid"]),
        "username"=>Inc::SqlCheck($_POST["username"]),
        "avatar"=>Inc::SqlCheck($_POST["avatar"])
    );
    if($password!="" || $passwords!=""){
        if($password!=$passwords): Inc::GoUrl("-1","两次密码不一致!"); endif;
        $update["password"]=password_hash($password,PASSWORD_BCRYPT,array('cost'=>12));
    }
    if(Data::UpdateData("cms_admin",$update,"id='$id'")){
        Inc::GoUrl("?m=ut-power","编辑成功!");
    }else{
        Inc::GoUrl("-1","编辑失败!");
    }
}
if($do=="del"){
    $adminnum=Data::QueryData("cms_admin","","","","","0")["querynum"];
    if($adminnum==1):
        Inc::GoUrl("-1","删除失败,已经是最后一条记录!");
    else:
        if(Data::DelData("cms_admin","id='$id'")):
            Inc::GoUrl("?m=ut-power","删除成功!");
        else:
            Inc::GoUrl("-1","删除失败!");
        endif;
    endif;
}