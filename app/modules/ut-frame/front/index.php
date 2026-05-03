<?php
use library\UsualToolInc\UTInc;
$setup=UTInc::InstallDev() ? 1 : 0;
if($setup):
    $app->Runin(array("setup","title"),array($setup,"Hello!UT"));
    $app->Open("index.cms");
else:
    UTInc::GoUrl("/install-dev/","");
endif;