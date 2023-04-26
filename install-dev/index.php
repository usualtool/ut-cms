<?php
/**
       * --------------------------------------------------------       
       *  |    ░░░░░░░░░     █   █░▀▀█▀▀░    ░░░░░░░░░      |           
       *  |  ░░░░░░░         █▄▄▄█   █                      |            
       *  |                                                 |            
       *  | Author:HuangDou   Email:292951110@qq.com        |            
       *  | QQ-Group:583610949                              |           
       *  | WebSite:http://www.UsualTool.com                |            
       *  | UT Framework is suitable for Apache2 protocol.  |            
       * --------------------------------------------------------                
*/
require_once dirname(dirname(__FILE__)).'/'.'autoload.php';
use library\UsualToolInc\UTInc;
use library\UsualToolMysql\UTMysql;
use library\UsualToolCli\UTCli;
if(UTInc::SearchFile(UTF_ROOT."/install-dev/usualtool.lock")):
   header("location:../");
   exit();
endif;
$httpcode=UTInc::HttpCode("http://frame.usualtool.com");
$sysinfo=UTInc::GetSystemInfo();
$do=UTInc::SqlCheck($_GET["do"]);
if($do=="db-save"):
   $info = file_get_contents(UTF_ROOT."/.ut.config"); 
   foreach($_POST as $k=>$v):
       $info = preg_replace("/{$k}=(.*)/","{$k}={$v}",$info); 
   endforeach;
   file_put_contents(UTF_ROOT."/.ut.config",$info);
   UTInc::GoUrl("?do=sql","配置成功!");
endif;
?>
<!DOCTYPE html>
<html>
<head>
    <title>UTCMS V9安装</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="//cdn.staticfile.org/bootstrap/4.6.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="//cdn.staticfile.org/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="//cdn.staticfile.org/jquery/3.1.0/jquery.min.js"></script>
    <script src="//cdn.staticfile.org/bootstrap/4.5.3/js/bootstrap.min.js"></script>
    <style>
    body{font-size:0.8rem;}
    a{color:black;text-decoration:underline;}
    p{margin-bottom:0rem;font-size:14px;}
    .fontsmall{font-size:12px;}#license p{font-size:11px;
    }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12 mb-3"></div>
            <div class="col-md-12">
                <div class="border p-4">
                <?php
                if(empty($do)):?>
                    <div>
                    <form name="license" action="?do=config" method="post">
                    <p class="mb-2">
                        <b>基于以下协议，本软件是免费且开源的软件。所有自然人或团体组织请在所在国法律允许范围内合法使用本软件。</b>
                    </p>
                    <p class="mb-2">
                        文书1：<a target="_blank" href="//www.apache.org/licenses/LICENSE-2.0">UTCMS V9基于Apache2.0授权，可以通过//www.apache.org/licenses/LICENSE-2.0了解或下载到详尽的协议内容。</a>
                    </p>
                    <p class="mb-2">
                        文书2：<a target="_blank" href="//frame.usualtool.com/baike#license">UTCMS V9遵循UT开源与免费协议，任何自然人与团体组织在协议约定的范围内均可免费使用。使用人应当阅读UT序言中关于文书的部分，往此去：//frame.usualtool.com/baike#license。</a>
                    </p>
                    <p class="mb-3">
                        <b>你需要仔细阅读以上2份文书，在理解和同意的前提下，方可使用本软件。</b><br/><br/>
                        <b>Made in China , usualtool.com , HuangDou</b>
                    </p>
                    <p class="mb-2">
                        <input type="submit" class="btn btn-info" value="请查阅以上文书（90）" id="btn"/> 
                    </p>
                    </form>
                    </div>
                    <script>
                        $(function(){
                            $("#btn").prop('disabled',true);
                            var time = 90;
                            timer = setInterval(function(){
                                time--;
                                if(time>=0){
                                    $("#btn").val("请查阅以上文书（"+time+"）")
                                }else{
                                    clearInterval(timer);
                                    $("#btn").prop('disabled',false);
                                    $("#btn").val("我已阅读相关文书");
                                    $("#btn").addClass('dd');
                                }
                            },1000);
                        })
                    </script>
                <?php
                elseif($do=="config"):?>
                    <div class="row">
                        <div class="col-7">
                            <h4>简要配置</h4>
                        </div>
                        <div class="col-5 text-right">
                            <a class="btn btn-warning" href="javascript:history.go(-1)">上一步</a>
                        </div>
                    </div>
                    <hr/>
                    <form action="?do=db-save" method="post" name="form">
                    <div class="row mb-2">
                        <div class="form-group col-md-6">
                            <p>与UT通讯状态：<?php echo$httpcode;?> <?php echo $httpcode=="200" ? "" : "， 因通讯障碍，安装将有极大几率失败。";?></p>
                            <p>PHP版本：<?php echo$sysinfo["PHP"];?></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="email">应用域名/IP:</label>
                            <input class="form-control" name="APPURL" id="APPURL" value="<?php echo$config["APPURL"];?>" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="email">UT令牌:</label>
                            <input class="form-control" name="UTCODE" id="UTCODE" value="<?php echo$config["UTCODE"];?>" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="email">数据库服务器:</label>
                            <input class="form-control" name="MYSQL_HOST" id="MYSQL_HOST" value="<?php echo$config["MYSQL_HOST"];?>" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="email">端口:</label>
                            <input class="form-control" name="MYSQL_PORT" id="MYSQL_PORT" value="<?php echo$config["MYSQL_PORT"];?>" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="email">数据库用户:</label>
                            <input class="form-control" name="MYSQL_USER" id="MYSQL_USER" value="<?php echo$config["MYSQL_USER"];?>" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="email">数据库密码:</label>
                            <input class="form-control" name="MYSQL_PASS" id="MYSQL_PASS" value="<?php echo$config["MYSQL_PASS"];?>" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="email">数据库名称:</label>
                            <input class="form-control" name="MYSQL_DB" id="MYSQL_DB" value="<?php echo$config["MYSQL_DB"];?>" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-info mr-2">保存配置</button>
                            <button type="reset" class="btn btn-danger mr-2">重置</button>
                        </div>
                    </div>
                    </form>
                <?php
                elseif($do=="sql"):?>
                    <div class="row">
                        <div class="col-7">
                            <h4>构建数据</h4>
                        </div>
                        <div class="col-5 text-right">
                            <a class="btn btn-warning" href="javascript:history.go(-1)">上一步</a>
                        </div>
                    </div>
                    <hr/>
                    <?php
                    if(UTMysql::ModTable("cms_admin")):?>
                        <p>已创建过数据，若要重新创建，请清除表后刷新本页。</p>
                        <p class="mb-3">不建议的方式：<a href="?do=sql&t=db-sql">强制性更新</a> 数据结构。</p>
                        <p><a class="btn btn-warning" href="?do=app">跳过，下一步</a></p>
                    <?php
                    else:
                        if($_GET["t"]=="db-sql"):
                            $sql=file_get_contents("./UTDev.sql");
                            $arr=explode(';',$sql);
                            $total=count($arr)-1;
                            $c=0;
                            for($i=0;$i<$total;$i++):
                                $k=$i+1;
                                $result=UTMysql::RunSql($arr[$i]);
                                if($result):
                                    echo"<p class='fontsmall'>第".$k."条SQL执行成功!</p>";
                                else:
                                    $c=$c+1;
                                    echo"<p class='fontsmall' style='color:red;'>第".$k."条SQL执行失败:".$arr[$i]."</p>";
                                endif;
                                if($k==$total && $c==0):
                                    UTInc::GoUrl("?do=app","创建数据成功!");
                                endif;
                            endfor;
                        endif;
                        echo'<p><a class="btn btn-info" href="?do=sql&t=db-sql">创建数据</a></p>';
                    endif;
                elseif($do=="app"):?>
                    <form action="?do=app&t=install" method="post" name="form">
                    <div class="row">
                        <div class="col-7">
                            <h4>快速搭建</h4>
                        </div>
                        <div class="col-5 text-right">
                            <a class="btn btn-warning" href="javascript:history.go(-1)">上一步</a>
                        </div>
                    </div>
                    <hr/>
                    <?php
                    if($_GET["t"]=="install"):
                        if(!empty($_POST["app"])):
                            $app=explode("__",$_POST["app"]);
                            $temp=$app[0];
                            $mods=explode(",",$app[1]);
                            for($i=0;$i<count($mods);$i++):
                                $k=$i+1;
                                echo"<br/>安装模块：".$mods[$i]."<br/>";
                                UTCli::Install(array("usualtool","install","module",$mods[$i],"-1"));
                            endfor;
                            if($k==count($mods)):
                                echo"<br/>安装模板：".$temp."<br/>";
                                UTCli::Install(array("usualtool","install","template",$temp,"-1"));
                                if(is_dir(UTF_ROOT."/app/template/".$temp)):
                                    UTMysql::UpdateData("cms_template",array("makefront"=>1),"tid='$temp'");
                                    $info=preg_replace("/TEMPFRONT=(.*)/","TEMPFRONT={$temp}",file_get_contents(UTF_ROOT."/.ut.config"));
                                    file_put_contents(UTF_ROOT."/.ut.config",$info);
                                    UTInc::GoUrl("?do=finish","快速搭建应用成功!");
                                endif;
                            endif;
                        else:
                            UTInc::GoUrl("-1","请选择模型!");
                        endif;
                    else:?>
                        <div class="row">
                        <?php
                        $data=UTInc::Gettemplate(1);
                        foreach($data as $rows):
                            if(strpos($rows["isfree"],'公开使用')!==false):
                                echo"<div class='col-md-3 mb-2'><img src='".$rows["picurl"]."' style='width:100%;' class='mb-2'><br/><input type='radio' name='app' value='".$rows["tid"]."__".$rows["module"]."' class='mr-1'>".$rows["title"]."</div>";
                            endif;
                        endforeach;
                        ?>
                        </div>
                        <p class="mt-3">
                            <button type="submit" class="btn btn-info mr-2">安装（预计20秒）</button>
                            <a class="btn btn-warning" href="?do=finish">跳过，下一步</a>
                        </p>
                        </form>
                    <?php
                    endif;
                elseif($do=="finish"):
                    file_put_contents("./usualtool.lock","lock");
                    UTInc::GoUrl("../app/dev/","后端初始账号密码为“admin”请及时修改!");
                endif;
                ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>