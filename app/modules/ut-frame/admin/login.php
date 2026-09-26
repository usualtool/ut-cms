<?php
use usualtool\Lib\Inc;
use usualtool\Lib\Data;
$do=$_GET["do"];
if($do=="out"){
    unset($_SESSION['admin']);
    unset($_SESSION['admin_id']);
    setcookie("Nav","ut-frame");
    echo"<script>alert('登出UT Develop成功!');window.location.href='?p=login'</script>";
}
if($do=="login"){
    $username=Inc::SqlCheck($_POST["username"]);
    $password=Inc::SqlCheck($_POST["password"]);
    $code=Inc::SqlCheck(strtolower($_POST["code"]));
    $ip=Inc::SqlCheck(Inc::GetIp());
    if($_SESSION['authcode']==$code){
        if(!empty($username)&&!empty($password)){
            $data=Data::QueryData("cms_admin","","username='$username'","","");
            if($data["querynum"]==1){
                $rows=$data["querydata"][0];
                if(password_verify($password,$rows['password'])){
                    if($rows["state"]==1):
                        Inc::GoUrl("-1","账户状态异常!");
                    endif;
                    Data::InsertData("cms_admin_log",array("username"=>$username,"ip"=>$ip,"logintime"=>date('Y-m-d H:i:s',time())));
                    $_SESSION['admin']=$rows['username'];
                    $_SESSION['admin_id']=$rows['id'];
                    $_SESSION['admin_roleid']=$rows['roleid'];
                    $_SESSION['admin_avatar']=$rows['avatar'];
                    session_regenerate_id(TRUE);
                    setcookie("Nav","ut-frame");
                    setcookie("Lock",0);
                    Inc::GoUrl("?p=index","登陆UT Develop成功!");
                }else{
                    Inc::GoUrl("-1","账户或密码不匹配!");
                }
            }else{
                Inc::GoUrl("-1","账户不存在!");
            }
        }else{
            Inc::GoUrl("-1","账户或密码不能为空!");
        }
    }else{
        Inc::GoUrl("-1","验证码不正确!");
    }    
}
$app->Open("login.cms");