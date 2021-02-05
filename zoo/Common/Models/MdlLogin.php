<?php namespace Zoo\Common\Models;

use CodeIgniter\Model;
use \Zoo\Common\Models\BaseModel as BaseModel;

class MdlLogin extends BaseModel
{
  use \Zoo\Common\Traits\GetPassword;
  protected $table    = TBL_MEMBER;
  protected $primaryKey = 'mem_pk';

  public function isLogin()
  {
    return !empty($_SESSION['zoo_mem_info']);
  }

  public function login(array $data):String
  {
    if (empty($data['loginid']) || empty($data['loginpwd'])) {
      return "LOGIN_DATA_EMPTY";
    }
    $loginid = $this->getPasswd($data['loginid']);
    $loginpwd = $this->getPasswd($data['loginpwd']);

    $qry = "select mem_pk,mem_id,mem_nm from ".$this->table." where password(md5(mem_id)) = '{$loginid}' and mem_pwd = '{$loginpwd}'";
    $row = $this->db->query($qry)->getRow();
  
    if (! $row) {
      return "LOGIN_INVALID";
    }
    if ($data['loginkey'] != $login_key) {
      //return "LOGIN_INVALID_AUTHKEY";
    }
    $mem_pk = $row->mem_pk;

    $sess = $this->db->table(TBL_SESSION);
    $sess->update(['mem_pk' => $mem_pk,'admin_pk'=>$mem_pk], ["id"=>session_id()]);
    $_SESSION['zoo_admin'] = md5($mem_pk);
    $_SESSION['zoo_member'] = md5($mem_pk);
    $_SESSION['zoo_mem_info'] = json_encode($row);
    return "LOGIN_OK";
  }
  
  public function logout()
  {
    $tbl_sess = $this->db->table(TBL_SESSION);
    $tbl_sess->update(['admin_pk' => 0], ["id"=>session_id()]);
    unset($_SESSION['dmg_admin']);
    $sess = \Config\Services::session();
    $sess->destroy();
  }
}