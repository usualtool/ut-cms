<?php
declare(strict_types=1);
$CONFIG = [
    // MCP 客户端访问令牌（Authorization: Bearer <token> 或 ?token=<token>）
    // 公网暴露必须设置
    'token'           => '',
    // 允许的浏览器 Origin（可选，空数组 = 不限制）
    'allowed_origins' => [],
    // 单次请求体上限（字节）
    'max_body'        => 4194304,
    // 对外声明的数据页面
    'pages'           => [
        'data-work' => [
            'title' => '数据',
            'desc'  => '获取数据概况（磁盘 / MySQL / 错误日志）',
        ],
    ],
    'server_name'     => 'ut-api-data',
    'server_version'  => '4.5.0',
];
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
@ini_set('display_errors', '0');
@ini_set('log_errors', '1');
@ini_set('html_errors', '0');
@ini_set('zlib.output_compression', '0');
@ini_set('implicit_flush', '0');
ob_start();
$GLOBALS['__ut_responded'] = false;
set_error_handler(static function (int $no, string $str, string $file, int $line) {
    if (!(error_reporting() & $no)) {
        return false;
    }
    throw new ErrorException($str, 0, $no, $file, $line);
});
register_shutdown_function(static function () {
    $err = error_get_last();
    $fatal = [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR];
    if ($err === null || !in_array($err['type'], $fatal, true)) {
        return;
    }
    error_log('[mcp.php] fatal: ' . $err['message'] . ' @ ' . $err['file'] . ':' . $err['line']);
    if ($GLOBALS['__ut_responded']) {
        return;
    }
    while (ob_get_level() > 0) {
        @ob_end_clean();
    }
    if (!headers_sent()) {
        http_response_code(500);
        header('Content-Type: application/json; charset=utf-8');
    }
    echo json_encode(
        ['jsonrpc' => '2.0', 'id' => null, 'error' => ['code' => -32603, 'message' => 'PHP fatal: ' . $err['message']]],
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );
    $GLOBALS['__ut_responded'] = true;
});
function ut_discard_buffer(): void{
    while (ob_get_level() > 0) {
        @ob_end_clean();
    }
}
function ut_json($payload): string{
    $json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($json === false) {
        $json = '{"jsonrpc":"2.0","id":null,"error":{"code":-32603,"message":"json_encode failed"}}';
    }
    return $json;
}
function ut_to_utf8(string $text): string{
    if ($text === '') {
        return $text;
    }
    if (function_exists('mb_check_encoding') && !mb_check_encoding($text, 'UTF-8')) {
        return mb_convert_encoding($text, 'UTF-8', 'GB18030');
    }
    return $text;
}
function ut_is_list(array $value): bool{
    if ($value === []) {
        return true;
    }
    return array_keys($value) === range(0, count($value) - 1);
}
function ut_respond_json(int $status, $payload): void{
    ut_discard_buffer();
    if (!headers_sent()) {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store');
    }
    $body = ut_json($payload);
    if (!headers_sent()) {
        header('Content-Length: ' . strlen($body));
    }
    echo $body;
    $GLOBALS['__ut_responded'] = true;
}
function ut_respond_text(int $status, string $text): void{
    ut_discard_buffer();
    if (!headers_sent()) {
        http_response_code($status);
        header('Content-Type: text/plain; charset=utf-8');
        header('Cache-Control: no-store');
        header('Content-Length: ' . strlen($text));
    }
    echo $text;
    $GLOBALS['__ut_responded'] = true;
}
function ut_respond_empty(int $status): void{
    ut_discard_buffer();
    if (!headers_sent()) {
        http_response_code($status);
        header('Content-Length: 0');
    }
    $GLOBALS['__ut_responded'] = true;
}
function ut_apply_cors(array $CONFIG): void{
    $origin  = isset($_SERVER['HTTP_ORIGIN']) ? (string) $_SERVER['HTTP_ORIGIN'] : '';
    $allowed = isset($CONFIG['allowed_origins']) && is_array($CONFIG['allowed_origins'])
        ? $CONFIG['allowed_origins'] : [];
    if ($origin === '') {
        return;
    }
    if ($allowed === [] || in_array($origin, $allowed, true)) {
        header('Access-Control-Allow-Origin: ' . $origin);
        header('Vary: Origin');
        header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-UT-Token, Mcp-Session-Id, Accept, Last-Event-ID');
        header('Access-Control-Max-Age: 86400');
    }
}
function ut_origin_rejected(array $CONFIG): bool{
    $allowed = isset($CONFIG['allowed_origins']) && is_array($CONFIG['allowed_origins'])
        ? $CONFIG['allowed_origins'] : [];
    if ($allowed === []) {
        return false;
    }
    $origin = isset($_SERVER['HTTP_ORIGIN']) ? (string) $_SERVER['HTTP_ORIGIN'] : '';
    if ($origin === '') {
        return false;
    }
    return !in_array($origin, $allowed, true);
}
function ut_request_token(&$via = null): string{
    foreach (['HTTP_AUTHORIZATION', 'REDIRECT_HTTP_AUTHORIZATION'] as $key) {
        if (!empty($_SERVER[$key]) && is_string($_SERVER[$key])) {
            $value = trim($_SERVER[$key]);
            if (strncasecmp($value, 'Bearer ', 7) === 0) {
                $via = 'authorization';
                return trim(substr($value, 7));
            }
            if ($value !== '') {
                $via = 'authorization';
                return $value;
            }
        }
    }
    foreach (['HTTP_X_UT_TOKEN', 'HTTP_X_AUTH_TOKEN'] as $key) {
        if (!empty($_SERVER[$key]) && is_string($_SERVER[$key])) {
            $via = 'header';
            return trim($_SERVER[$key]);
        }
    }
    if (function_exists('getallheaders')) {
        $all = getallheaders();
        if (is_array($all)) {
            foreach ($all as $key => $value) {
                if (!is_string($value)) {
                    continue;
                }
                $lower = strtolower((string) $key);
                if ($lower === 'authorization') {
                    $value = trim($value);
                    if (strncasecmp($value, 'Bearer ', 7) === 0) {
                        $via = 'getallheaders';
                        return trim(substr($value, 7));
                    }
                    if ($value !== '') {
                        $via = 'getallheaders';
                        return $value;
                    }
                }
                if ($lower === 'x-ut-token' || $lower === 'x-auth-token') {
                    $via = 'getallheaders';
                    return trim($value);
                }
            }
        }
    }
    if (isset($_GET['token']) && is_string($_GET['token'])) {
        $via = 'query';
        return trim($_GET['token']);
    }
    return '';
}
function ut_authorized(array $CONFIG): bool{
    $expected = isset($CONFIG['token']) ? (string) $CONFIG['token'] : '';
    if ($expected === '') {
        return true;
    }
    $actual = ut_request_token();
    if ($actual === '') {
        return false;
    }
    return hash_equals($expected, $actual);
}
function ut_front_pages(): array{
    $pages = [];
    $self  = basename(__FILE__);
    foreach (scandir(__DIR__) ?: [] as $name) {
        if (substr($name, -4) !== '.php' || $name === 'data-verify.php' || $name === $self) {
            continue;
        }
        $pages[] = substr($name, 0, -4);
    }
    sort($pages);
    return $pages;
}
function ut_gate(array $CONFIG): bool{
    global $config;
    ut_apply_cors($CONFIG);
    $method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
    if ($method === 'OPTIONS') {
        ut_respond_empty(204);
        return false;
    }
    if (!isset($config) || !is_array($config) || empty($config['UTCODE'])
        || !class_exists('library\\UsualToolData\\UTData')) {
        $utWhy = [];
        if (!isset($config) || !is_array($config)) {
            $utWhy[] = 'config未加载';
        } elseif (empty($config['UTCODE'])) {
            $utWhy[] = 'UTCODE为空';
        }
        if (!class_exists('library\\UsualToolData\\UTData')) {
            $utWhy[] = 'UTData类未加载';
        }
        ut_respond_json(500, [
            'error' => 'MCP未运行在UT框架环境中',
            'hint'  => '缺失项: ' . implode(' / ', $utWhy) . '；config来源: ' . ($GLOBALS['__ut_cfg_src'] ?? 'none'),
        ]);
        return false;
    }
    if ($method === 'GET' && isset($_GET['health'])) {
        ut_respond_json(200, [
            'ok'      => true,
            'server'  => (string) $CONFIG['server_name'],
            'version' => (string) $CONFIG['server_version'],
            'php'     => PHP_VERSION,
            'framework' => 'UT $config 已加载',
            'time'    => gmdate('Y-m-d H:i:s') . 'Z',
        ]);
        return false;
    }
    if (ut_origin_rejected($CONFIG)) {
        ut_respond_json(403, ['jsonrpc' => '2.0', 'id' => null, 'error' => ['code' => -32002, 'message' => 'Origin not allowed']]);
        return false;
    }
    if (!ut_authorized($CONFIG)) {
        header('WWW-Authenticate: Bearer realm="ut-mcp"');
        ut_respond_json(401, [
            'jsonrpc' => '2.0',
            'id'      => null,
            'error'   => ['code' => -32001, 'message' => 'Unauthorized：请在 Authorization: Bearer <token> 头或 ?token= 参数中提供访问令牌'],
        ]);
        return false;
    }
    return true;
}
function ut_parse_body(array $CONFIG){
    $maxBody = (int) $CONFIG['max_body'];
    $length  = 0;
    if (isset($_SERVER['CONTENT_LENGTH']) && is_numeric($_SERVER['CONTENT_LENGTH'])) {
        $length = (int) $_SERVER['CONTENT_LENGTH'];
    }
    if ($maxBody > 0 && $length > $maxBody) {
        ut_respond_json(413, ['jsonrpc' => '2.0', 'id' => null, 'error' => ['code' => -32600, 'message' => '请求体超过 ' . $maxBody . ' 字节']]);
        return false;
    }
    $raw = file_get_contents('php://input');
    if ($raw === false || $raw === '') {
        ut_respond_json(400, ['jsonrpc' => '2.0', 'id' => null, 'error' => ['code' => -32700, 'message' => 'Parse error: empty request body']]);
        return false;
    }
    $parsed = json_decode($raw, true);
    if (!is_array($parsed)) {
        ut_respond_json(400, ['jsonrpc' => '2.0', 'id' => null, 'error' => ['code' => -32700, 'message' => 'Parse error: invalid JSON']]);
        return false;
    }
    return ut_is_list($parsed) ? $parsed : [$parsed];
}
function ut_bridge_headers(): void{
    global $config;
    $timeStr = date('Y-m-d H:i:s');
    $_SERVER['HTTP_TIME']    = $timeStr;
    $_SERVER['HTTP_TOKEN']   = md5(md5((string) $config['UTCODE']) . (string) strtotime($timeStr));
    $_SERVER['HTTP_REFERER'] = (string) $config['APPURL'];
}
function ut_fetch(array $CONFIG, string $page, string $postData = ''): string{
    $page = trim($page);
    if ($page === '') {
        throw new InvalidArgumentException('page 参数不能为空');
    }
    if (!preg_match('/^[A-Za-z0-9\-]+$/', $page)) {
        throw new InvalidArgumentException('page 参数含非法字符: ' . $page);
    }
    $file = __DIR__ . '/' . $page . '.php';
    if (!is_file($file)) {
        throw new RuntimeException('页面不存在: ' . $page . '（front 目录可用: ' . implode('、', ut_front_pages()) . '）');
    }
    global $config;
    $depth = ob_get_level();
    ob_start();
    try {
        include $file;
    } catch (Throwable $e) {
        while (ob_get_level() > $depth) {
            @ob_end_clean();
        }
        throw $e;
    }
    $out  = (string) ob_get_clean();
    $text = ut_to_utf8($out);
    $decoded = json_decode($text, true);
    if (is_array($decoded)) {
        foreach (['content', 'data', 'markdown', 'text', 'body'] as $key) {
            if (isset($decoded[$key]) && is_string($decoded[$key])) {
                return $decoded[$key];
            }
        }
        $pretty = json_encode($decoded, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        return $pretty === false ? $text : $pretty;
    }
    return $text;
}
function ut_tool_definitions(array $CONFIG): array{
    $defs = [];
    foreach ($CONFIG['pages'] as $page => $meta) {
        $defs[] = [
            'name'        => 'get_' . str_replace('-', '_', (string) $page),
            'description' => (string) $meta['desc'] . '（返回 Markdown/JSON 格式内容）',
            'inputSchema' => [
                'type'                 => 'object',
                'properties'           => new stdClass(),
                'additionalProperties' => false,
            ],
        ];
    }
    $defs[] = [
        'name'        => 'get_page',
        'description' => '按页面名获取框架数据（须为 ut-api模块下的页面，查询数据请用 data-query 页面）。',
        'inputSchema' => [
            'type'       => 'object',
            'properties' => [
                'page' => ['type' => 'string', 'description' => '页面名，例如 data-work'],
            ],
            'required'   => ['page'],
        ],
    ];
    $defs[] = [
        'name'        => 'get_data_query',
        'description' => '查询框架数据（走 data-query 页面，参数透传给页面，白名单由页面内部校验，只读）。',
        'inputSchema' => [
            'type'       => 'object',
            'properties' => [
                'table'  => ['type' => 'string', 'description' => '要查询的表名，须在 cms_api_set.opentable 白名单内'],
                'where'  => ['type' => 'string', 'description' => '查询条件，例如 id=1'],
                'fields' => ['type' => 'string', 'description' => '返回字段，逗号分隔（选填，页面支持时生效）'],
                'order'  => ['type' => 'string', 'description' => '排序（选填，页面支持时生效）'],
                'limit'  => ['type' => 'string', 'description' => '返回条数（选填，页面支持时生效）'],
            ],
            'required'             => ['table'],
            'additionalProperties' => true,
        ],
    ];
    $defs[] = [
        'name'        => 'list_pages',
        'description' => '列出当前 MCP 服务已支持的框架数据页面',
        'inputSchema' => [
            'type'                 => 'object',
            'properties'           => new stdClass(),
            'additionalProperties' => false,
        ],
    ];
    return $defs;
}
function ut_text_result(string $text): array{
    return ['content' => [['type' => 'text', 'text' => $text]]];
}
function ut_call_tool(array $CONFIG, string $name, array $args): array{
    if ($name === 'list_pages') {
        $lines = [];
        foreach ($CONFIG['pages'] as $page => $meta) {
            $lines[] = '- ' . $page . '：' . $meta['title'] . ' —— ' . $meta['desc'];
        }
        return ut_text_result("已支持的数据页面：\n" . implode("\n", $lines));
    }
    if ($name === 'get_data_query') {
        foreach ($args as $key => $value) {
            if (!is_string($key) || preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $key) !== 1) {
                continue;
            }
            if (!is_scalar($value)) {
                continue;
            }
            $val          = (string) $value;
            $_GET[$key]   = $val;
            $_POST[$key]  = $val;
            $_REQUEST[$key] = $val;
        }
        return ut_text_result(ut_fetch($CONFIG, 'data-query'));
    }
    if ($name === 'get_page') {
        $page = isset($args['page']) && is_string($args['page']) ? trim($args['page']) : '';
    } elseif (strncmp($name, 'get_', 4) === 0) {
        $slug = substr($name, 4);
        $page = null;
        foreach ($CONFIG['pages'] as $candidate => $meta) {
            if (str_replace('-', '_', (string) $candidate) === $slug) {
                $page = (string) $candidate;
                break;
            }
        }
        if ($page === null) {
            $page = str_replace('_', '-', $slug);
        }
    } else {
        throw new RuntimeException('未知工具: ' . $name);
    }
    return ut_text_result(ut_fetch($CONFIG, (string) $page));
}
function ut_rpc_result($id, $result): array{
    return ['jsonrpc' => '2.0', 'id' => $id, 'result' => $result];
}
function ut_rpc_error($id, int $code, string $message): array{
    return ['jsonrpc' => '2.0', 'id' => $id, 'error' => ['code' => $code, 'message' => $message]];
}
function ut_handle_message(array $msg, array $CONFIG){
    $method = isset($msg['method']) && is_string($msg['method']) ? $msg['method'] : '';
    $id     = array_key_exists('id', $msg) ? $msg['id'] : null;
    $params = isset($msg['params']) && is_array($msg['params']) ? $msg['params'] : [];
    $isRequest = ($id !== null);
    if ($method === '') {
        return $isRequest ? ut_rpc_error($id, -32600, 'Invalid Request: missing method') : null;
    }
    if ($method === 'initialize') {
        $supported  = ['2024-11-05', '2025-03-26', '2025-06-18'];
        $clientVer  = isset($params['protocolVersion']) ? (string) $params['protocolVersion'] : '';
        $negotiated = in_array($clientVer, $supported, true) ? $clientVer : '2024-11-05';
        return ut_rpc_result($id, [
            'protocolVersion' => $negotiated,
            'capabilities'    => ['tools' => ['listChanged' => false]],
            'serverInfo'      => [
                'name'    => (string) $CONFIG['server_name'],
                'version' => (string) $CONFIG['server_version'],
            ],
            'instructions'    => '本服务提供框架数据查询。调用 get_data_work 获取数据概况；查询数据用 get_data_query（table/where）；其他页面用 get_page 指定 page 名。',
        ]);
    }
    if ($method === 'tools/list') {
        return ut_rpc_result($id, ['tools' => ut_tool_definitions($CONFIG)]);
    }
    if ($method === 'tools/call') {
        $name = isset($params['name']) && is_string($params['name']) ? $params['name'] : '';
        $args = isset($params['arguments']) && is_array($params['arguments']) ? $params['arguments'] : [];
        try {
            return ut_rpc_result($id, ut_call_tool($CONFIG, $name, $args));
        } catch (Throwable $e) {
            error_log('[mcp.php] tool ' . $name . ' failed: ' . $e->getMessage());
            return ut_rpc_result($id, [
                'content' => [['type' => 'text', 'text' => '工具执行出错: ' . $e->getMessage()]],
                'isError' => true,
            ]);
        }
    }
    if ($method === 'ping') {
        return ut_rpc_result($id, new stdClass());
    }
    if ($method === 'resources/list' || $method === 'resources/templates/list') {
        return ut_rpc_result($id, ['resources' => []]);
    }
    if ($method === 'prompts/list') {
        return ut_rpc_result($id, ['prompts' => []]);
    }
    if (in_array($method, [
        'notifications/initialized',
        'initialized',
        'notifications/cancelled',
        'notifications/progress',
        'notifications/roots/list_changed',
        'logging/setLevel',
    ], true)) {
        return null;
    }
    return $isRequest ? ut_rpc_error($id, -32601, 'Method not found: ' . $method) : null;
}
function ut_route(array $CONFIG, $msgs): void{
    $method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
    if ($method === 'GET') {
        if (isset($_GET['tools'])) {
            ut_respond_json(200, ['tools' => ut_tool_definitions($CONFIG)]);
            return;
        }
        if (isset($_GET['dump'])) {
            $page = is_string($_GET['dump']) ? trim($_GET['dump']) : '';
            if ($page === '') {
                $page = (string) key($CONFIG['pages']);
            }
            ut_respond_text(200, ut_fetch($CONFIG, $page));
            return;
        }
        if (isset($_GET['selftest'])) {
            global $config, $_modpath_, $_form_;
            $via = 'none';
            ut_request_token($via);
            $report = [
                'php' => [
                    'version'      => PHP_VERSION,
                    'sapi'         => PHP_SAPI,
                    'timezone_ini' => (string) ini_get('date.timezone'),
                    'display_err'  => (string) ini_get('display_errors'),
                    'ob_level'     => ob_get_level(),
                ],
                'framework' => [
                    'utcode_set' => !empty($config['UTCODE']),
                    'appurl'     => (string) ($config['APPURL'] ?? ''),
                    'module'     => (string) ($_modpath_ ?? ''),
                    'form'       => (string) ($_form_ ?? ''),
                    'front_pages'=> ut_front_pages(),
                ],
                'config' => [
                    'token_required' => (string) $CONFIG['token'] !== '',
                    'auth_via'       => $via,
                    'pages'          => array_keys($CONFIG['pages']),
                    'server'         => (string) $CONFIG['server_name'] . ' v' . (string) $CONFIG['server_version'],
                ],
                'api' => [],
            ];
            $page    = (string) key($CONFIG['pages']);
            $started = microtime(true);
            try {
                $content = ut_fetch($CONFIG, $page);
                $report['api'] = [
                    'page'       => $page,
                    'ok'         => true,
                    'bytes'      => strlen($content),
                    'elapsed_ms' => (int) round((microtime(true) - $started) * 1000),
                    'preview'    => substr($content, 0, 300),
                ];
            } catch (Throwable $e) {
                $report['api'] = ['page' => $page, 'ok' => false, 'error' => $e->getMessage()];
            }
            ut_respond_json(200, $report);
            return;
        }
        header('Allow: POST, GET, OPTIONS');
        ut_respond_json(405, [
            'error' => 'Method Not Allowed',
            'hint'  => 'MCP 端点请使用 POST 发送 JSON-RPC；浏览器自检请访问 ?health=1 / ?selftest=1 / ?tools=1 / ?dump=data-work',
        ]);
        return;
    }
    if ($method !== 'POST') {
        header('Allow: POST, GET, OPTIONS');
        ut_respond_json(405, ['error' => 'Method Not Allowed', 'hint' => 'Use POST for JSON-RPC']);
        return;
    }
    $responses = [];
    foreach ((array) $msgs as $item) {
        if (is_array($item)) {
            $response = ut_handle_message($item, $CONFIG);
            if ($response !== null) {
                $responses[] = $response;
            }
        }
    }
    if ($responses === []) {
        ut_respond_empty(202);
        return;
    }
    $payload = count($responses) === 1 ? $responses[0] : $responses;
    $accept = isset($_SERVER['HTTP_ACCEPT']) ? (string) $_SERVER['HTTP_ACCEPT'] : '';
    if (strpos($accept, 'text/event-stream') !== false && strpos($accept, 'application/json') === false) {
        ut_discard_buffer();
        if (!headers_sent()) {
            http_response_code(200);
            header('Content-Type: text/event-stream; charset=utf-8');
            header('Cache-Control: no-cache, no-transform');
            header('X-Accel-Buffering: no');
        }
        echo 'event: message' . "\n" . 'data: ' . ut_json($payload) . "\n\n";
        $GLOBALS['__ut_responded'] = true;
        return;
    }
    ut_respond_json(200, $payload);
}
try {
    $GLOBALS['__ut_cfg_src'] = 'native($config)';
    $GLOBALS['config'] = (isset($config) && is_array($config) && !empty($config))
        ? $config
        : (class_exists('library\\UsualToolInc\\UTInc') ? \library\UsualToolInc\UTInc::GetConfig() : null);
    $ut_msgs = null;
    if (strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET')) === 'POST') {
        $ut_msgs = ut_parse_body($CONFIG);
        if ($ut_msgs === false) {
            return;
        }
    }
    ut_bridge_headers();
    if (!ut_gate($CONFIG)) {
        return;
    }
    ut_route($CONFIG, $ut_msgs);
} catch (Throwable $e) {
    error_log('[mcp.php] unhandled: ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());
    if (!$GLOBALS['__ut_responded']) {
        ut_respond_json(500, ['jsonrpc' => '2.0', 'id' => null, 'error' => ['code' => -32603, 'message' => 'Internal error: ' . $e->getMessage()]]);
    }
}