<?php
use usualtool\Lib\Inc;
$parsedConfig = [];
$currentSection = 'default';
$lines = explode("\n", file_get_contents(UTF_ROOT."/.ut.config"));
foreach($lines as $line){
    $line = trim($line);
    if (empty($line)) continue;
    if (preg_match('/\/\*(.+)\*\//', $line, $matches)) {
        $currentSection = trim($matches[1]);
        if (!isset($parsedConfig[$currentSection])) {
            $parsedConfig[$currentSection] = [];
        }
        continue;
    }
    if (strpos($line, '=') !== false) {
        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        if (!isset($parsedConfig[$currentSection])) {
            $parsedConfig[$currentSection] = [];
        }
        $parsedConfig[$currentSection][$key] = $value;
    }
}
$do=$_GET["do"];
/**
 * 加载全局配置信息
 */
$app->Runin("config",$parsedConfig);
/**
 * 载入模板
 */
$app->Open("index.cms");
if($do=="setup"){
    $info = file_get_contents(UTF_ROOT."/.ut.config"); 
    foreach($_POST as $k=>$v){ 
        $info = preg_replace("/{$k}=(.*)/","{$k}={$v}",$info); 
    }
    file_put_contents(UTF_ROOT."/.ut.config",$info);
    Inc::GoUrl("?m=ut-system","保存配置成功!");
}