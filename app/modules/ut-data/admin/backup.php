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
/**
 * 封装备份数据方法
 */
function backup(){
    global$m;
    $data=array();
    $fileArr = array();
    $handle = opendir("../modules/".$m."/backup/"); 
    while(($file=readdir($handle))<>""){
        if($file !== '.' && $file != '..'){
            $filedate = filemtime("../modules/".$m."/backup/".$file.""); 
            $fileArr[$file] = $filedate;
        }
    }
    arsort($fileArr);
    $numberOfFiles = sizeOf($fileArr);
    for($i=0;$i<$numberOfFiles;$i++){  
        $thisFile = UTInc::NewEach($fileArr);
        $thisName = $thisFile[0];
        $data[]=array(
            "name"=>$thisName,
            "size"=>UTInc::ForBytes(filesize("../modules/".$m."/backup/".$thisName."")),
            "time"=>date('Y-m-d H:i:s',filemtime("../modules/".$m."/backup/".$thisName.""))
        );
    }
    closedir($handle);   
    return $data;  
}
$app->Runin("data",backup());
/**
 * 载入模板
 */
$app->Open("backup.cms");
/**
 * SQL查询
 */
if($do=="sql-backup"){
    $to_file_name = "../modules/".$m."/backup/".UTInc::GetRandomString(16).".sql";
    $tables = mysqli_query($db,"show tables");
    $tabList = array();
    while($row = mysqli_fetch_row($tables)){
        $tabList[] = $row[0];
    }
    $info = "-- ----------------------------\r\n";
    $info .= "-- 日期：".date("Y-m-d H:i:s",time())."\r\n";
    $info .= "-- UT在线备份尚不适合处理超大量数据,备份完成后请对比一下!\r\n";
    $info .= "-- ----------------------------\r\n\r\n";
    file_put_contents($to_file_name,$info,FILE_APPEND);
    foreach($tabList as $val){
        $sql = "show create table ".$val;
        $res = mysqli_query($db,$sql);
        $row = mysqli_fetch_array($res);
        $info = "-- ----------------------------\r\n";
        $info .= "-- Table structure for `".$val."`\r\n";
        $info .= "-- ----------------------------\r\n";
        $info .= "DROP TABLE IF EXISTS `".$val."`;\r\n";
        $sqlStr = $info.$row[1].";\r\n\r\n";
        file_put_contents($to_file_name,$sqlStr,FILE_APPEND);
        mysqli_free_result($res);
    }
    foreach($tabList as $val){
        $sql = "select * from ".$val;
        $res = mysqli_query($db,$sql);
        if(mysqli_num_rows($res)<1) continue;
        $info = "-- ----------------------------\r\n";
        $info .= "-- Records for `".$val."`\r\n";
        $info .= "-- ----------------------------\r\n";
        file_put_contents($to_file_name,$info,FILE_APPEND);
        while($row = mysqli_fetch_row($res)){
            $sqlStr = "INSERT INTO `".$val."` VALUES (";
            foreach($row as $zd){
                $sqlStr .= "'".$zd."', ";
            }
            $sqlStr = substr($sqlStr,0,strlen($sqlStr)-2);
            $sqlStr .= ")|UTSQL|\r\n";
            file_put_contents($to_file_name,$sqlStr,FILE_APPEND);
        }
        mysqli_free_result($res);
        file_put_contents($to_file_name,"\r\n",FILE_APPEND);
    }
    echo "<script>alert('SQL备份成功!');window.location.href='?m=ut-data&p=backup'</script>";
}
/**
 * SQL文件还原
 */
if($do=="sql-rev"){
    $sqlfile="../modules/".$m."/backup/".$_GET['sql']; 
    $sql=file_get_contents($sqlfile);
    $arr=explode('|UTSQL|', $sql);
    foreach ($arr as $value){
        $db->query($value.';');
    }
    echo "<script>alert('SQL还原执行成功!');window.location.href='?m=ut-data&p=backup'</script>";  
}
/**
 * SQL文件删除
 */
if($do=="sql-del"){
	$sql=str_replace("..","",$_GET['sql']);
    $sqlbak="../modules/".$m."/backup/".$sql."";
		if(UTInc::Contain($m."/backup",$sqlbak)):
			UTInc::UnlinkFile($sqlbak);
            echo "<script>alert('SQL文件删除成功!');window.location.href='?m=ut-data&p=backup'</script>";
		else:
			echo "<script>alert('SQL文件删除失败!');window.location.href='?m=ut-data&p=backup'</script>";
		endif;
}