<?php namespace Zoo\Common\Models;

use CodeIgniter\Model;
use \Zoo\Config\Services as DmgServices;

class BaseModel extends Model
{
  public $sess_data;
  public function __construct()
  {
    parent::__construct();
    $this->sess_data = $_SESSION;
  }

  public function rmCachedByModel($path_cache)
  {
    $cache = ZooServices::libcache();
    return $cache->rm($path_cache);
  }
}