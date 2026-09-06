<?php
//需启用usualtool/ut-ai扩展
use usualtool\Ai\Ai;
require'data-verify.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    $messages = $data['messages'] ?? [];
    $model = $data['model'] ?? 'GLM-4.7-FlashX';
    $stream = $data['stream'] ?? false;
    $sessionid = $data['sessionid'] ?? '';
    $ai = new Ai();
    echo $ai->Chat($messages, $model, $stream, $sessionid);
}