<?php
use library\UsualToolInc\UTInc;
use library\UsualToolData\UTData;
$do=UTInc::SqlCheck($_GET["do"]);
$data=UTData::QueryData("cms_template","","","","")["querydata"];
/**
 * 载入模板文件管理器
 */
$temp=$app->GetTempFile();
$front_temp=$temp["front"];
$admin_temp=$temp["admin"];
$app->Runin(array("do","tempdata","front","admin"),array($do,$data,$front_temp,$admin_temp));
/**
 * 载入模板
 */
$app->Open("index.cms");
if($do=="front" || $do=="admin"){
    $tempid=UTInc::SqlCheck($_GET["tempid"]);
    $info = file_get_contents(UTF_ROOT."/.ut.config");
    if($do=="front"){
        if(UTData::UpdateData("cms_template",array("makefront"=>0),"id>0")){
            UTData::UpdateData("cms_template",array("makefront"=>1),"tid='$tempid'");
            $info = preg_replace("/TEMPFRONT=(.*)/","TEMPFRONT={$tempid}",$info);
            file_put_contents(UTF_ROOT."/.ut.config",$info);
            if(is_dir(APP_ROOT."/template/".$tempid."/move")):
                UTInc::MoveDir(APP_ROOT."/template/".$tempid."/move",UTF_ROOT);
            endif;
            UTInc::GoUrl("?m=ut-template&p=index&do=template","设置成功!");
        }else{
            UTInc::GoUrl("-1","设置失败!");
        }
    }else{
        if(UTData::UpdateData("cms_template",array("makeadmin"=>0),"id>0")){
            UTData::UpdateData("cms_template",array("makeadmin"=>1),"tid='$tempid'");
            $info = preg_replace("/TEMPADMIN=(.*)/","TEMPADMIN={$tempid}",$info);
            file_put_contents(UTF_ROOT."/.ut.config",$info);
            if(is_dir(APP_ROOT."/template/".$tempid."/move")):
                UTInc::MoveDir(APP_ROOT."/template/".$tempid."/move",UTF_ROOT);
            endif;
            UTInc::GoUrl("?m=ut-template&p=index&do=template","设置成功!");
        }else{
            UTInc::GoUrl("-1","设置失败!");
        }
    }
}
if($do=="unfront" || $do=="unadmin"){
    $tempid=UTInc::SqlCheck($_GET["tempid"]);
    $info = file_get_contents(UTF_ROOT."/.ut.config");
    if($do=="unfront"){
        if(UTData::UpdateData("cms_template",array("makefront"=>0),"tid='$tempid'")){
            $info = preg_replace("/TEMPFRONT=(.*)/","TEMPFRONT=0",$info);
            file_put_contents(UTF_ROOT."/.ut.config",$info);
            UTInc::GoUrl("?m=ut-template&p=index&do=template","取消成功!");
        }else{
            UTInc::GoUrl("-1","取消失败!");
        }
    }else{
        if(UTData::UpdateData("cms_template",array("makeadmin"=>0),"tid='$tempid'")){
            $info = preg_replace("/TEMPADMIN=(.*)/","TEMPADMIN=0",$info);
            file_put_contents(UTF_ROOT."/.ut.config",$info);
            UTInc::GoUrl("?m=ut-template&p=index&do=template","取消成功!");
        }else{
            UTInc::GoUrl("-1","取消失败!");
        }
    }
}