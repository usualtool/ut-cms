<?php
use usualtool\Lib\Inc;
use usualtool\Lib\Sockets;
$config=Inc::GetConfig();
$socket=new Sockets($config["SOCKETS_HOST"],$config["SOCKETS_PORT"]);