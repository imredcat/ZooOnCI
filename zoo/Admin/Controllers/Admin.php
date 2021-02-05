<?php namespace Zoo\Admin\Controllers;

use Zoo\Admin\Controllers\AdminController as AdminController;
use Zoo\Common\Controllers\BaseController as BaseController;

class Admin extends AdminController
{
  public function index()
  {
    $view = \Config\Services::renderer();
    $data = [];
    $data['html_head_css_js'] = array_merge(
      ['_mod_/admin/assets/css/admin_colors.css',
          '_mod_/admin/assets/css/admin_layout.css',
          '_mod_/admin/assets/css/form.css'],
      $data['html_head_css_js']?:[]
    );

    $minifyjscs = \Zoo\Common\Config\Services::libminifyjscss();
    $data['html_head_css_js'] = $minifyjscs->loadjscss($data['html_head_css_js']);
    
    return $view->setData($data, 'raw')
                ->render('Zoo\Admin\Views\layout', $options, $saveData);
  }
}