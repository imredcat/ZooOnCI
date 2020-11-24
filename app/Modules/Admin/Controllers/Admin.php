<?php namespace App\Modules\Admin\Controllers;
use App\Controllers\BaseController;
/**
 * Class Admin
 */
class Admin extends BaseController {
    public function index() {
      echo view('App\Modules\Admin\Views\coming_soon_view',['name'=>'Boy']);
    }
}