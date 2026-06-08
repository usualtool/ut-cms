<?php
use library\UsualToolInc\UTInc;
use library\UsualToolData\UTData;
function GetLog($path){
    if(!file_exists($path) || !is_readable($path)){
        return []; 
    }
    $parsedLogs = [];
    $pattern = '/^\[(?P<time>\d{2}-\w{3}-\d{4} \d{2}:\d{2}:\d{2})(?:\s+[^\]]+)?\]\s+(?P<content>.*)$/';
    $handle = fopen($path, 'r');
    if($handle){
        while(($line = fgets($handle)) !== false){
            $line = rtrim($line, "\r\n"); 
            if($line==='') continue;
            if(preg_match($pattern, $line, $matches)){
                list($fullLine, $timeStr, $contentStr) = $matches;
                $parsedLogs[] = [
                    'time'    => $timeStr,
                    'content' => $contentStr
                ];
            }
        }
        fclose($handle);
    }
    return $parsedLogs;
}
/**
 * 错误日志
 */
$app->Runin("log_error",GetLog(UTF_ROOT."/log/php_errors.log"));
/**
 * 载入模板
 */
$app->Open("syslog.cms");