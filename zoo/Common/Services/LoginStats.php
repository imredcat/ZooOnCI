<?php namespace Zoo\Common\Sevices;

class LoginStats 
{
  public function isLogin()
  {
    return !empty($_SESSION['zoo_mem_info']);
  }
  
  public function isAdmin()
  {
    return !empty($_SESSION['zoo_admin']);
  }

  public function isMember()
  {
    return !empty($_SESSION['zoo_member']);
  }
}