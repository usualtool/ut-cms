<?php
use usualtool\Oauth\Oauth;
$oauth = new Oauth();
$path = $_GET['route'] ?? '';
switch ($path) {
    case 'authorize':
        $oauth->Authorize();
        break;
    case 'token':
        $oauth->Token();
        break;
    default:
        http_response_code(404);
        echo "ROUTE ERROR";
}