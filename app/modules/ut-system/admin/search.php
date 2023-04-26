<?php
use library\UsualToolInc\UTInc;
use library\UsualToolData\UTData;
$data=UTData::QueryData("cms_search_set","","","","")["querydata"];
$app->Runin("data",$data);
$app->Open("search.cms");
if($_GET["do"]=="update"){
    $ids=implode("-UT-",$_POST["sid"]);
    $dbs=implode("-UT-",$_POST["sdb"]);
    $fields=implode("-UT-",$_POST["sfield"]);
    $wheres=implode("-UT-",$_POST["swhere"]);
    $pages=implode("-UT-",$_POST["spage"]);
    $idx=explode("-UT-",$ids);
    $dbx=explode("-UT-",$dbs);
    $fieldx=explode("-UT-",$fields);
    $wherex=explode("-UT-",$wheres);
    $pagex=explode("-UT-",$pages);
    for($s=0;$s<=count($dbx);$s++){
        if($idx[$s]=="x"){
            UTData::InsertData("cms_search_set",array(
                "dbs"=>$dbx[$s],
                "fields"=>$fieldx[$s],
                "wheres"=>$wherex[$s],
                "pages"=>$pagex[$s]));
        }else{
            UTData::UpdateData("cms_search_set",array(
                "dbs"=>$dbx[$s],
                "fields"=>$fieldx[$s],
                "wheres"=>$wherex[$s],
                "pages"=>$pagex[$s]),"id='".$idx[$s]."'");
        }
        UTData::RunSql($sql);
        }
    UTInc::GoUrl("?m=ut-system&p=search","设置成功!");
}
if($_GET["do"]=="del"){
    $id=UTInc::SqlCheck($_GET["id"]);
    if(UTData::DelData("cms_search_set","id='$id'")){
        UTInc::GoUrl("?m=ut-system&p=search","删除成功!");
    }else{
        UTInc::GoUrl("?m=ut-system&p=search","删除失败!");
    }
}