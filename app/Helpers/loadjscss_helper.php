<?php

/**
 * uri 호출 구조에 따른 js 와 css 를 자동으로 호출하는 기능
 */
function loadJsCss($arr_jscss=[])
{
  $arr_jscss = $arr_jscss?$arr_jscss:[];
  if (count($arr_jscss) == 0) {
    return false;
  }
  foreach ($arr_jscss as $filetype => $group) {
    foreach ($group as $tagtype => $items) {
      foreach ($items as $item) {
        $item = is_array($item)?$item:[$item,0];
        $src = $item[0];
        //$mtime = $item[1];
        if ($filetype == 'css') {
          if (strstr($src, 'cached_') === false) {
            echo "<link href='{$src}?mt={$mtime}' rel='stylesheet'>\n";
          }else{
            echo "<link href='/assets/jscssload/jscssload.php?xtnd=css&src={$src}&mt={$mtime}' rel='stylesheet'>\n";
          }
        }else {
          $_tagtype = in_array($tagtype, ['defer','async'])?$tagtype:'';
          if (strstr($src,'cached_') === false) {
            echo "<script src='{$src}?mt={$mtime}' {$_tagtype}></script>\n";
          }else {
            echo "<script src='/assets/jscssload/jscssload.php?xtnd=js&src={$src}&mt={$mtime}' {$_tagtype}></script>\n";
          }
        }
      }
    }
  }
}

function loadJsCssCustom($arr_js)
{
  static $minify = false;
  if (!$minify) {
    $minify = \Zoo\Common\Config\Services::libminifyjscss();
  }
  $arr_js = $minify->loadjscss($arr_js,0);
  loadJsCss($arr_js);
}