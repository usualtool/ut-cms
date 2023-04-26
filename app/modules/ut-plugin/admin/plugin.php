<?php
use library\UsualToolInc\UTInc;
use library\UsualToolData\UTData;
$do=$_GET["do"];
/**
 * 载入私有插件
 */
$app->Runin("private_plugin",UTInc::GetPlugin());
/**
 * 载入第三方插件
 */
$app->Runin("pubilc_plugin",UTInc::GetPlugin(1));
/**
 * 载入订购插件
 */
$app->Runin("buy_plugin",UTInc::GetPlugin(2));
/**
 * 载入模板
 */
$app->Open("plugin.cms");
if($do=="install"){
    $t=$_GET["t"];
    $pid=str_replace(".","",UTInc::SqlCheck($_GET["pid"]));
    if($t!="s"):
        if($t=="g"):
            $down=UTInc::Auth($config["UTCODE"],$config["UTFURL"],"plugin-".$pid);
        elseif($t=="b"):  
            $down=UTInc::Auth($config["UTCODE"],$config["UTFURL"],"pluginorder-".$pid);
        endif;
        $downurl=UTInc::StrSubstr("<downurl>","</downurl>",$down);
        $filename=basename($downurl);
        $res=UTInc::SaveFile($downurl,APP_ROOT."/plugins",$filename,1);
        if(!empty($res)):
            UTInc::Auth($config["UTCODE"],$config["UTFURL"],"plugindel-".str_replace(".zip","",$filename)."");
            $zip=new ZipArchive;
            if($zip->open(APP_ROOT."/plugins/".$filename)===TRUE): 
                $zip->extractTo(APP_ROOT."/plugins/");
                $zip->close();
                unlink(APP_ROOT."/plugins/".$filename);
            else:
               echo "<script>alert('plugins目录775权限不足!');window.location.href='?m=ut-plugin&p=plugin'</script>";
               exit();
            endif;
        else:
            echo "<script>alert('安装权限不足!$downurl');window.location.href='?m=ut-plugin&p=plugin'</script>";
            exit();
        endif;
    endif;    
    $pconfig=APP_ROOT."/plugins/".$pid."/usualtool.config";
    $plugins=file_get_contents($pconfig);
    $type=UTInc::StrSubstr("<type>","</type>",$plugins);
    $auther=UTInc::StrSubstr("<auther>","</auther>",$plugins);
    $title=UTInc::StrSubstr("<title>","</title>",$plugins);
    $ver=UTInc::StrSubstr("<ver>","</ver>",$plugins);
    $description=UTInc::StrSubstr("<description>","</description>",$plugins);
    $installsql=UTInc::StrSubstr("<installsql><![CDATA[","]]></installsql>",$plugins);
    if(UTData::QueryData("cms_plugin","","pid='$pid'","","1")["querynum"]>0):
        UTData::UpdateData("cms_plugin",array(
            "type"=>$type,
            "auther"=>$auther,
            "title"=>$title,
            "ver"=>$ver,
            "description"=>$description),"pid='$pid'");
    else:
        UTData::InsertData("cms_plugin",array(
            "pid"=>$pid,
            "type"=>$type,
            "auther"=>$auther,
            "title"=>$title,
            "ver"=>$ver,
            "description"=>$description));
    endif;
    if($installsql=='0'):
        echo"<script>alert('成功安装插件!');window.location.href='?m=ut-plugin&p=plugin'</script>";
    else:
        if(UTData::RunSql($installsql)):
            echo"<script>alert('成功安装插件!');window.location.href='?m=ut-plugin&p=plugin'</script>";
        else:
            echo"<script>alert('插件安装失败!');window.location.href='?m=ut-plugin&p=plugin'</script>";
        endif;   
    endif;
}