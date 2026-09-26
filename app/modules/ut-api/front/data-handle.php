<?php
use usualtool\Lib\Inc;
use usualtool\Lib\Data;
require'data-verify.php';
$table=Inc::SqlCheck($_POST["table"]);
$where=$_POST["where"];
$action=Inc::SqlCheck($_POST["action"]);
$data=array_diff_key($_POST,array("table"=>$table,"where"=>$where,"action"=>$action));
if(Data::ModTable($table)):
	if(empty($action) || $action=="add"):
		if(Data::InsertData($table,$data)):
			echo'[{"error":0}]';
		else:
			echo'[{"error":1}]';
		endif;
	elseif($action=="mon"):
		if(Data::UpdateData($table,$data,$where)):
			echo'[{"error":0}]';
		else:
			echo'[{"error":1}]';
		endif;
	elseif($action=="del"):
		if(Data::DelData($table,$where)):
			echo'[{"error":0}]';
		else:
			echo'[{"error":1}]';
		endif;
	endif;		
else:
	echo'[{"error":1}]';
endif;