<?php namespace Zoo\Admin\Controllers;

use Zoo\Common\Controllers\BaseController as BaseController;

class AdminController extends BaseController
{

  /**
   * 로그인 상태 확인
   */
  public function checkLoginStat()
  {
    if (!$this->mdl_login) {
      $this->mdl_login = loadModel('\Zoo\Common\Models\MdlLogin');
    }
    if ($this->request->uri->getSegment(1) == 'admin' &&
      $this->request->uri->getSegment(2) != 'login' &&
      $this->mdl_login->isLogin() == false) {
      
      header("location:/admin/login/");
      exit();
    }
    if ($this->request->uri->getSegment(1) == 'admin' &&
    $this->request->uri->getSegment(2) == 'login' &&
    $this->mdl_login->isLogin() == true) {
      header("location:/admin/");
      exit();
    }
  }
}