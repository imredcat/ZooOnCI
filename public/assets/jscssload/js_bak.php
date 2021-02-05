<?php
/**
* Document info
* @project
* @modifydate
* @package
* @version
* @comment
https://manas.tungare.name/software/js-compression-in-php
*/
error_reporting(E_ALL^E_NOTICE);
ob_start();
// Enable caching
header('Cache-Control: public');
// Expire in one day
header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 86400) . ' GMT');
// Set the correct MIME type, because Apache won't set it for us
header('Content-type: text/javascript');
// Write everything out
$path = rtrim(__DIR__, '/');
$path = $path.'/../../../vendor/matthiasmullie';
require_once $path . '/minify/src/Minify.php';
require_once $path . '/minify/src/CSS.php';
require_once $path . '/minify/src/JS.php';
require_once $path . '/minify/src/Exception.php';
require_once $path . '/minify/src/Exceptions/BasicException.php';
require_once $path . '/minify/src/Exceptions/FileImportException.php';
require_once $path . '/minify/src/Exceptions/IOException.php';
require_once $path . '/path-converter/src/ConverterInterface.php';
require_once $path . '/path-converter/src/Converter.php';

$minify = new \MatthiasMullie\Minify\JS();

$is_debug = $_GET['debug']?$_GET['debug']:0;

$a = $_SERVER['HTTP_REFERER'];
if (!$a) {
    //	exit();
}
function array_insert($arr, $idx, $add)
{
    $arr_front = array_slice($arr, 0, $idx);
    $arr_end = array_slice($arr, $idx);
    $arr_front[] = $add;
    return array_merge($arr_front, $arr_end);
}

$ROOTDIR = rtrim($_SERVER['DOCUMENT_ROOT'], '/').'/';
$DMGDIR = $ROOTDIR."../dmg/";
$dir_caches = $ROOTDIR."../writable/cache/cssjs/";

if (!is_dir($dir_caches)) {
    mkdir($dir_caches, 0777);
    chmod($dir_caches, 0777);
}
$dir_caches .= "js/";
if (!is_dir($dir_caches)) {
    mkdir($dir_caches, 0777);
    chmod($dir_caches, 0777);
}

if ($dh = opendir($dir_caches)) {
    while (($filejs = readdir($dh)) !== false) {
        if ($filejs == "." || $filejs == "..") {
            continue;
        }
        $filetm = fileatime($dir_caches.$filejs);
        if (time() - $filetm > 259200) {
            unlink($dir_caches.$filejs);
        }
    }
    closedir($dh);
}


$arr_js = $_GET['jslists'];
$version = $_GET['v']?$_GET['v']:date("YmW");
$cachefile = $version."_".md5($arr_js).".js";
$cachefile = $dir_caches.$cachefile;

if (file_exists($cachefile) && filesize($cachefile) > 0 && $is_debug != 1) {
    $buffer = file_get_contents($cachefile);
    echo "/* Cached File */\n$buffer";
    exit;
}

$arr_js = explode("|", $arr_js);
$arr_nm = array();
$arr_pth = array();
$arr_url = array();
foreach ($arr_js as $js_val) {
    $arr_js_val = array_values(array_diff(explode("/", $js_val), array('',null)));
    if ($arr_js_val[0] == "_mod_") {
        //$arr_js_val = array_insert($arr_js_val, 2, "assets"); //처음부터 해당 인덱스까지 자름
        $arr_js_val[0] = $DMGDIR;
        $arr_js_val[1] = ucfirst($arr_js_val[1]);
        //array_unshift($arr_js_val, "__ZOO__");
        //$js = str_replace("//", "/", "/".join("/", $arr_js_val));
        $js = join("/", $arr_js_val);
    //echo $js;
    } else {
        $js = $ROOTDIR.$js_val;
    }
    $js = str_replace("//", "/", $js);
    $pth_js = pathinfo($js);
    $_pth = $js;
    if ($pth_js['extension'] != 'js' || !file_exists($_pth)) {
        continue;
    }
    $arr_nm[] = $pth_js['filename'];
    $arr_url[] = $js_val;
    $arr_pth[] = $_pth;
}

$buffer = array();
foreach ($arr_pth as $key => $jsfile) {
    $_bf = trim(file_get_contents($jsfile));
    $buffer[] = "/* -- ".$arr_url[$key]." -- */\n"
                    ."console.log('load -js : ".$arr_url[$key]."');\n".trim($_bf);
}
$buffer = join("\n\n", $buffer);
if ($is_debug == 1) {
    echo $buffer;
    exit;
}

$url = 'https://javascript-minifier.com/raw';
$ch = curl_init();
curl_setopt_array($ch, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => array("Content-Type: application/x-www-form-urlencoded"),
        CURLOPT_POSTFIELDS => http_build_query(array("input" => $buffer ))
));
$minified = curl_exec($ch);
// finally, close the request
curl_close($ch);

$buffer = $minified?$minified:$buffer;

$fp = fopen($cachefile, 'w');
fwrite($fp, $buffer);
fclose($fp);
chmod($cachefile, 0777);
echo $buffer;
exit;
