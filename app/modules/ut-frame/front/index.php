<?php
namespace Controller\Ut_frame\Admin;
use usualtool\Lib\Inc;
class Index{
    public function __call($method,$args){
        $this->index();
    }
    public function index(){
        global $app;
        $setup=Inc::InstallDev() ? 1 : 0;
        if($setup){
            $app->Runin(array("setup","title"),array($setup,"Hello!UT"));
            $app->Open("index.cms");
        }else{
            Inc::GoUrl("/install-dev/","");
        }
    }
}