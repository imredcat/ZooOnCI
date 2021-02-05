<?php namespace Zoo\Member\Models;

use \Zoo\Common\Models\BaseModel as BaseModel;

class MdlMember extends BaseModel
{
    use \Zoo\Common\Traits\GetPassword;
    protected $table      = TBL_MEMBER;
    protected $primaryKey = 'admin_pk';

    public function isLogin()
    {
        return !empty($_SESSION['zoo_mem_info']);
    }

    public function login(array $data):String
    {
        $login_key = date("Ymd")."dmg";
        if (empty($data['loginid']) || empty($data['loginpwd']) || empty($data['loginkey'])) {
            return "LOGIN_DATA_EMPTY";
        }
        $loginid = $this->getPasswd($data['loginid']);
        $loginpwd = $this->getPasswd($data['loginpwd']);

        $qry = "select admin_pk from ".TBL_ADMIN." where password(md5(admin_id)) = '{$loginid}' and admin_pwd = '{$loginpwd}'";
        $row = $this->db->query($qry)->getRow();
    
        if (! $row) {
            return "LOGIN_INVALID";
        }
        if ($data['loginkey'] != $login_key) {
            //return "LOGIN_INVALID_AUTHKEY";
        }
        $admin_pk = $row->admin_pk;

        $sess = $this->db->table(TBL_SESSION);
        $sess->update(['admin_pk' => $admin_pk], ["id"=>session_id()]);
        $_SESSION['dmg_admin'] = $admin_pk;
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