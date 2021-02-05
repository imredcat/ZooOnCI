<?php
/**
* Document info
* @project
* @modifydate
* @package
* @version
* @comment
https://manas.tungare.name/software/css-compression-in-php
*/
error_reporting(E_ALL^E_NOTICE);
ob_start();
// Enable caching
header('Cache-Control: public');
// Expire in one day
header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 86400) . ' GMT');
// Set the correct MIME type, because Apache won't set it for us
header("Content-type: text/css");

$is_debug = $_GET['debug']?$_GET['debug']:0;

$a = $_SERVER['HTTP_REFERER'];
if (!$a && $is_debug == 0) {
    //exit();
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
$dir_caches .= "css/";
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

$arr_css = $_GET['csslists'];

$version = $_GET['v']?$_GET['v']:date("YmW");
$cachefile = $version."_".md5($arr_css).".css";
$cachefile = $dir_caches.$cachefile;
if (file_exists($cachefile) && filesize($cachefile) > 0 && $is_debug != 1) {
    echo "/* -- Cached File -- */\n";
    $buffer = file_get_contents($cachefile);
    echo $buffer;
    exit;
}

$arr_css = explode("|", $arr_css);
$arr_nm = array();
$arr_pth = array();
$arr_url = array();
foreach ($arr_css as $css_val) {
    $arr_css_val = array_values(array_diff(explode("/", $css_val), array('',null)));
    if ($arr_css_val[0] == "_mod_") {
        //$arr_css_val = array_insert($arr_css_val, 2, "assets"); //처음부터 해당 인덱스까지 자름
        $arr_css_val[0] = $DMGDIR;
        //array_unshift($arr_css_val, "__ZOO__");
        //$css = str_replace("//", "/", "/".join("/", $arr_css_val));
        $css = join("/", $arr_css_val);
    } else {
        $css = $ROOTDIR.$css_val;
    }
    $pth_css = pathinfo($css);
    $_pth = $css;
    if ($pth_css['extension'] != 'css' || !file_exists($_pth)) {
        continue;
    }
    $arr_nm[] = $pth_css['filename'];
    $arr_url[] = $css_val;
    $arr_pth[] = $_pth;
}
$buffer = array();
foreach ($arr_pth as $key => $cssfile) {
    $_bf = trim(file_get_contents($cssfile));
    $_bf = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $_bf);
    $_bf = str_replace(': ', ':', $_bf);
    $_bf = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $_bf);
    $a_pth = array_diff(explode("/", $arr_pth[$key]), array(""));

    $x = count($a_pth);

    for ($i=$x;$i>0;$i--) {
        $arr_src_path = array_pad(array(), $i, "../");
        $arr_dist_path = array_slice($a_pth, 0, $x-$i);
        array_push($arr_dist_path, "");
        $dist_path = "/".join("/", $arr_dist_path);
        $src_path = join($arr_src_path);
        $_bf = str_replace($src_path, $dist_path, $_bf);
    }
    $_pth_ = "";
    //
    $buffer[] = "\n\n/* -- ".$arr_url[$key]." -- */\n".trim($_bf);
}
$buffer = join("\n", $buffer);


$fp = fopen($cachefile, 'w');
fwrite($fp, $buffer);
fclose($fp);
chmod($cachefile, 0777);
// Write everything out
echo($buffer);
