<?php
use library\UsualToolInc\UTInc;
use library\UsualToolData\UTData;
/**
 * 官方更新
 */
$app->Runin("updatelog",explode("|",UTInc::Auth($config["UTCODE"],$config["UTFURL"],"update")));
/**
 * AD
 */
$app->Runin("ad",explode("^",explode("|",UTInc::Auth($config["UTCODE"],$config["UTFURL"],"upapi"))[1]));
/**
 * 在线更新
 */
$t=UTInc::sqlcheck($_GET["t"]);
$i=UTInc::sqlcheck(str_replace("..","",$_GET["i"]));
if($t=="update"):
    $url=$config["UPDATEURL"]."/".$i.".zip";
    $save_dir=UTF_ROOT."/update";  
    $filename=basename($url); 
    $res=UTInc::SaveFile($url,$save_dir,$filename,1);
    if(!empty($res)):
        $zip=new ZipArchive;
        if($zip->open(UTF_ROOT."/update/".$i.".zip")===TRUE): 
            $zip->extractTo(UTF_ROOT."/update/");
            $zip->close();
            if(file_exists(UTF_ROOT."/update/".$i."/usualtool.config")):
                $up=file_get_contents(UTF_ROOT."/update/".$i."/usualtool.config");
                $thesql=UTInc::StrSubstr("<sql><![CDATA[","]]></sql>",$up);
                $resx=UTData::RunSql($thesql); 
            else:
                $resx=1;
            endif;
            if($resx):
                $olddir=UTF_ROOT."/update/".$i."/";
                UTInc::movedir($olddir,UTF_ROOT);
                UTData::insertData("cms_update",array("updateid"=>$i,"updatetime"=>date('Y-m-d H:i:s',time())));    
                UTInc::deldir(UTF_ROOT."/update/".$i."/");
                unlink(UTF_ROOT."/update/".$i.".zip");
                echo "<script>alert('Online update complete!');window.location.href='?p=update'</script>";
            endif;
        else:
            echo "<script>alert('Decompression failed!');window.location.href='?p=update'</script>";
        endif;
    else:
        echo "<script>alert('Download failed!');window.location.href='?p=update'</script>";
    endif;
endif;
/**
 * 载入模板
 */
$app->Open("update.cms");