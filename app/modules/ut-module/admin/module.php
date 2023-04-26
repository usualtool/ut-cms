<?php
use library\UsualToolInc\UTInc;
use library\UsualToolData\UTData;
$do=$_GET["do"];
/**
 * 载入私有模块
 */
$app->Runin("private_mod",UTInc::GetMod());
/**
 * 载入第三方模块
 */
$app->Runin("pubilc_mod",UTInc::GetMod(1));
/**
 * 载入订购模块
 */
$app->Runin("buy_mod",UTInc::GetMod(2));
/**
 * 载入模板
 */
$app->Open("module.cms");
if($do=="install"){
    $t=$_GET["t"];
    $mid=str_replace(".","",UTInc::SqlCheck($_GET["mid"]));
    if($t!="s"):
        if($t=="g"):
            $down=UTInc::Auth($config["UTCODE"],$config["UTFURL"],"module-".$mid);
        elseif($t=="b"):  
            $down=UTInc::Auth($config["UTCODE"],$config["UTFURL"],"moduleorder-".$mid);
        endif;
        $downurl=UTInc::StrSubstr("<downurl>","</downurl>",$down);
        $filename=basename($downurl);
        $res=UTInc::SaveFile($downurl,APP_ROOT."/modules",$filename,1);
        if(!empty($res)):
            UTInc::Auth($config["UTCODE"],$config["UTFURL"],"moduledel-".str_replace(".zip","",$filename)."");
            $zip=new ZipArchive;
            if($zip->open(APP_ROOT."/modules/".$filename)===TRUE): 
                $zip->extractTo(APP_ROOT."/modules/");
                $zip->close();
                unlink(APP_ROOT."/modules/".$filename);
            else:
                echo "<script>alert('modules目录775权限不足!');window.location.href='?m=ut-module&p=module'</script>";
               exit();
            endif;
        else:
            echo "<script>alert('安装权限不足!');window.location.href='?m=ut-module&p=module'</script>";
            exit();
        endif;
    endif;
    $modconfig=APP_ROOT."/modules/".$mid."/usualtool.config";
    $mods=file_get_contents($modconfig);
    $modname=UTInc::StrSubstr("<modname>","</modname>",$mods);
    $ordernum=UTInc::StrSubstr("<ordernum>","</ordernum>",$mods);
    $modurl=UTInc::StrSubstr("<modurl>","</modurl>",$mods);
    $befoitem=UTInc::StrSubstr("<befoitem>","</befoitem>",$mods);
    $backitem=UTInc::StrSubstr("<backitem>","</backitem>",$mods);
    $itemid=UTInc::StrSubstr("<itemid>","</itemid>",$mods);
    $installsql=UTInc::StrSubstr("<installsql><![CDATA[","]]></installsql>",$mods);
    $role=UTData::QueryData("cms_admin_role","","","","")["querydata"];
    foreach($role as $rows):
        $role_range=UTData::QueryData("cms_admin_role","","id='".$rows["id"]."'","","")["querydata"][0]["module"];
        $new_range=$role_range.",".$mid;
        UTData::UpdateData("cms_admin_role",array("module"=>$new_range),"id='".$rows["id"]."'");
    endforeach;
    if(UTData::QueryData("cms_module","","mid='$mid'","","1")["querynum"]>0):
        UTData::UpdateData("cms_module",array(
            "bid"=>$itemid,
            "modname"=>$modname,
            "modurl"=>$modurl,
            "befoitem"=>$befoitem,
            "backitem"=>$backitem),"mid='$mid'");
    else:
        UTData::InsertData("cms_module",array(
            "bid"=>$itemid,
            "mid"=>$mid,
            "modname"=>$modname,
            "modurl"=>$modurl,
            "isopen"=>1,
            "look"=>1,
            "ordernum"=>$ordernum,
            "befoitem"=>$befoitem,
            "backitem"=>$backitem));
    endif;
    if($installsql=='0'):
        echo"<script>alert('成功安装模块!');window.location.href='?m=ut-module&p=module'</script>";
    else:
        if(UTData::RunSql($installsql)):
            echo"<script>alert('成功安装模块!');window.location.href='?m=ut-module&p=module'</script>";
        else:
            echo"<script>alert('模块安装失败!');window.location.href='?m=ut-module&p=module'</script>";
        endif;   
    endif;
}