<?php
use library\UsualToolInc\UTInc;
use library\UsualToolLang\UTLang;
require'data-verify.php';
$words=UTInc::SqlCheck($_GET["word"]);
$module=UTInc::SqlCheck($_GET["module"]);
$lg=empty($_GET["lang"]) ? 1 : UTInc::SqlCheck($_GET["lang"]);
$word=explode(",",$words);
for($i=0;$i<count($word);$i++):
    if(!empty($module)):
        setcookie("Language",$lg);
        $thisword[]=array("word"=>UTLang::ModLangData($word[$i],$module));
    else:
        $thisword[]=array("word"=>UTLang::LangData($word[$i],$lg));
    endif;
endfor;
echo json_encode($thisword,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);