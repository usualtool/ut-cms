<?php
use library\UsualToolInc\UTInc;
use library\UsualToolData\UTData;
if(isset($_SESSION['oauth_uid'])):
    $oauth_uid=$_SESSION['oauth_uid'];
    $oauth=UTData::QueryData("oauth_users","","id='$oauth_uid'");
    if($oauth["querynum"]!=1):
        UTInc::GoUrl("?m=ut-oauth&p=login");
    endif;
else:
    UTInc::GoUrl("?m=ut-oauth&p=login");
endif;