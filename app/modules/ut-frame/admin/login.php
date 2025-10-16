<?php
use library\UsualToolInc\UTInc;
use library\UsualToolData\UTData;
$do=$_GET["do"];
if($do=="out"){
    unset($_SESSION['admin']);
    unset($_SESSION['adminid']);
    setcookie("Nav","ut-frame");
    echo"<script>alert('登出UT Develop成功!');window.location.href='?p=login'</script>";
}
if($do=="login"){
    $username=UTInc::SqlCheck($_POST["username"]);
    $password=UTInc::SqlCheck($_POST["password"]);
    $code=UTInc::SqlCheck(strtolower($_POST["code"]));
    $ip=UTInc::SqlCheck(UTInc::GetIp());
    if($_SESSION['authcode']==$code){
        if(!empty($username)&&!empty($password)){
            $data=UTData::QueryData("cms_admin","","username='$username'","","");
            if($data["querynum"]==1){
                $rows=$data["querydata"][0];
                $shaupass=sha1($rows['salts'].$password);
                if($shaupass==$rows['password']){
                    UTData::InsertData("cms_admin_log",array("username"=>$username,"ip"=>$ip,"logintime"=>date('Y-m-d H:i:s',time())));
                    $_SESSION['admin']=$rows['username'];
                    $_SESSION['admin_id']=$rows['id'];
                    $_SESSION['admin_roleid']=$rows['roleid'];
                    $_SESSION['admin_avatar']=$rows['avatar'];
                    session_regenerate_id(TRUE);
                    setcookie("Nav","ut-frame");
                    setcookie("Lock",0);
                    echo"<script>alert('登陆UT Develop成功!');window.location.href='?p=index'</script>";
                }else{
                    echo"<script>alert('账户或密码不匹配!');window.history.go(-1);</script>";
                }
            }else{
                echo"<script>alert('账户不存在!');window.history.go(-1);</script>";
            }
        }else{
        echo"<script>alert('账户或密码不能为空!');window.history.go(-1);</script>";
        }
    }else{
    echo"<script>alert('验证码不正确!');window.history.go(-1);</script>";
    }    
}
$app->Open("login.cms");