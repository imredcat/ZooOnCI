<?php namespace Zoo\Admin\Controllers;

use CodeIgniter\Controller as Controller;
use \Zoo\Common\Config\Services as ZooServices;
use \Zoo\Common\Controllers\BaseController as BaseController;

class Login extends BaseController
{
  public function index()
  {
    $auth = service('auth');  
    $view = \Config\Services::renderer();
    $view->data_get = $this->data_get;
    $view->data_post = $this->data_post;
    $view->data_getpost = $this->data_getpost;
    $view->data_server = $this->data_server;
    $view->data_cookie = $this->data_cookie;
    $view->called_method = $this->called_method;
    $view->called_controller = $this->called_controller;
    $this->mdl_login = $this->loadModel('Zoo\Common\Models\MdlLogin');
    $data['login_status'] = 'LOGIN_FORM';
    if ($this->request_method == 'post') {
      $result = $this->mdl_login->login($view->data_post);
      if ($result == 'LOGIN_OK') {
        $go_url = $view->data_post['gourl']??'/';
        header("location:$go_url");
        exit();
      }
      $data['login_status'] = $result;
    }

    $minifyjscs = ZooServices::libminifyjscss();
    $data['html_head_css_js'] = $minifyjscs->loadjscss($data['html_head_css_js']);
    $saveData = config(View::class)->saveData;
    $options = [];
    $options['saveData'] = 1;
    if (array_key_exists('saveData', $options)) {
        $saveData = (bool) $options['saveData'];
        unset($options['saveData']);
    }

    return $view->setData($data, 'raw')
                ->render('Zoo\Admin\Views\loginform', $options, $saveData);
  }
}