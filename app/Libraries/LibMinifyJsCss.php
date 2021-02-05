<?php
namespace App\Libraries;

use \Zoo\Common\Config\Services as ZooServices;

// composer 사용 안할 경우 https://github.com/matthiasmullie/minify/issues/83
$path = APPPATH.'/Libraries';
require_once $path . '/minify/src/Minify.php';
require_once $path . '/minify/src/CSS.php';
require_once $path . '/minify/src/JS.php';
require_once $path . '/minify/src/Exception.php';
require_once $path . '/minify/src/Exceptions/BasicException.php';
require_once $path . '/minify/src/Exceptions/FileImportException.php';
require_once $path . '/minify/src/Exceptions/IOException.php';
require_once $path . '/path-converter/src/ConverterInterface.php';
require_once $path . '/path-converter/src/Converter.php';
use \MatthiasMullie\Minify as Minify;

/**
 * 모듈의 기본 스크립트, 스타일시트와 지정된 js,css 파일을 가져와서 minifiying 하고 캐시파일로 저장
 * 파일명 배열은 serialize , md5 하여 캐시파일명을 생성하고, 동일한 캐시 파일이 있는 캐시 생성일과 파일 수정일을 비교, 수정되어다면 캐시를 다시 새성한다.
 * 파일명 배열 구성 ['파일명:alone|defer|async|nominify','파일명:alone']
 * alone : 공통 지시자이며 캐시 생성 및 태그 생성시 독립적으로 생성된다.
 * defer|async : 스크립트 태그 생성시 속성값 alone 이 없으도 이 항목이 있을 경우 alone으로 생성된다.
 * nominify : alone 이며 minifying 하지 않는다.
 * 코드 내에 / * minified * / 문구가 있거나 파일명에 .min. 이 있으면 minify 하지 않으며 지시자가 없으면 통합 된다.
 * 모듈의 기본 js, css는 모두 통합된 캐시파일을태그로 생성 된다.
 * 태그 생성 순서는 모듈 등 통합 캐시 태그 다음에 alone(defer, sync)로 생성된다.
 * loadjscss -> getModeulJsCss:모듈 스크립트 추출 | getCustomJsCss : 요청된 스크립트 추출 -> getSrcUrl(array_merge(getModeulJsCss,getCustomJsCss))
 *  -> minify -> 스크립트 배열
 */

class LibMinifyJsCss
{
    public $arr_is_force = [];
    public function __construct()
    {
        if (!is_dir(DIR_CACHE_JSCSS)) {
            @mkdir(DIR_CACHE_JSCSS,0777);
        }
        if (!is_dir(DIR_CACHE_JSCSS.'js')) {
            @mkdir(DIR_CACHE_JSCSS.'js',0777);
        }
        if (!is_dir(DIR_CACHE_JSCSS.'css')) {
            @mkdir(DIR_CACHE_JSCSS.'css',0777);
        }
    }
    /**
     * js, css 파일을 가져온다.
     */
    public function loadjscss($arr_jscss=array(), $withmodule=1)
    {
        $this->gcCaches();
        $withmodule = $withmodule?$withmodule:0;
        $arr_jscss = is_array($arr_jscss)?$arr_jscss:[];
        $arr_css_mod = [];
        $arr_js_mod = [];
        if ($withmodule) {
            $arr_css_mod = $this->getModeulJsCss('css');  // 모듈 기본 css
            $arr_js_mod = $this->getModeulJsCss('js');    // 모듈 기본 js
        }
        
        $arr_css = $this->getCustomJsCss($arr_jscss, 'css');
        $arr_css['union'] = array_merge($arr_css_mod, $arr_css['union']);
        $arr_js = $this->getCustomJsCss($arr_jscss, 'js');
        $arr_js['union'] = array_merge($arr_js_mod, $arr_js['union']);
        //$arr_css = array_merge($this->getModeulJsCss('css'), $this->getCustomJsCss($arr_jscss, 'css'));
        //$arr_js = array_merge($this->getModeulJsCss('js'), $this->getCustomJsCss($arr_jscss, 'js'));
        $arr_css_path = $this->getSrcUrl($arr_css, 'css');
        $arr_js_path = $this->getSrcUrl($arr_js, 'js');
        return ['css'=>$arr_css_path,'js'=>$arr_js_path];
    }
    

    public function getModeulJsCss($tp = '')
    {
        static $arr_jscss = array();
        
        if (count($arr_jscss) > 0) {
            return $tp?$arr_jscss[$tp]:$arr_jscss;
        }
        $routes = \Config\Services::routes();
        $uri = \Config\Services::uri();
        $arr_request_uri = explode("/", $uri->getPath());
        $arr_js = [];
        $arr_css = [];
        $jscss_path =   "_mod_";
        $jscss_dir =    ZOOPATH;
        $jscss_fname = "";
        foreach ($arr_request_uri as $key => $value) {
            if (!$value) {
                if ($key == 0) {
                    $value = $routes->getDefaultController();
                } elseif ($key == 1) {
                    $value = $routes->getDefaultMethod();
                }
            }
            if ($key == 0) {
                $js_path = "{$jscss_path}/{$value}/assets/js/";
                $css_path = "{$jscss_path}/{$value}/assets/css/";
                $jscss_dir .= "{$value}/assets/";
            }
            $jscss_fname_single = strtolower($value);
            if (!$jscss_fname) {
                $jscss_fname = strtolower($value);
            } else {
                $jscss_fname .= "_".strtolower($value);
            }
            if (file_exists($jscss_dir."/js/".$jscss_fname.".js") &&
            !in_array($js_path.$jscss_fname.".js", $arr_js)) {
                $arr_js[] = $js_path.$jscss_fname.".js";
            }
            if (file_exists($jscss_dir."/js/".$jscss_fname_single.".js") &&
            !in_array($js_path.$jscss_fname_single.".js", $arr_js)) {
                $arr_js[] = $js_path.$jscss_fname_single.".js";
            }
            if (file_exists($jscss_dir."/css/".$jscss_fname.".css") &&
            !in_array($js_path.$jscss_fname.".css", $arr_css)) {
                $arr_css[] = $css_path.$jscss_fname.".css";
            }
            if (file_exists($jscss_dir."/css/".$jscss_fname_single.".css") &&
            !in_array($js_path.$jscss_fname_single.".css", $arr_css)) {
                $arr_css[] = $css_path.$jscss_fname_single.".css";
            }
        }
        $arr_css = array_unique($arr_css);
        $arr_js = array_unique($arr_js);
        $arr_jscss = ['js'=>$arr_js,'css'=>$arr_css];
        return $arr_jscss[$tp]?:$arr_jscss;
    }
    public function getCustomJsCss($arr_jscss, $w='')
    {
        $arr  = [];
        $arr['css'] = [];
        $arr['css']['union'] = [];
        $arr['css']['alone'] = [];
        
        $arr['js'] = [];
        $arr['js']['union'] = [];
        $arr['js']['alone'] = [];
        $arr['js']['defer'] = [];
        $arr['js']['async'] = [];
        if (count($arr_jscss)>0) {
            $_loaded = [];
            
            foreach ($arr_jscss as $key => $val) {
                $arr_val = explode(":", $val);
                $_file = array_shift($arr_val);
                if (in_array($_file, $_loaded) || !$this->getRealPath($_file)) {
                    continue;
                }
                
                $_fileinfo = pathinfo($_file);
                if ($_fileinfo['extension'] == 'css') {
                    if (in_array('alone', $arr_val)) {
                        $arr['css']['alone'][] = $_file;
                    } elseif (in_array('nominify', $arr_val) || strstr($_fileinfo['basename'], '.min.')) {
                        $arr['css']['nominify'][] = $_file;
                    } else {
                        $arr['css']['union'][] = $_file;
                    }
                } else {
                    if (in_array('alone', $arr_val)) {
                        $arr['js']['alone'][] = $_file;
                    } elseif (in_array('defer', $arr_val)) {
                        $arr['js']['defer'][] = $_file;
                    } elseif (in_array('async', $arr_val)) {
                        $arr['js']['async'][] = $_file;
                    } elseif (in_array('nominify', $arr_val) || strstr($_fileinfo['basename'], '.min.')) {
                        $arr['js']['nominify'][] = $_file;
                    } else {
                        if (strstr($_fileinfo['basename'], '.min.')) {
                            $arr['js']['alone'][] = $_file;
                        } else {
                            $arr['js']['union'][] = $_file;
                        }
                    }
                }
                if (in_array('force', $arr_val)) {
                    $arr_is_force[] = $_file;
                }
                $_loaded[] = $_file;
            }
        }
        if (empty($w)) {
            return $arr;
        }
        return $arr[$w];
    }
    public function getRealPath(string $file_path)
    {
        if (file_exists($file_path)) {
            return $file_path;
        }
        $arr_path = array_values(array_diff(explode("/", $file_path), array('',null)));
        if ($arr_path[0] == '_mod_') {
            $arr_path[0] = DIR_ZOO; //zoo/Common/Config/Constants.php
            $file_path = join("/", $arr_path);
        } else {
            $file_path = rtrim(FCPATH, '/').'/'.ltrim($file_path, '/');
        }
        
        if (file_exists($file_path)) {
            return $file_path;
        }
        return false;
    }
    public function Minify($arr_realpath, $tagtype, $cache_file_nm, $path_called)
    {
        $arr_realpath = is_array($arr_realpath)?$arr_realpath:[$arr_realpath];
        $path_called = is_array($path_called)?$path_called:[$path_called];
        if ($tagtype == 'css') {
            //$minifier = ZooServices::minifycss();
            //$minifier = new \MatthiasMullie\Minify\CSS();
            // composer 안될 경우
            $minifier = new Minify\CSS();
        } else {
            //$minifier = new \MatthiasMullie\Minify\JS();
            $minifier = new Minify\JS();
        }
        foreach ($arr_realpath as $key => $path) {
            $file_nm = pathinfo($path, PATHINFO_BASENAME);
            $pathinfo_called = rtrim(pathinfo($path_called[$key], PATHINFO_DIRNAME), '/').'/';
            $content = file_get_contents($path);
            
            $content = str_replace('url("./', 'url("'.$pathinfo_called, $content);
            $content = str_replace("url('./", "url(".$pathinfo_called, $content);
            $content = str_replace("url(./", "url(".$pathinfo_called, $content);
            $_paths = explode('/', $pathinfo_called);
            for ($i = count($_paths);$i > 0;$i--) {
                $a_p = array_pad([], $i, '../');
                $_target_path = join($a_p);
                $_dist_path = join('/', array_slice($_paths, 0, $i));
                $content = str_replace($_target_path, $_dist_path, $content);
            }
            $content = "\n/*! loaded {$path_called[$key]} = {$file_nm}*/\n{$content}\n\n/*! loaded {$path_called[$key]} = {$file_nm}*/\n\n\n";
            $minifier->add($content);
        }
        

        $minifier->minify(DIR_CACHE_JSCSS.$tagtype.'/'.$cache_file_nm);
        $cache_mtime = filemtime(DIR_CACHE_JSCSS.$tagtype.'/'.$cache_file_nm);
        return [$cache_file_nm,$cache_mtime];
    }

    /**
     * 파일 배열을 정리 하고 minifying 실행한다.
     * 캐시파일이 존재하면 원본 수정 시간과 비교, 원본이 최신이면 캐시(minify)를 다시 만든다. 아니면 캐시 파일명을 반환한다.
     */
    public function getSrcUrl($arr, $filetype)
    {
        $arr_tag_src = ['union'=>[],'alone'=>[],'defer'=>[],'async'=>[],'nominify'=>[]];     //  태그에 적용되는 파일 src
        $arr_path_real = $arr_tag_src;
        $arr_path_called = $arr_tag_src;
        $mtime_last_original = $arr_tag_src;
        $mtime_last_original['union'] = 0;

        foreach ($arr as $tagtype => $arr_items) {
            foreach ($arr_items as $key => $file) {
                if (! $file) {
                    continue;
                }
                $file_path_real = $this->getRealPath($file);
                if (!$file_path_real) {
                    continue;
                }
                $mtime = filemtime($file_path_real);
                
                if ($tagtype == 'union') {
                    if ($mtime > $mtime_last_original[$tagtype]) {
                        $mtime_last_original[$tagtype] = $mtime;
                    }
                } else {
                    $mtime_last_original[$tagtype][$key] = $mtime;
                }
                $arr_path_real[$tagtype][$key] = $file_path_real;
                $arr_path_called[$tagtype][$key] = $file;
            }
        }
        foreach ($arr_tag_src as $tagtype => $mtime) {
            if (count($arr_path_real[$tagtype]) == 0) {
                continue;
            }
            if ($tagtype == 'union') {
                $cache_file_nm = 'cached_'.md5(serialize($arr_path_called[$tagtype]));
                $cache_file_nm = $cache_file_nm.'.'.$filetype;
                $_arr_is_force = array_diff($this->arr_is_force, $arr_path_called[$tagtype]);
                if (file_exists(DIR_CACHE_JSCSS.$filetype.'/'.$cache_file_nm) && count($_arr_is_force) == count($arr_path_called[$tagtype])) {
                    $cache_mtime = filemtime(DIR_CACHE_JSCSS.$filetype.'/'.$cache_file_nm);
                    if ($mtime_last_original[$tagtype] <= $cache_mtime) {
                        $arr_tag_src[$tagtype][] = [$cache_file_nm, $cache_mtime];
                        continue;
                    }
                }
                $arr_tag_src[$tagtype][] = $this->Minify($arr_path_real[$tagtype], $filetype, $cache_file_nm, $arr_path_called[$tagtype]);
            } else {
                foreach ($arr_path_real[$tagtype] as $key => $path_real) {
                    $path_called = $arr_path_called[$tagtype][$key];
                    $file_nm_called = pathinfo($path_called, PATHINFO_BASENAME);
                    
                    if (strstr($file_nm_called, '.min.') || $tagtype == 'nominify') {
                        $cache_mtime = filemtime($path_real);
                        $arr_tag_src[$tagtype][] = [$arr_path_called[$tagtype][$key],$cache_mtime];
                        continue;
                    }
                    $cache_file_nm = 'cached_'.$file_nm_called.'.'.$filetype;
                    if (file_exists(DIR_CACHE_JSCSS.$filetype.'/'.$cache_file_nm) && !in_array($path_real, $this->arr_is_force)) {
                        $cache_mtime = filemtime(DIR_CACHE_JSCSS.$filetype.'/'.$cache_file_nm);
                        if ($mtime_last_original[$tagtype][$key] <= $cache_mtime) {
                            $arr_tag_src[$tagtype][] = [$cache_file_nm, $cache_mtime];
                            continue;
                        }
                    }
                    $arr_tag_src[$tagtype][] = $this->Minify($path_real, $filetype, $cache_file_nm, $arr_path_called[$tagtype][$key]);
                }
            }
        }
        return $arr_tag_src;
    }

    public function gcCaches()
    {
        foreach (['css','js'] as $filetype) {
            $dir_caches = DIR_CACHE_JSCSS.$filetype.'/';
            if ($dh = opendir($dir_caches)) {
                while (($filejs = readdir($dh)) !== false) {
                    if ($filejs == "." || $filejs == "..") {
                        continue;
                    }
                    $filetm = filemtime($dir_caches.$filejs);
                    if (time() - $filetm > WEEK) {
                        unlink($dir_caches.$filejs);
                    }
                }
                closedir($dh);
            }
        }
    }
}