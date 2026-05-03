<?php
use library\UsualToolCode\UTCode;
$captcha = new UTCode();
$captcha->CreateImage();
$_SESSION['authcode']=$captcha->GetCode();