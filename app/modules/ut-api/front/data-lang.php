<?php
use library\UsualToolInc\UTInc;
use library\UsualToolLang\UTLang;
$auth=UTInc::SqlCheck($_GET["auth"]);
$words=UTInc::SqlCheck($_GET["word"]);
$module=UTInc::SqlCheck($_GET["module"]);
$lang=UTInc::SqlCheck($_GET["lang"]);
$lg=empty($lang) ? "zh" : $lang;
$word=explode(",",$words);
if($config["UTCODE"]==$auth){
    for($i=0;$i<count($word);$i++){
        if(!empty($module)){
            setcookie("Language",$lg);
            $thisword[]=array("word"=>UTLang::ModLangData($word[$i],$module));
        }else{
            $thisword[]=array("word"=>UTLang::LangData($word[$i],$lg));
        }
    }
    echo json_encode($thisword,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
}else{
    echo'[{"error":1}]';
}