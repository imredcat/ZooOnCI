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

$is_debug = $_GET['debug']?$_GET['debug']:0;
$a = $_SERVER['HTTP_REFERER'];
$src = $_GET['src'];
$xtnd = $_GET['xtnd'];

if (!$src || !in_array($xtnd,['js','css'])) {
  exit;
}

// Enable caching
header('Cache-Control: public');
// Expire in one day
header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 604800) . ' GMT');
// Set the correct MIME type, because Apache won't set it for us
if ($xtnd == 'css') {
  header("Content-type: text/css");
}
if ($xtnd == 'js') {
  header('Content-type: text/javascript');
}



$a = $_SERVER['HTTP_REFERER'];
if (!$a && $is_debug == 0) {
    //exit();
}


$ROOTDIR = rtrim($_SERVER['DOCUMENT_ROOT'], '/').'/';
$ZOODIR = $ROOTDIR."../zoo/";
$dir_caches = $ROOTDIR."../writable/cache/cssjs/";

if (!is_dir($dir_caches)) {
    mkdir($dir_caches, 0777);
    chmod($dir_caches, 0777);
}
$dir_caches .= $xtnd."/";
if (!is_dir($dir_caches)) {
    mkdir($dir_caches, 0777);
    chmod($dir_caches, 0777);
}


if (strpos($src,'cached_') === 0) {
    echo "/* -- Cached File -- */\n";
    $cachefile = $dir_caches.$src;
}else if(strpos($src,'_mod_') === 0){
  $arr_path = array_values(array_diff(explode("/", $src), array('',null)));
  if ($arr_path[0] == '_mod_') {
    echo "/* -- Mod File {$src} -- */\n";
    $arr_path[0] = $ZOODIR;
    $cachefile = str_replace('//','/',join("/", $arr_path));
  }
}else {
    $cachefile = $ROOTDIR.$src;
}
//chdir($ROOTDIR.'assets/zTree_v3/css/zTreeStyle/');
if (file_exists($cachefile) && filesize($cachefile) > 0 && $is_debug != 1) {
    
    $buffer = file_get_contents($cachefile);
    echo $buffer;
    
}
exit;