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
use usualtool\Lib\Data;
function task_guard($key,$label){
   $GLOBALS[$key]=false;
   register_shutdown_function(function()use($key,$label){
      if(!empty($GLOBALS[$key])){return;}
      $tail="";
      while(ob_get_level()>0){$tail=ob_get_clean().$tail;}
      $tail=trim(preg_replace("/\s+/"," ",strip_tags($tail)));
      $tail=trim(preg_replace("/(Fatal error|Parse error|Uncaught \w*Error).*$/su","",$tail));
      $err=error_get_last();
      if(!empty($err["message"])&&in_array($err["type"],array(E_ERROR,E_PARSE,E_CORE_ERROR,E_COMPILE_ERROR,E_USER_ERROR))){
         $tail.=" [PHP ".$err["message"]." @".basename($err["file"]).":".$err["line"]."]";
      }
      $msg=$label.($tail!="" ? "信息：".mb_substr($tail,-200) : "请查看服务器日志。");
      echo'<script>if(window.parent&&window.parent.waitFail){window.parent.waitFail('.json_encode($msg,JSON_UNESCAPED_UNICODE).');}else{alert('.json_encode($msg,JSON_UNESCAPED_UNICODE).');history.back();}</script>';
      echo'</body></html>';
   });
}
if(Inc::SearchFile(OPEN_ROOT."/install-dev/usualtool.lock")){
   header("location:../");
   exit();
}
$do=$_GET["do"];
if($do=="sql"){
   header("location:?do=pull");
   exit();
}
if($do=="db-save"){
   $info = file_get_contents(UTF_ROOT."/.ut.config"); 
    foreach($_POST as $k=>$v){
        $info = preg_replace("/{$k}=(.*)/","{$k}={$v}",$info); 
    }
   file_put_contents(UTF_ROOT."/.ut.config",$info);
   Inc::GoUrl("?do=pull","配置成功!");
}
if($do=="pull" && isset($_GET["t"]) && $_GET["t"]=="down"){
   while(ob_get_level()>0){@ob_end_flush();}
   header('Content-Type: text/html; charset=utf-8');
   echo'<!DOCTYPE html><html><head><meta charset="utf-8"><script>window.__taskChild=true;</script></head><body>';
   echo'<span data-status="正在部署可视包（源码 + 数据），约需1分钟，请耐心等待..."></span>';
   echo str_pad('',4096);
   flush();
   task_guard("dev_done","部署失败或中断！");
   ob_start();
   Cli::Install(array("usualtool","install","develop","","-1"));
   $out=ob_get_clean();
   $GLOBALS["dev_done"]=true;
   if(strpos($out,"成功安装可视包")!==false){
      $info=file_get_contents(UTF_ROOT."/.ut.config"); 
      $info = preg_replace("/^DEVELOP=.*/m","DEVELOP=/dev",$info);
      file_put_contents(UTF_ROOT."/.ut.config",$info);
      echo'<script>if(window.parent&&window.parent.waitDone){window.parent.waitDone("?do=app","可视包部署成功!");}else{location.href="?do=app";}</script>';
   }else{
      $log=trim(preg_replace("/\s+/"," ",strip_tags($out)));
      $msg="部署失败：".mb_substr($log,-160);
      echo'<script>if(window.parent&&window.parent.waitFail){window.parent.waitFail('.json_encode($msg,JSON_UNESCAPED_UNICODE).');}else{alert('.json_encode($msg,JSON_UNESCAPED_UNICODE).');history.back();}</script>';
   }
   echo'</body></html>';
   exit();
}
if($do=="app" && isset($_GET["t"]) && $_GET["t"]=="install"){
   while(ob_get_level()>0){@ob_end_flush();}
   header('Content-Type: text/html; charset=utf-8');
   echo'<!DOCTYPE html><html><head><meta charset="utf-8"><script>window.__taskChild=true;</script></head><body>';
   task_guard("app_done","安装失败或中断！");
   if(!empty($_POST["app"])){
      $app=explode("__",$_POST["app"]);
      $temp=$app[0];
      if(!preg_match('/^[A-Za-z0-9_-]+$/',$temp)){
         $GLOBALS["app_done"]=true;
         echo'<script>if(window.parent&&window.parent.waitFail){window.parent.waitFail("参数非法!");}else{alert("参数非法!");history.back();}</script>';
         echo'</body></html>';
         exit();
      }
      echo'<span data-status="正在安装模块与模板，请耐心等待..."></span>';
      echo str_pad('',4096);
      flush();
      $mods=array_values(array_filter(explode(",",$app[1]),'strlen'));
      for($i=0;$i<count($mods);$i++){
         if(!preg_match('/^[A-Za-z0-9_-]+$/',$mods[$i])){
            $GLOBALS["app_done"]=true;
            echo'<script>if(window.parent&&window.parent.waitFail){window.parent.waitFail("参数非法!");}else{alert("参数非法!");history.back();}</script>';
            echo'</body></html>';
            exit();
         }
         echo'<span data-status="正在安装模块：'.$mods[$i].'"></span>';
         flush();
         ob_start();
         Cli::Install(array("usualtool","install","module",$mods[$i],"-1"));
         $cliout=trim(ob_get_clean());
         if(!is_dir(APP_ROOT."/modules/".$mods[$i])){
            $GLOBALS["app_done"]=true;
            $msg=json_encode("安装模块 ".$mods[$i]." 失败：".$cliout,JSON_UNESCAPED_UNICODE);
            echo'<script>if(window.parent&&window.parent.waitFail){window.parent.waitFail('.$msg.');}else{alert('.$msg.');history.back();}</script>';
            echo'</body></html>';
            exit();
         }
      }
      echo'<span data-status="正在安装模板：'.$temp.'"></span>';
      flush();
      ob_start();
      Cli::Install(array("usualtool","install","template",$temp,"-1"));
      $cliout=trim(ob_get_clean());
      if(is_dir(APP_ROOT."/template/".$temp)){
         Data::UpdateData("cms_template",array("makefront"=>1),"tid='$temp'");
         $info=preg_replace("/TEMPFRONT=(.*)/","TEMPFRONT={$temp}",file_get_contents(UTF_ROOT."/.ut.config"));
         file_put_contents(UTF_ROOT."/.ut.config",$info);
         echo'<script>if(window.parent&&window.parent.waitDone){window.parent.waitDone("?do=finish","快速搭建应用成功!");}else{location.href="?do=finish";}</script>';
      }else{
         $msg=json_encode("安装模板失败：".$cliout,JSON_UNESCAPED_UNICODE);
         echo'<script>if(window.parent&&window.parent.waitFail){window.parent.waitFail('.$msg.');}else{alert('.$msg.');history.back();}</script>';
      }
   }else{
      echo'<script>if(window.parent&&window.parent.waitFail){window.parent.waitFail("请选择模板工程!");}else{alert("请选择模板工程!");history.back();}</script>';
   }
   echo'</body></html>';
   $GLOBALS["app_done"]=true;
   exit();
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
    <iframe id="taskFrame" name="taskFrame" style="display:none;width:0;height:0;border:0;"></iframe>
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
                    $httpcode=Inc::HttpCode($config["UTFURL"]);
                    $sysinfo=Inc::GetSystemInfo();
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
                        echo"<p>请将/app、/log、/update、/open、/assets权限设置为可写（755），并在此处 <a onclick='Refresh()' class='text-danger'>刷新页面</a></p>";
                    }
                }elseif($do=="pull"){?>
                    <div class="row">
                        <div class="col-7">
                            <h4>部署可视包</h4>
                        </div>
                        <div class="col-5 text-end">
                            <a class="btn btn-warning" href="?do=config">上一步</a>
                        </div>
                    </div>
                    <hr/>
                    <?php
                    if(is_dir(APP_ROOT."/modules/ut-power")){
                        echo"<p class='mb-3'>可视包已就位，无需重复部署。</p>";
                        echo"<p><a class=\"btn btn-info\" href=\"?do=app\">下一步</a></p>";
                    }else{
                        echo'<p class="mb-3">将自动拉取可视包并安装数据表。</p>';
                        echo'<div class="d-flex justify-content-between"><a class="btn btn-info" href="javascript:void(0)" onclick="return runTask(\'正在部署可视包（源码 + 数据），约需1分钟，请耐心等待...\',\'?do=pull&t=down\')">部署可视包</a><a class="btn btn-warning" href="?do=app">跳过，下一步</a></div>';
                    }
                }elseif($do=="app"){?>
                    <form action="?do=app&t=install" method="post" name="form" target="taskFrame">
                    <div class="row">
                        <div class="col-7">
                            <h4>快速搭建</h4>
                        </div>
                        <div class="col-5 text-end">
                            <a class="btn btn-warning" href="?do=pull">上一步</a>
                        </div>
                    </div>
                    <hr/>
                    <div class="row">
                    <?php
                    $data=array();
                    $temps=Inc::Auth("temp");
                    preg_match_all( "/\<temp\>(.*?)\<\/temp\>/s",$temps,$tempblocks);
                    foreach($tempblocks[1] as $temp){
                        preg_match_all("/\<tid\>(.*?)\<\/tid\>/",$temp,$tid);
                        preg_match_all("/\<title\>(.*?)\<\/title\>/",$temp,$title);
                        preg_match_all("/\<picurl\>(.*?)\<\/picurl\>/",$temp,$picurl);
                        preg_match_all("/\<module\>(.*?)\<\/module\>/",$temp,$module);
                        preg_match_all("/\<lang\>(.*?)\<\/lang\>/",$temp,$lang);
                        preg_match_all("/\<isfree\>(.*?)\<\/isfree\>/",$temp,$isfree);
                        $data[]=array("tid"=>$tid[1][0],"title"=>$title[1][0],"picurl"=>$picurl[1][0],"module"=>$module[1][0],"lang"=>$lang[1][0],"isfree"=>$isfree[1][0]);
                    }
                    foreach($data as $rows){
                        if(strpos($rows["isfree"],'公开使用')!==false){
                            echo"<div class='col-md-3 mb-2 text-center'><img src='".$rows["picurl"]."' style='width:100%;' class='mb-2'><br/><input type='radio' name='app' value='".$rows["tid"]."__".$rows["module"]."' class='mr-1'>".$rows["title"]."</div>";
                        }
                    }
                    ?>
                    </div>
                    <div class="d-flex justify-content-between border-top pt-3">
                        <button type="submit" class="btn btn-info mr-2" onclick="return startWait('正在安装模块与模板，请耐心等待...')">安装（预计20秒）</button>
                        <a class="btn btn-warning" href="?do=finish">跳过，下一步</a>
                    </div>
                    </form>
                <?php
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
        var pollTimer=null;
        var taskFailTicks=0;
        function startWait(msg){
            document.getElementById('waitText').innerText=msg;
            document.getElementById('waitMask').classList.add('show');
            taskFailTicks=0;
            if(pollTimer){clearInterval(pollTimer);}
            pollTimer=setInterval(pollTask,500);
            return true;
        }
        function pollTask(){
            try{
                var d=document.getElementById('taskFrame').contentDocument;
                if(!d||!d.defaultView){return;}
                var list=d.querySelectorAll('[data-status]');
                if(list.length){
                    document.getElementById('waitText').innerText=list[list.length-1].getAttribute('data-status');
                }
                if(d.readyState==='complete'&&d.defaultView.__taskChild){
                    taskFailTicks++;
                    if(taskFailTicks>=4){
                        var tail=d.body?d.body.innerText.replace(/\s+/g,' ').trim():'';
                        waitFail('安装进程异常中止！'+(tail?'信息：'+tail.slice(-160):'请查看服务器日志。'));
                    }
                }else{
                    taskFailTicks=0;
                }
            }catch(e){}
        }
        function runTask(msg,url){
            startWait(msg);
            document.getElementById('taskFrame').src=url;
            return false;
        }
        function waitDone(url,msg){
            if(pollTimer){clearInterval(pollTimer);}
            document.getElementById('waitMask').classList.remove('show');
            if(msg){alert(msg);}
            window.location.href=url;
        }
        function waitFail(msg){
            if(pollTimer){clearInterval(pollTimer);}
            document.getElementById('waitMask').classList.remove('show');
            alert(msg);
        }
    </script>
</html>