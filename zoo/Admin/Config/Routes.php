<?php

if (!isset($routes)) {
    $routes = \Config\Services::routes(true);
}


$routes->group('admin', ['namespace' => '\Zoo\Admin\Controllers'], function ($routes) {
    $routes->add('/', 'Admin::index');
    $routes->add('members', 'Members::index');
    $routes->add('members/info', 'Members::info');
    $routes->match(['get','post'], 'members/department/', 'Members::department');
    $routes->match(['get','post'], 'members/company', 'Members::company');
    $routes->match(['get','post'], 'members/companyuse', 'Members::companyuse');
    $routes->add('login', 'Login::index');
    $routes->add('logout', 'Admin::logout');
});