<?php
use usualtool\Lib\Inc;
use usualtool\Lib\Data;
require 'data-verify.php';
date_default_timezone_set('Asia/Shanghai');
$debug = false;
$framework = trim((string)@file_get_contents(UTF_ROOT . "/.version.ini"));
$sys = Inc::GetSystemInfo();
$diskPath   = !empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : UTF_ROOT;
$diskTotalN = (float)@disk_total_space($diskPath);
$diskFreeN  = (float)@disk_free_space($diskPath);
$diskTotal = !empty($sys['DISK_TOTAL']) ? $sys['DISK_TOTAL']
           : ($diskTotalN > 0 ? round($diskTotalN / 1073741824, 2) . ' GB' : 'N/A');
$diskFree  = !empty($sys['DISK_FREE']) ? $sys['DISK_FREE']
           : ($diskTotalN > 0 ? round($diskFreeN / 1073741824, 2) . ' GB' : 'N/A');
$diskUsed  = !empty($sys['DISK_USED']) ? $sys['DISK_USED']
           : ($diskTotalN > 0 ? round((1 - $diskFreeN / $diskTotalN) * 100, 1) . '%' : 'N/A');
$logFile  = UTF_ROOT . "/log/php_errors.log";
$today    = date('d-M-Y');
$maxItems = 200;
$diag = array(
    'path'     => $logFile,
    'exists'   => file_exists($logFile),
    'readable' => is_readable($logFile),
    'size'     => file_exists($logFile) ? filesize($logFile) : -1,
    'mtime'    => file_exists($logFile) ? date('Y-m-d H:i:s', filemtime($logFile)) : '',
);
$todayItems = array();
$firstLines = array();
$lineCount  = 0;
if (!function_exists('ParseErr')) {
    function ParseErr($rest){
        $level = 'UNKNOWN';
        $msg   = $rest;
        if (preg_match('/^PHP\s+(Fatal error|Parse error|Recoverable fatal error|Warning|Notice|Deprecated|Strict Standards|Exception|Error)\s*:\s*(.*)$/i', $rest, $lv)) {
            $level = strtoupper($lv[1]);
            $msg   = trim($lv[2]);
        }
        return array($level, mb_substr($msg, 0, 300));
    }
}
if (!function_exists('Settle')) {
    function Settle($cur, $today, $maxItems, &$todayItems){
        if ($cur['date'] === $today && count($todayItems) < $maxItems) {
            $todayItems[] = array(
                'time'    => $cur['time'],
                'level'   => $cur['level'],
                'message' => $cur['message'],
                'stack'   => $cur['stack'] !== '' ? mb_substr($cur['stack'], 0, 1000) : '',
            );
        }
    }
}
if ($diag['readable']) {
    $fh = @fopen($logFile, 'rb');
    if ($fh) {
        $cur = null;
        while (($raw = fgets($fh)) !== false) {
            $lineCount++;
            $line = rtrim($raw, "\r\n");
            if ($debug && count($firstLines) < 3) {
                $firstLines[] = mb_substr($line, 0, 200);
            }
            if (preg_match('/\[(\d{2}-[A-Za-z]{3}-\d{4})\s+(\d{2}:\d{2}:\d{2})\s*([^\]]*)\]/', $line, $m, PREG_OFFSET_CAPTURE)) {
                $end  = $m[0][1] + strlen($m[0][0]);
                $rest = trim(substr($line, $end));
                if ($cur !== null) {
                    Settle($cur, $today, $maxItems, $todayItems);
                }
                $p = ParseErr($rest);
                $cur = array(
                    'date'    => $m[1][0],
                    'time'    => $m[2][0] . ' ' . $m[3][0],
                    'level'   => $p[0],
                    'message' => $p[1],
                    'stack'   => '',
                );
            } else {
                if ($cur !== null && $line !== '' && strlen($cur['stack']) < 2000) {
                    $cur['stack'] .= $line . "\n";
                }
            }
        }
        if ($cur !== null) {
            Settle($cur, $today, $maxItems, $todayItems);
        }

        fclose($fh);
    }
}
if (!function_exists('MysqlInfo')) {
    function MysqlInfo(){
        $out = array(
            'tables'   => 0,
            'rows'     => 0,
            'size_mb'  => 0,
            'data_mb'  => 0,
            'index_mb' => 0,
        );
        $sql = "SELECT DATABASE() AS dbname,
                       COUNT(*) AS t,
                       IFNULL(SUM(TABLE_ROWS),0) AS r,
                       IFNULL(SUM(DATA_LENGTH),0) AS d,
                       IFNULL(SUM(INDEX_LENGTH),0) AS i
                FROM information_schema.TABLES
                WHERE TABLE_SCHEMA = DATABASE()";
        try {
            $res = Data::JoinQuery($sql);
        } catch (Throwable $e) {
            $out['error'] = $e->getMessage();
            return $out;
        }
        $row = array();
        if (is_array($res)) {
            if (isset($res['querydata'][0]) && is_array($res['querydata'][0])) {
                $row = $res['querydata'][0];
            } elseif (isset($res[0]) && is_array($res[0])) {
                $row = $res[0];
            } elseif (isset($res['t']) || isset($res['T'])) {
                $row = $res;
            }
        }
        if (empty($row)) {
            $out['error'] = '未取到统计结果';
            return $out;
        }
        $get = function ($k) use ($row) {
            if (isset($row[$k])) return $row[$k];
            $uk = strtoupper($k);
            if (isset($row[$uk])) return $row[$uk];
            return 0;
        };
        $dataB  = (float)$get('d');
        $indexB = (float)$get('i');
        $out['tables']   = (int)$get('t');
        $out['rows']     = (int)$get('r');
        $out['size_mb']  = round(($dataB + $indexB) / 1048576, 2);
        $out['data_mb']  = round($dataB / 1048576, 2);
        $out['index_mb'] = round($indexB / 1048576, 2);
        return $out;
    }
}
$mysql = MysqlInfo();
$error = array(
    "date"  => $today,
    "items" => $todayItems,
);
if ($debug) {
    $diag['lines']  = $lineCount;
    $diag['first3'] = $firstLines;
    $error['diag']  = $diag;
}
$data = array(
    "work" => array(
        "version" => $framework,
        "disk"    => "磁盘共".$diskTotal."，可用".$diskFree."，已用".$diskUsed,
        "mysql"   => $mysql,
        "error"   => $error,
    ),
);
echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);