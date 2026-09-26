<?php
use usualtool\Lib\Inc;
use usualtool\Lib\Lang;
require'data-verify.php';
$words=Inc::SqlCheck($_GET["word"]);
$module=Inc::SqlCheck($_GET["module"]);
$lg=empty($_GET["lang"]) ? 1 : Inc::SqlCheck($_GET["lang"]);
$word=explode(",",$words);
for($i=0;$i<count($word);$i++):
    if(!empty($module)):
        setcookie("Language",$lg);
        $thisword[]=array("word"=>Lang::ModLangData($word[$i],$module));
    else:
        $thisword[]=array("word"=>Lang::LangData($word[$i],$lg));
    endif;
endfor;
echo json_encode($thisword,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);