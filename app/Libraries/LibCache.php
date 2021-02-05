<?php
namespace App\Libraries;

use \Config\Cache as Cache;
use \Zoo\Config\ZooCache as ZooCache;
use \Zoo\Config\Services as ZooServices;

/**
 * 캐시 관리
 */

class LibCache extends ZooCache
{
    public $path;
    public $ttl = WEEK;
    public function __construct()
    {
        $this->path = $this->storePath."dmg/";
        helper('filesystem');
        helper('string');
    }


    /**
     * 캐시 저장하기
     * 캐시명은 캐시 저장 경로를 포함할 수 있으며, 경로는 호출되는 컨트롤러 모델 등의 namespace를 참고하여 작성할 것
     *
     * @param string    $cache_nm   캐시파일 명 (경로를 포함하면 해당 경로의 폴더를 생성하여 저장함 eg. Company/Comapnylist => cache/Company/Companylist 파일 생성)
     * @param mixed     $contents   캐시 내용
     * @param array     $options    옵션 raw : 캐시 내용 원본 유지 여부 1:유지, 0:serialized,$contents 스트링이면 자동으로 유지 | ttl : 캐시 라이프 타임 ,
     *                              params : 메쏘드 호출시 요청된 파라메터
     *
     * @return bool
     */
    public function save($cache_nm, $contents, $options=[])
    {
        $cache_nm = str_replace("::", DIRECTORY_SEPARATOR, $cache_nm);
        $pinfo = pathinfo($cache_nm);
        $file_nm = $pinfo['basename'];
        $file_path = $pinfo['dirname'];
        $arr_file_path = array_diff(preg_split("/[\/\\\]/", str_replace($this->path, '', $file_path)), array('','..'));
        if (! is_dir($this->path)) {
            mkdir($this->path, 0777);
        }
        $file_path = $this->path;
        if (count($arr_file_path) > 0) {
            foreach ($arr_file_path as $key => $val) {
                $file_path = $file_path.$val."/";
                if (!is_dir($file_path)) {
                    mkdir($file_path, 0777);
                }
            }
        }
        
        //$file_path = $this->path.$cache_nm;
        $file_path = rtrim($file_path, DIRECTORY_SEPARATOR).'/'.$file_nm;
        $params = $options['params'];
        if ($params) {
            $params = $options['params'];
            $file_path.=".".md5(serialize($params));
            $options['raw'] = false;
        }
        $raw = isset($options['raw'])?$options['raw']:false;
        $raw = is_string($contents) ? $raw:false;
        $ttl = isset($options['ttl'])?$options['ttl']:$this->ttl;
        if (!$raw) {
            $contents = [
                'time' => time(),
                'ttl'  => $ttl,
                'params' => $params,
                'data' => $contents,
            ];
            $contents = serialize($contents);
            $file_path .= ".serialized";
        } else {
            $file_path .= ".raw";
        }
        $result = write_file($file_path, $contents);
        if ($result) {
            chmod($file_path, 0640);
            return true;
        }
        return false;
    }
    public function get($cache_nm, $params)
    {
        $data = $this->getData($cache_nm, $params);
        return $data ?? false;
    }
    public function getObject($cache_nm, $params)
    {
        $data = $this->get($cache_nm, $params);
        if (is_object($data)) {
            return $data;
        }
        if (is_array($data)) {
            return json_decode(zoo_json_encode($data));
        }
        return $data ? @json_decode($data) : false;
    }
    public function getArray($cache_nm, $params)
    {
        $data = $this->get($cache_nm, $params);
        if (is_array($data)) {
            return $data;
        }
        if (is_object($data)) {
            return json_decode(zoo_json_encode($data), true);
        }
        return $data ? @json_decode($data, true) : false;
    }

    public function getData($cache_nm, $params)
    {
        $cache_nm = str_replace("::", DIRECTORY_SEPARATOR, $cache_nm);
        $pinfo = pathinfo($cache_nm);
        $file_nm = $pinfo['basename'];
        $file_path = $pinfo['dirname'];
        $arr_file_path = array_diff(preg_split("/[\/\\\]/", str_replace($this->path, '', $file_path)), array('','..'));
        $file_path = join(DIRECTORY_SEPARATOR, $arr_file_path);
        $cache_nm = rtrim($file_path, DIRECTORY_SEPARATOR).'/'.$file_nm;
        if ($params) {
            $cache_nm .= ".".md5(serialize($params));
        }

        $is_raw = true;
        if (is_file($this->path . $cache_nm.'.serialized')) {
            $filepath = $this->path . $cache_nm.'.serialized';
            $is_raw = false;
        } elseif (is_file($this->path . $cache_nm.'.raw')) {
            $filepath = $this->path . $cache_nm.'.raw';
        } elseif (is_file($this->path . $cache_nm)) {
            $filepath = $this->path . $cache_nm;
        } else {
            return false;
        }
        clearstatcache();
        $data = file_get_contents($filepath);
        if ($is_raw == true) {
            if (time() > filemtime($filepath) + $this->ttl) {
                @unlink($filepath);
                return false;
            }
        } else {
            $data = unserialize($data);
            if ($data['ttl'] > 0 && time() > $data['time'] + $data['ttl']) {
                @unlink($filepath);
                return false;
            }
            $data = $data['data'];
        }
        return $data;
    }
    public function gc($gc_dir='', int $ttl = null)
    {
        $ttl = $ttl?$ttl:$this->ttl;
        $gc_dir = rtrim($gc_dir, '/');
        $gc_dir = $this->path.$gc_dir;
        clearstatcache();
        if ($dh = opendir($gc_dir)) {
            while (($item = readdir($dh)) !== false) {
                if ($item == "." || $item == "..") {
                    continue;
                }
                if (is_dir($gc_dir.$item)) {
                    $this->gc($gc_dir.$item, $ttl);
                } elseif (is_file($gc_dir.$item)) {
                    $filetm = filemtime($gc_dir.$item);
                    if (time() - $item > $ttl) {
                        unlink($gc_dir.$item);
                    }
                }
            }
            closedir($dh);
        }
    }
    /**
     * TODO: 요청된 캐시 삭제
     */
    
    public function rm($cache_nm)
    {
        $cache_nm = ltrim($cache_nm,'/');
        $cache_nm = ltrim($cache_nm,'\\');
        $cache_nm = str_replace($this->path,'',$cache_nm);
        $gc_dir = $this->path.$cache_nm;
        if (is_file($gc_dir)) {
            unlink($gc_dir);
            return ;
        }
        $gc_dir = $gc_dir.DIRECTORY_SEPARATOR;
        if ($dh = opendir($gc_dir)) {
            while (($item = readdir($dh)) !== false) {
                if ($item == "." || $item == "..") {
                    continue;
                }
                if (is_dir($gc_dir.$item)) {
                    $this->rm($gc_dir.$item);
                } elseif (is_file($gc_dir.$item)) {
                    unlink($gc_dir.$item);
                }
            }
            closedir($dh);
            rmdir($gc_dir);
        }
        return ;
    }
}