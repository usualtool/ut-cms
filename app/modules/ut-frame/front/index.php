<?php
use usualtool\Lib\Inc;
$setup=Inc::InstallDev() ? 1 : 0;
if($setup):
    $app->Runin(array("setup","title"),array($setup,"Hello!UT"));
    $app->Open("index.cms");
else:
    Inc::GoUrl("/install-dev/","");
endif;