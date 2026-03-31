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
require dirname(__DIR__).'/'.'config.php';
use library\UsualToolInc\UTInc;
use library\UsualToolData\UTData;
use library\UsualToolDebug\UTDebug;
/**
 * 控制终端
 */
$_form_="admin";
/**
 * 模板终端
 */
$_temp_="admin";
/**
 * 获取版本号并载入应用部分设置
 */
$framework=file_get_contents(UTF_ROOT."/.version.ini");
$version=substr($frame_version,0,5);
$version_time=substr($frame_version,-6);
$app->Runin(
    array("version","version_time","update","develop","lock"),
    array($version,$version_time,$config["UPDATEURL"],$config["DEVELOP_OPEN"],$config["LOCKSCREEN"])
);
/**
 * 接收官方消息
 */
$webmsg=UTF_ROOT."/log/usualtool.log";
$msgdata=file_exists($webmsg) ? json_decode(file_get_contents($webmsg),true) : null;
if(!$msgdata || time()-strtotime($msgdata["time"])>86400):
    $msg=UTInc::Auth($config["UTCODE"],$config["UTFURL"],"message");
    file_put_contents($webmsg,json_encode(["time"=>date("Y-m-d"),"message"=>$msg],JSON_UNESCAPED_UNICODE));
else:
    $app->Runin("message", explode("|", $msgdata["message"]));
endif;
/**
 * 底层模块
 * 除开UT-FRAME公共模块
 */
$app->Runin("modules",UTData::QueryData("cms_module","","bid=3 and mid<>'".$config["DEFAULT_MOD"]."'","","")["querydata"]);
/**
 * 自定义模块
 */
$app->Runin("custmods",UTData::QueryData("cms_module","","bid<>3")["querydata"]);
/**
 * 当前运行模块及栏目
 * befoitem前端地址集，backtem后端栏目集
 */
$thismod=UTData::QueryData("cms_module","","mid='$m'","","")["querydata"][0];
$app->Runin(
    array("title","befoitem","backitem"),
    array($thismod["modname"],$thismod["befoitem"],$thismod["backitem"])
);
/**
 * 写入后端公共模板路径
 * 公共使用的模板可以放在公共模块ut-frame/skin/下
 * 使用方法：<{include "$pubtemp/head.cms"}>
 * 当前示例表示后端共用头部模板
 * 以下设置/admin表示后端，/front表示前端
 */
$app->Runin("pubtemp",PUB_TEMP."/".$_temp_);
/**
 * 写入模板工程后端公共路径
 */
$app->Runin("template",$adminwork."/skin/".$config["DEFAULT_MOD"]."/".$_temp_);
/**
 * 权限验证机制
 * 排除不需要验证的页面
 * 数组形式Contain($p,array("login","captcha"))，判断数组中是否包含页面名$p
 * 字符形式Contain("login",$p)，判断页面名$p中是否包含login
 */
if(!UTInc::Contain($p,array("login","captcha"))):
    /**
     * 加载自定义权限文件
     * 该文件亦可封装为函数让autoload自动加载
     */
    require PUB_PATH.'/'.$_form_.'/session.php';
endif;
/**
 * 路由分发控制
 */
$_map_=$modpath."/route.php";
$_file_=$p;
if(UTInc::SearchFile($_map_)):
    $_route=include $_map_;
    $_file_=$_route[$p] ?? $p;
endif;
$_file_path_=$modpath."/".$_form_."/".$_file_.".php";
/**
 * 判断文件真实性
 */
if(UTInc::SearchFile($_file_path_)):
    require_once $_file_path_;
    $_class_=UTInc::GetClassName($_file_path_);
    /**
     * 分层模式
     */
    if($_class_):
        $action=UTInc::SqlCheck($_GET["action"]) ?? "index";
        if(!preg_match('/^[a-zA-Z0-9_]+$/',$action)):
            $action="index"; 
        endif;
        $controller=new $_class_();
        /**
         * 执行动作
         */
        if(method_exists($controller,$action) || method_exists($controller,'__call')):
            $controller->$action();
        endif;
    endif;
else:
    UTDebug::Error("module",str_replace(APP_ROOT."/modules","",$modfile));
endif;
$config["DEBUG"] && UTDebug::Debug($config["DEBUG_BAR"]);