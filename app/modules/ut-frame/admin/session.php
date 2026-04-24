<?php
use library\UsualToolInc\UTInc;
use library\UsualToolData\UTData;
if(isset($_SESSION['admin'])&&isset($_SESSION['admin_id'])&&!empty($_SESSION['admin'])&&!empty($_SESSION['admin_id'])):
		/**
		 * 获取版本号并载入应用部分设置
		 */
		$framework=file_get_contents(UTF_ROOT."/.version.ini");
		$version=substr($frame_version,0,5);
		$version_time=substr($frame_version,-6);
		$app->Runin(
				array("version","version_time","update","develop","lock","editor"),
				array($version,$version_time,$config["UPDATEURL"],$config["DEVELOP_OPEN"],$config["LOCKSCREEN"],$config["EDITOR"])
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