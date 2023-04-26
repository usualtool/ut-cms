<?php
use library\UsualToolInc\UTInc;
use library\UsualToolData\UTData;
$do=UTInc::SqlCheck($_GET["do"]);
$auth=UTInc::SqlCheck($_GET["auth"]);
$table=UTInc::SqlCheck($_GET["table"]);
$where=str_replace("%27","'",UTInc::SqlCheck(str_replace("'","%27",str_replace("]","",str_replace("[","",$_GET["where"])))));
if($config["UTCODE"]==$auth):
    if(UTData::ModTable($table)):
		if(empty($do) || $do=="add"):
			if(UTData::InsertData($table,$_POST)):
				echo'[{"error":0}]';
			else:
				echo'[{"error":1}]';
			endif;
		elseif($do=="mon"):
			if(UTData::UpdateData($table,$_POST,$where)):
				echo'[{"error":0}]';
			else:
				echo'[{"error":1}]';
			endif;
		elseif($do=="del"):
			if(UTData::DelData($table,$where)):
				echo'[{"error":0}]';
			else:
				echo'[{"error":1}]';
			endif;
		endif;		
    else:
		echo'[{"error":1}]';
    endif;
else:
    echo'[{"error":1}]';
endif;