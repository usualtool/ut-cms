<?php
/**
       * --------------------------------------------------------       
       *  |                  █   █ ▀▀█▀▀                    |           
       *  |                  █▄▄▄█   █                      |           
       *  |                                                 |           
       *  |    Author: Huang Hui                            |           
       *  |    Repository 1: https://gitee.com/usualtool    |           
       *  |    Repository 2: https://github.com/usualtool   |           
       *  |    Applicable to Apache 2.0 protocol.           |           
       * --------------------------------------------------------       
*/
set_time_limit(300);
ini_set('max_execution_time', '300');
require dirname(__DIR__).'/'.'config.php';
use usualtool\Lib\Inc;
use usualtool\Lib\Cli;
use usualtool\Lib\Mysql;
if(Inc::SearchFile(OPEN_ROOT."/install-dev/usualtool.lock")){
   header("location:../");
   exit();
}
$do=$_GET["do"];
$httpcode=Inc::HttpCode($config["UTFURL"]);
$sysinfo=Inc::GetSystemInfo();
if($do=="db-save"){
   $info = file_get_contents(UTF_ROOT."/.ut.config"); 
    foreach($_POST as $k=>$v){
        $info = preg_replace("/{$k}=(.*)/","{$k}={$v}",$info); 
    }
   file_put_contents(UTF_ROOT."/.ut.config",$info);
   Inc::GoUrl("?do=sql","配置成功!");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>安装引导程式</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="/install-dev/assets/bootstrap.min.css" rel="stylesheet">
    <script src="/install-dev/assets/bootstrap.min.js"></script>
    <style>
    body{font-size:0.8rem;}
    a{color:black;text-decoration:underline;}
    p{margin-bottom:0rem;font-size:14px;}
    .fontsmall{font-size:12px;}
    #license p{font-size:11px;
    }
    .wait-mask{position:fixed;left:0;top:0;width:100%;height:100%;background:rgba(0,0,0,.45);z-index:9999;display:none;}
    .wait-mask.show{display:flex;align-items:center;justify-content:center;}
    .wait-box{background:#fff;border-radius:8px;padding:24px 36px;text-align:center;box-shadow:0 6px 24px rgba(0,0,0,.25);}
    .wait-spinner{width:36px;height:36px;margin:0 auto 12px;border:4px solid #e9ecef;border-top-color:#17a2b8;border-radius:50%;animation:waitspin 1s linear infinite;}
    @keyframes waitspin{to{transform:rotate(360deg);}}
    .wait-text{font-size:14px;color:#333;}
    </style>
</head>
<body>
    <div class="wait-mask" id="waitMask"><div class="wait-box"><div class="wait-spinner"></div><div class="wait-text" id="waitText"></div></div></div>
    <div class="container">
        <div class="row">
            <div class="col-md-12 mt-3 mb-3 text-center"><img src="<?php echo$config["APPURL"];?>/assets/logo.png?v=2.0"></div>
            <div class="col-md-12">
                <div class="border rounded p-4">
                <?php
                if(empty($do)){?>
                    <div>
                    <form name="license" action="?do=config" method="post">
                    <p class="mb-2">
                        <b>基于以下协议，本软件是免费且开源的软件。所有自然人或团体组织请在所在国法律允许范围内合法使用本软件。</b>
                    </p>
                    <p class="mb-2">
                        文书1：<a target="_blank" href="//www.apache.org/licenses/LICENSE-2.0">UTCMS基于Apache2.0授权，可以通过//www.apache.org/licenses/LICENSE-2.0了解或下载到详尽的协议内容。</a>
                    </p>
                    <p class="mb-2">
                        文书2：<a target="_blank" href="//github.com/usualtool/ut-cms/blob/master/LICENSE.ADD">UTCMS遵循UT开源与免费协议，任何自然人与团体组织在协议约定的范围内均可免费使用。使用人应当阅读UT序言中关于文书的部分，往此去：//github.com/usualtool/ut-cms/blob/master/LICENSE.ADD。</a>
                    </p>
                    <p class="mb-3">
                        <b>你需要仔细阅读以上2份文书，在理解和同意的前提下，方可使用本软件。</b><br/><br/>
                        <b>Made in China , github.com/usualtool/ut-cms , HuangHui</b>
                    </p>
                    <p class="mb-2">
                        <input type="submit" class="btn btn-info" value="请查阅以上文书（60）" id="btn"/> 
                    </p>
                    </form>
                    </div>
                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            var btn = document.getElementById('btn');
                            btn.disabled = true;
                            var time = 60;
                            var timer = setInterval(function () {
                                time--;
                                if (time >= 0) {
                                    btn.value = '请查阅以上文书（' + time + '）';
                                } else {
                                    clearInterval(timer);
                                    btn.disabled = false;
                                    btn.value = '我已阅读相关文书';
                                    btn.classList.add('dd');
                                }
                            }, 1000);
                        });
                    </script>
                <?php
                }elseif($do=="config"){
                    if(Inc::FileMode(UTF_ROOT)){
                ?><div class="row">
                        <div class="col-7">
                            <h4>简要配置</h4>
                        </div>
                        <div class="col-5 text-end">
                            <a class="btn btn-warning" href="javascript:history.go(-1)">上一步</a>
                        </div>
                    </div>
                    <hr>
                    <form action="?do=db-save" method="post" name="form">
                    <div class="row mb-2">
                        <div class="col-md-6 mb-3">
                            <p>与UT通讯状态：<?php echo $httpcode; ?><?php echo $httpcode == "200" ? "" : "， 因通讯障碍，安装将有极大几率失败。"; ?></p>
                            <p>PHP版本：<?php echo $sysinfo["PHP"]; ?></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label>应用名称:</label>
                            <input class="form-control" name="APPNAME" id="APPNAME" value="<?php echo $config["APPNAME"]; ?>" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>应用域名/IP:</label>
                            <input class="form-control" name="APPURL" id="APPURL" value="<?php echo $config["APPURL"]; ?>" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>UT令牌:</label>
                            <input class="form-control" name="UTCODE" id="UTCODE" value="<?php echo $config["UTCODE"]; ?>" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label>数据库服务器:</label>
                            <input class="form-control" name="MYSQL_HOST" id="MYSQL_HOST" value="<?php echo $config["MYSQL_HOST"]; ?>" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>端口:</label>
                            <input class="form-control" name="MYSQL_PORT" id="MYSQL_PORT" value="<?php echo $config["MYSQL_PORT"]; ?>" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label>数据库用户:</label>
                            <input class="form-control" name="MYSQL_USER" id="MYSQL_USER" value="<?php echo $config["MYSQL_USER"]; ?>" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>数据库密码:</label>
                            <input class="form-control" name="MYSQL_PASS" id="MYSQL_PASS" value="<?php echo $config["MYSQL_PASS"]; ?>" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>数据库名称:</label>
                            <input class="form-control" name="MYSQL_DB" id="MYSQL_DB" value="<?php echo $config["MYSQL_DB"]; ?>" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-info me-2">保存配置</button>
                            <button type="reset" class="btn btn-danger me-2">重置</button>
                        </div>
                    </div>
                    </form>
                <?php
                    }else{
                        echo"<p>请将/app、/log、/update、/open/assets权限设置为可写（755），并在此处 <a onclick='Refresh()' class='text-danger'>刷新页面</a></p>";
                    }
                }elseif($do=="sql"){?>
                    <div class="row">
                        <div class="col-7">
                            <h4>构建数据</h4>
                        </div>
                        <div class="col-5 text-end">
                            <a class="btn btn-warning" href="javascript:history.go(-1)">上一步</a>
                        </div>
                    </div>
                    <hr/>
                    <?php
                    if(Mysql::ModTable("cms_admin")){?>
                        <p>已创建过数据，若要重新创建，请清除表后刷新本页。</p>
                        <p class="mb-3">不建议的方式：<a href="?do=sql&t=db-sql">强制性更新</a> 数据结构。</p>
                        <p><a class="btn btn-info" href="?do=pull">下一步</a></p>
                    <?php
                    }else{
                        if($_GET["t"]=="db-sql"){
                            $sql=file_get_contents("./develop.sql");
                            $arr=explode(';',$sql);
                            $total=count($arr)-1;
                            $c=0;
                            for($i=0;$i<$total;$i++){
                                $k=$i+1;
                                $result=Mysql::RunSql($arr[$i]);
                                if($result){
                                    echo"<p class='fontsmall'>第".$k."条SQL执行成功!</p>";
                                }else{
                                    $c=$c+1;
                                    echo"<p class='fontsmall' style='color:red;'>第".$k."条SQL执行失败:".$arr[$i]."</p>";
                                }
                                if($k==$total && $c==0){
                                    Inc::GoUrl("?do=pull","创建数据成功!");
                                }
                            }
                        }
                        echo'<p><a class="btn btn-info" href="?do=sql&t=db-sql">创建数据</a></p>';
                    }
                }elseif($do=="pull"){?>
                    <div class="row">
                        <div class="col-7">
                            <h4>拉取可视包</h4>
                        </div>
                        <div class="col-5 text-end">
                            <a class="btn btn-warning" href="?do=sql">上一步</a>
                        </div>
                    </div>
                    <hr/>
                    <?php
                    if(is_dir(APP_ROOT."/modules/ut-power")){
                        echo"<p class='mb-3'>可视包已就位，无需重复拉取。</p>";
                        echo"<p><a class=\"btn btn-info\" href=\"?do=app\">下一步</a></p>";
                    }else{
                        if($_GET["t"]=="down"){
                            echo'<div class="wait-mask show"><div class="wait-box"><div class="wait-spinner"></div><div class="wait-text">正在拉取可视包，请耐心等待约1分钟...</div></div></div>';
                            echo"<p class='fontsmall'>正在拉取可视包...</p>";
                            echo str_pad('',4096);
                            flush();
                            $res=Inc::SaveFile("https://codeload.github.com/usualtool/develop/zip/refs/heads/main",UTF_ROOT."/update","develop.zip",1);
                            if(!empty($res)){
                                $zip=new ZipArchive;
                                if($zip->open(UTF_ROOT."/update/develop.zip")===TRUE){
                                    $zip->extractTo(UTF_ROOT."/update/");
                                    $zip->close();
                                    $pkg=is_dir(UTF_ROOT."/update/develop-main/") ? "develop-main" : "develop";
                                    Inc::MoveDir(UTF_ROOT."/update/".$pkg."/",UTF_ROOT);
                                    Inc::DelDir(UTF_ROOT."/update/".$pkg."/");
                                    unlink(UTF_ROOT."/update/develop.zip");
                                    $info=file_get_contents(UTF_ROOT."/.ut.config"); 
                                    $info = preg_replace("/^DEVELOP=.*/m","DEVELOP=/dev",$info);
                                    $info = preg_replace("/^ADMIN_POWER_PAGE=.*/m","ADMIN_POWER_PAGE=ut-frame/admin/session",$info);
                                    $info = preg_replace("/^ADMIN_POWER_OUT=.*/m","ADMIN_POWER_OUT=ut-frame/admin/login,ut-frame/admin/captcha",$info);
                                    file_put_contents(UTF_ROOT."/.ut.config",$info);
                                    Inc::GoUrl("?do=app","可视包拉取成功!");
                                }else{
                                    echo"<p class='fontsmall' style='color:red;'>可视包解压失败，请检查update权限!</p>";
                                }
                            }else{
                                echo"<p class='fontsmall' style='color:red;'>可视包下载失败，请检查网络后重试!</p>";
                            }
                        }
                        echo'<div class="d-flex justify-content-between"><a class="btn btn-info" href="?do=pull&t=down" onclick="return showWait(\'正在拉取可视包，请耐心等待约1分钟...\')">拉取可视包</a><a class="btn btn-warning" href="?do=app">跳过，下一步</a></div>';
                    }
                }elseif($do=="app"){?>
                    <form action="?do=app&t=install" method="post" name="form">
                    <div class="row">
                        <div class="col-7">
                            <h4>快速搭建</h4>
                        </div>
                        <div class="col-5 text-end">
                            <a class="btn btn-warning" href="?do=pull">上一步</a>
                        </div>
                    </div>
                    <hr/>
                    <?php
                    if($_GET["t"]=="install"){
                        echo'<div class="wait-mask show"><div class="wait-box"><div class="wait-spinner"></div><div class="wait-text">正在安装模块与模板，请耐心等待...</div></div></div>';
                        echo str_pad('',4096);
                        flush();
                        if(!empty($_POST["app"])){
                            $app=explode("__",$_POST["app"]);
                            $temp=$app[0];
                            if(!preg_match('/^[A-Za-z0-9_-]+$/',$temp)){
                                Inc::GoUrl("-1","参数非法!");
                            }
                            $mods=explode(",",$app[1]);
                            for($i=0;$i<count($mods);$i++){
                                $k=$i+1;
                                echo"<br/>安装模块：".$mods[$i]."<br/>";
                                flush();
                                Cli::Install(array("usualtool","install","module",$mods[$i],"-1"));
                                flush();
                            }
                            if($k==count($mods)){
                                echo"<br/>安装模板：".$temp."<br/>";
                                flush();
                                Cli::Install(array("usualtool","install","template",$temp,"-1"));
                                flush();
                                if(is_dir(APP_ROOT."/template/".$temp)){
                                    Mysql::UpdateData("cms_template",array("makefront"=>1),"tid='$temp'");
                                    $info=preg_replace("/TEMPFRONT=(.*)/","TEMPFRONT={$temp}",file_get_contents(UTF_ROOT."/.ut.config"));
                                    file_put_contents(UTF_ROOT."/.ut.config",$info);
                                    Inc::GoUrl("?do=finish","快速搭建应用成功!");
                                }
                            }
                        }else{
                            Inc::GoUrl("-1","请选择模板工程!");
                        }
                    }else{?>
                        <div class="row">
                        <?php
                        $data=Inc::Gettemplate(1);
                        foreach($data as $rows){
                            if(strpos($rows["isfree"],'公开使用')!==false){
                                echo"<div class='col-md-3 mb-2 text-center'><img src='".$rows["picurl"]."' style='width:100%;' class='mb-2'><br/><input type='radio' name='app' value='".$rows["tid"]."__".$rows["module"]."' class='mr-1'>".$rows["title"]."</div>";
                            }
                        }
                        ?>
                        </div>
                        <div class="d-flex justify-content-between border-top pt-3">
                            <button type="submit" class="btn btn-info mr-2" onclick="return showWait('正在安装模块与模板，请耐心等待...')">安装（预计20秒）</button>
                            <a class="btn btn-warning" href="?do=finish">跳过，下一步</a>
                        </div>
                        </form>
                    <?php
                    }
                }elseif($do=="finish"){
                    file_put_contents("./usualtool.lock","lock");
                    Inc::GoUrl("/dev/","安装成功!后端初始账号密码为“admin”请及时修改!");
                }
                ?>
                </div>
            </div>
        </div>
    </div>
</body>
    <script>
        function Refresh(){
            window.location.reload();
        }
        function showWait(msg){
            document.getElementById('waitText').innerText=msg;
            document.getElementById('waitMask').classList.add('show');
            return true;
        }
    </script>
</html>