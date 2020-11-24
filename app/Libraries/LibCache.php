<?php
namespace App\Libraries;

use \Config\Cache as Cache;
use \Zoo\Config\ZooCache as ZooCache;
use \Zoo\Config\Services as ZooServices;

class LibCache extends ZooCache
{
    public $path;
    public $ttl = WEEK;
    public function __construct()
    {
        $this->path = $this->storePath."dmg/";
    }
    public function save($cache_nm, $contents,$options=[])
    {
        $pinfo = pathinfo($cache_nm);
        $file_nm = $pinfo['basename'];
        $file_path = $pinfo['dirname'];
        $arr_file_path = array_diff(explode("/",str_replace($this->path,$file_path)),Array('','..'));
        if (count($arr_file_path) > 0) {
            $file_path = $this->path;
            foreach ($arr_file_path as $key => $val) {
                $file_path = $file_path.$val."/";
                mkdir($file_path,0777);
            }
        }
        $file_path = $this->path.$cache_nm;
        $raw = isset($options['raw'])?$options['raw']:false;
        $ttl = isset($options['ttl'])?$options['ttl']:$this->ttl;
        if (!$raw) {
            $contents = [
                'time' => time(),
                'ttl'  => $ttl,
                'data' => $contents,
            ];
            $contents = serialize($contents);
            $file_path .= ".serialized";
        }else {
            $file_path .= ".raw";
        }

        if ( write_file($file_path, $contents) ) {
            chmod($file_path, 0640);
            return true;
        }
        return flase;
    }
    public function get($cache_nm)
    {
        return $this->getData($cache_nm);
    }
    public function getJson($cache_nm)
    {
        $data = $this->get($cache_nm);
        return @json_decode($data);
    }
    public function getArray($cache_nm)
    {
        $data = $this->get($cache_nm);
        return @json_decode($data,true);
    }

    public function getData($cache_nm,$datatype='')
    {
        $is_raw = true;
        if (is_file($this->path . $cache_nm.'.serialized')) {
            $filepath = $this->path . $cache_nm.'.serialized';
            $is_raw = false;
        }elseif (is_file($this->path . $cache_nm.'.raw')) {
            $filepath = $this->path . $cache_nm.'.raw';
        }elseif (is_file($this->path . $cache_nm)) {
            $filepath = $this->path . $cache_nm;
        }else {
            return false;
        }
        clearstatcache();
        $data = file_get_contents($filepath);
        if ($is_raw == true) {
            if (time() > filemtime($filepath) + $this->ttl ) {
                @unlink($filepath);
                return false;
            }
        }else {
            $data = unserialize($data);
            if ($data['ttl'] > 0 && time() > $data['time'] + $data['ttl'])
            {
                @unlink($filepath);
                return false;
            }
            $data = $data['data'];
        }
        return $data;
    }
    public function gc($gc_dir='',int $ttl = null)
    {
        $ttl = $ttl?$ttl:$this->ttl;
        $gc_dir = rtrim($gc_dir,'/');
        $gc_dir = $this->path.$gc_dir;
        clearstatcache();
        if ($dh = opendir($gc_dir)) {
            while (($item = readdir($dh)) !== false) {
                if ($item == "." || $item == "..") {
                    continue;
                }
                if (is_dir($gc_dir.$item)) {
                    $this->gc($gc_dir.$item,$ttl);
                }else if(is_file($gc_dir.$item)) {
                    $filetm = filemtime($gc_dir.$item);
                    if (time() - $item > $ttl) {
                        unlink($gc_dir.$item);
                    }
                }
            }
            closedir($dh);
        }
    }
}