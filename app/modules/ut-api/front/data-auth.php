<?php
use library\UsualToolInc\UTInc;
use library\UsualToolData\UTData;
use library\UsualToolWechat\UTWechat;
use library\UsualToolAliopen\UTAliopen;
if(UTData::ModTable("cms_routine")):
    $set=UTData::QueryData("cms_routine","","","","1")["querydata"][0];
    $do=UTInc::SqlCheck($_GET["do"]);
    $auth=UTInc::SqlCheck($_GET["auth"]);
    $code=UTInc::SqlCheck($_GET["code"]);
    if($config["UTCODE"]==$auth):
        if($do=="wechat"):
            $result=file_get_contents("https://api.weixin.qq.com/sns/jscode2session?appid=".$set["wxrteid"]."&secret=".$set["wxrtesecret"]."&js_code=".$code."&grant_type=authorization_code");
        elseif($do=="wechat-crypt"):
            $encrypteddata=$_POST["encrypteddata"];
            $iv=$_POST["iv"];
            $openid=UTInc::SqlCheck($_POST["openid"]);
            $thirdkey=$_POST["thirdkey"];
            $wxin=new UTWechat($set["wxappline"],$set["wxrteid"],$set["wxrtesecret"]);
            $errcode=$wxin->DecryptData($thirdkey,$encrypteddata,$iv,$data);
            if($errcode==0):
                $result=$data;
            else:
                $result=$errcode;
            endif;
        elseif($do=="alipay"):
            $ali=new UTAliopen($set["alrteid"],$set["alapppubkey"],$set["alpaypubkey"]);
            $result=$ali->ApiRequest("alipay.system.oauth.token",array("grant_type"=>"authorization_code","code"=>$code));
        else:
            $appid="";
            $appkey="";
            $result="0";
        endif;
        echo$result;
    else:
        echo'[{"error":1}]';
    endif;
else:
    echo'[{"error":1}]';
endif;