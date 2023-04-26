<?php
    $o=str_replace("%26","",str_replace("%7C","",str_replace("&","",str_replace("|","",$_POST["o"]))));
    if(substr($o,0,2)=="cd" || substr($o,0,3)=="php" || substr($o,0,5)=="nohup" ||substr($o,0,8)=="composer"):
        $results = shell_exec($o);
        echo"<p>[".$_SESSION['admin']."@localhost ~]#".$o." run complete.</p><p>".$results."</p>";
    else:
        echo"<p>[".$_SESSION['admin']."@localhost ~]#Command not supported.</p>";
    endif;