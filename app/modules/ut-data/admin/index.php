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
$data=array();
$result = $db->query("SHOW TABLES");
while($arr = $result->fetch_assoc()){
    foreach($arr as $key=>$val){
        $rows='';
        $type='';
        $null='';
        $key='';
        $def='';
        $extra='';
        $res=$db->query("desc ".$val);
        while($row=mysqli_fetch_assoc($res)){ 
            $rows.=$row['Field'].",";
            $type.=$row['Type'].",";
            $null.=$row['Null'].",";
            $key.=$row['Key'].",";
            $def.=$row['Default'].",";
            $extra.=$row['Extra'].",";
        }
        $cols=rtrim($rows,",");
        $types=rtrim($type,",");
        /**$types=str_replace(")","-T-",str_replace("(","-U-",rtrim($type,",")));*/
        $nulls=rtrim($null,",");
        $keys=rtrim($key,",");
        $defs=rtrim($def,",");
        $extras=rtrim($extra,",");
        $datanum=mysqli_num_rows($db->query("select * from `".$val."`"));
        $data[]=array(
            "name"=>$val,
            "field"=>$cols,
            "type"=>$types,
            "null"=>$nulls,
            "key"=>$keys,
            "def"=>$defs,
            "extra"=>$extras,
            "num"=>$datanum);
    }
}
$app->Runin("data",$data);
/**
 * 载入模板
 */
$app->Open("index.cms");