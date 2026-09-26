<?php
use usualtool\Lib\Inc;
use usualtool\Lib\Data;
if(isset($_SESSION['oauth_uid'])):
    $oauth_uid=$_SESSION['oauth_uid'];
    $oauth=Data::QueryData("oauth_users","","id='$oauth_uid'");
    if($oauth["querynum"]!=1):
        Inc::GoUrl("?m=ut-oauth&p=login");
    endif;
else:
    Inc::GoUrl("?m=ut-oauth&p=login");
endif;