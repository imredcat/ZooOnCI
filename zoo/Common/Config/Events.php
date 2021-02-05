<?php

use CodeIgniter\Events\Events;
use Zoo\Common\Services\LoginObserver;

Events::on('post_controller_constructor',function(){
  $login = new Zoo\Common\Services\LoginObserver();
  $login->checkLogin();

},1);