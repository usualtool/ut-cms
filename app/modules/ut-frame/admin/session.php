<?php
use library\UsualToolInc\UTInc;
use library\UsualToolData\UTData;
if(isset($_SESSION['admin'])&&isset($_SESSION['admin_id'])&&!empty($_SESSION['admin'])&&!empty($_SESSION['admin_id'])):
    $admin=$_SESSION['admin'];
    $admin_id=$_SESSION['admin_id'];
    $admin_roleid=$_SESSION['admin_roleid'];
    $admin_avatar=$_SESSION['admin_avatar'];
    $adminnum=UTData::QueryData("cms_admin","","id='$admin_id' and username='$admin'","","","0")["querynum"];
    if($adminnum!==1):
        header("location:?p=login");exit();
    else:
        $myrole=UTData::QueryData("cms_admin_role","","id='$admin_roleid'","","")["querydata"][0];
        $app->Runin(array("admin_id","admin_role","admin_user","admin_avatar"),array($admin_id,$myrole["role"],$admin,$admin_avatar));
        if(!UTInc::Contain($m,explode(",",$myrole["module"]))){
            echo"<script>alert('权限不足!');window.history.go(-1);</script>";
            exit();
        }
    endif;
else:
    header("location:?p=login");
    exit();
endif;