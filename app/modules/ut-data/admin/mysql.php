<?php
use library\UsualToolInc\UTInc;
use library\UsualToolData\UTData;
/**
 * 连接数据库
 */
$db=UTData::GetDatabase();
/**
 * 传递参数过程
 */
$do=UTInc::SqlCheck($_GET["do"]);
if($do=="mysql-sql"){
    $sql=$_POST['sql'];
    $res=UTData::RunSql($sql);
    echo 1;
}
if($do=="mysql-edit"){
    $table=$_POST["u_table"];
    $oldcols=$_POST["u_cols"];
    //$keys=$_POST["key"];
    $cols=$_POST["col"];
    $types=$_POST["type"];
    $nulls=$_POST["null"];
    $defs=$_POST["def"];
    $exts=$_POST["ext"];
    $oldcol=explode(",",$oldcols);
    $col=explode(",",$cols);
    $type=explode(",",$types);
    $null=explode(",",$nulls);
    $def=explode(",",$defs);
    $ext=explode(",",$exts);
    for($i=0;$i<count($oldcol);$i++){
        if(strtoupper($null[$i])=="YES"){
            $thenull="NULL";
        }else{
            $thenull="NOT NULL";
        }
        if(!empty($def[$i])){
            $thedef="DEFAULT '".$def[$i]."'";
        }else{
            $thedef="";
        }
        if(!empty($ext[$i])){
            $theext=strtoupper($ext[$i]);
        }else{
            $theext="";
        }
        $sql="ALTER TABLE `".$table."` CHANGE `".$oldcol[$i]."` `".$col[$i]."` ".strtoupper($type[$i])." ".$thenull." ".$thedef." ".$theext.";";
        UTData::RunSql($sql);
    }
    echo 1;    
}
if($do=="mysql-add"){
    $table=$_POST["u_table"];
    $cols=$_POST["col"];
    $types=$_POST["type"];
    $nulls=$_POST["null"];
    $defs=$_POST["def"];
    $col=explode(",",$cols);
    $type=explode(",",$types);
    $null=explode(",",$nulls);
    $def=explode(",",$defs);
    for($i=0;$i<count($col);$i++){
        if(strtoupper($null[$i])=="YES"){
            $thenull="NULL";
        }else{
            $thenull="NOT NULL";
        }
        if(!empty($def[$i])){
            $thedef="DEFAULT '".$def[$i]."'";
        }else{
            $thedef="";
        }
        $sql="ALTER TABLE `".$table."` ADD `".$col[$i]."` ".strtoupper($type[$i])." ".$thenull." ".$thedef.";";
        UTData::RunSql($sql);
    }
    echo 1;
}