<?php
namespace Zoo\Common\Traits;

trait GetPassword
{
  public function getPasswd(String $str): String
  {
    $str = md5($str);
    return $this->db->query("select password('{$str}') as str")->getRow()->str;
  }
}