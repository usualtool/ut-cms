<?php
use library\UsualToolInc\UTInc;
use library\UsualToolData\UTData;
require'data-verify.php';
$table=UTInc::SqlCheck($_POST["table"]);
$where=$_POST["where"];
$action=UTInc::SqlCheck($_POST["action"]);
$data=array_diff_key($_POST,array("table"=>$table,"where"=>$where,"action"=>$action));
if(UTData::ModTable($table)):
	if(empty($action) || $action=="add"):
		if(UTData::InsertData($table,$data)):
			echo'[{"error":0}]';
		else:
			echo'[{"error":1}]';
		endif;
	elseif($action=="mon"):
		if(UTData::UpdateData($table,$data,$where)):
			echo'[{"error":0}]';
		else:
			echo'[{"error":1}]';
		endif;
	elseif($action=="del"):
		if(UTData::DelData($table,$where)):
			echo'[{"error":0}]';
		else:
			echo'[{"error":1}]';
		endif;
	endif;		
else:
	echo'[{"error":1}]';
endif;