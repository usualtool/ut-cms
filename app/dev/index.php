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
require dirname(dirname(__FILE__)).'/'.'config.php';
use library\UsualToolInc\UTInc;
use library\UsualToolData\UTData;
/**
 * 获取版本号并载入应用部分设置
 */
$ver=substr(file_get_contents(UTF_ROOT."/UTVer.ini"),-6);
$app->Runin(
    array("ver","update","develop","lock"),
    array($ver,$config["UPDATEURL"],$config["DEVELOP"],$config["LOCKSCREEN"])
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
 * 写入底层模块
 * 除开UT-FRAME公共模块
 */
$app->Runin("modules",UTData::QueryData("cms_module","","bid=3 and mid<>'".$config["DEFAULT_MOD"]."'","","")["querydata"]);
/**
 * 写入自定义模块
 */
$app->Runin("custmods",UTData::QueryData("cms_module","","bid<>3")["querydata"]);
/**
 * 写入当前运行模块名称及栏目
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
$app->Runin("pubtemp",PUB_TEMP."/admin");
/**
 * 写入模板工程后端公共路径
 */
$app->Runin("template",$adminwork."/skin/".$config["DEFAULT_MOD"]."/admin");
/**
 * 权限验证机制
 * 排除不需要验证的页面
 * 数组形式Contain($p,array("login","captcha"))，判断数组中是否包含页面名$p
 * 字符形式Contain("login",$p)，判断页面名$p中是否包含login
 */
if(!UTInc::Contain($p,array("login","captcha"))){
    /**
     * 加载自定义权限文件
     * 该文件亦可封装为函数让autoload自动加载
     */
    require PUB_PATH.'/admin/session.php';
}
/**
 * 拼接当前文件
 */
$modfile=$modpath."/admin/".$p.".php";
/**
 * 判断文件真实性
 */
if(library\UsualToolInc\UTInc::SearchFile($modfile)){
    /**
     * 引用后端模板
     */
    require_once $modfile;
}else{
    /**
     * 配置公共错误提示
     */
    require_once PUB_PATH.'/front/error.php';
    exit();
}
if($config["DEBUG"]){
    library\UsualToolDebug\UTDebug::Debug($config["DEBUG_BAR"]);
}