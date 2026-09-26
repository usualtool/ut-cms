<?php
use usualtool\Lib\Code;
$captcha = new Code();
$captcha->CreateImage();
$_SESSION['authcode']=$captcha->GetCode();