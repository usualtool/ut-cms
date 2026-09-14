<?php
use library\UsualToolInc\UTInc;
use library\UsualToolData\UTData;
$do=$_GET["do"];
if($do=="out"){
    unset($_SESSION['admin']);
    unset($_SESSION['admin_id']);
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
                if(password_verify($password,$rows['password'])){
                    UTData::InsertData("cms_admin_log",array("username"=>$username,"ip"=>$ip,"logintime"=>date('Y-m-d H:i:s',time())));
                    $_SESSION['admin']=$rows['username'];
                    $_SESSION['admin_id']=$rows['id'];
                    $_SESSION['admin_roleid']=$rows['roleid'];
                    $_SESSION['admin_avatar']=$rows['avatar'];
                    session_regenerate_id(TRUE);
                    setcookie("Nav","ut-frame");
                    setcookie("Lock",0);
                    UTInc::GoUrl("?p=index","登陆UT Develop成功!");
                }else{
                    UTInc::GoUrl("-1","账户或密码不匹配!");
                }
            }else{
                UTInc::GoUrl("-1","账户不存在!");
            }
        }else{
            UTInc::GoUrl("-1","账户或密码不能为空!");
        }
    }else{
        UTInc::GoUrl("-1","验证码不正确!");
    }    
}
$app->Open("login.cms");