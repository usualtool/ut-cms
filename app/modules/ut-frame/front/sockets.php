<?php
use library\UsualToolInc\UTInc;
use library\UsualToolSockets\UTSockets;
$config=UTInc::GetConfig();
$socket=new UTSockets($config["SOCKETS_HOST"],$config["SOCKETS_PORT"]);