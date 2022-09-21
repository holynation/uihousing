<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Equipro');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Equipro::index');

// The route for the application mobile API is here
$routes->group('api', ['filter' => 'apiAuth'], function ($routes) {
    $routes->add('(:any)', 'Api::mobileApi/$1');
    $routes->add('(:any)/(:any)', 'Api::mobileApi/$1/$2');
    $routes->add('(:any)/(:any)', 'Api::mobileApi/$1/$2/$3');
});

// The route for the application web API is here
$routes->group('webApi', ['filter' => 'apiAuth'], function ($routes) {
    $routes->add('(:any)', 'Api::webApi/$1');
    $routes->add('(:any)/(:any)', 'Api::webApi/$1/$2');
    $routes->add('(:any)/(:any)', 'Api::webApi/$1/$2/$3');
});

// The route for normal operations for the backend
$routes->group('vc/create', function ($routes) {
    $routes->add('(:any)', 'Viewcontroller::create/$1');
    $routes->add('(:any)/(:any)', 'Viewcontroller::create/$1/$2');
    $routes->add('(:any)/(:any)/(:any)', 'Viewcontroller::create/$1/$2/$3');
    $routes->add('(:any)/(:any)/(:any)/(:any)', 'Viewcontroller::create/$1/$2/$3/$4');
});

$routes->group('vc', function ($routes) {
    $routes->add('resetPassword/(:any)', 'Viewcontroller::resetPassword/$1');
    $routes->add('changePassword', 'Viewcontroller::changePassword');
    $routes->add('changePassword/(:any)', 'Viewcontroller::changePassword/$1');
    $routes->add('(:any)', 'Viewcontroller::view/$1');
    $routes->add('(:any)/(:any)', 'Viewcontroller::view/$1/$2');
    $routes->add('(:any)/(:any)/(:any)', 'Viewcontroller::view/$1/$2/$3');
});

$routes->add('edit/(:any)/(:any)', 'Viewcontroller::edit/$1/$2');
$routes->add('ajaxData/savePermission', 'Ajaxdata::savePermission');

$routes->group('mc', function ($routes) {
    $routes->add('add/(:any)/(:any)', 'Modelcontroller::add/$1/$2');
    $routes->add('add/(:any)/(:any)/(:any)/(:any)', 'Modelcontroller::add/$1/$2/$3/$4');
    $routes->add('update/(:any)/(:any)/(:any)', 'Modelcontroller::update/$1/$2/$3');
    $routes->add('update/(:any)/(:any)/(:any)/(:any)', 'Modelcontroller::update/$1/$2/$3/$4');
    $routes->add('delete/(:any)/(:any)', 'Modelcontroller::delete/$1/$2');
    $routes->add('template/(:any)', 'Modelcontroller::template/$1');
    $routes->add('export/(:any)', 'Modelcontroller::export/$1');
    $routes->add('sFile/(:any)', 'Modelcontroller::modelFileUpload/$1');
});

$routes->group('ac', function ($routes) {
    $routes->add('disable/(:any)/(:any)', 'Actioncontroller::disable/$1/$2');
    $routes->add('enable/(:any)/(:any)', 'Actioncontroller::enable/$1/$2');
});

$routes->add('delete/(:any)/(:any)', 'Actioncontroller::delete/$1/$2');
$routes->add('delete/(:any)/(:any)/(:any)', 'Actioncontroller::delete/$1/$2/$3');
$routes->add('truncate/(:any)', 'Actioncontroller::truncate/$1');
$routes->add('mail/(:any)/(:any)', 'Actioncontroller::mail/$1/$2');
$routes->add('changestatus/(:any)/(:any)/(:any)', 'Actioncontroller::changeStatus/$1/$2/$3');

$routes->add('account/verify/(:any)/(:any)/(:any)', 'Auth::verify/$1/$2/$3');
$routes->add('account/verifyTransaction/(:any)', 'Auth::verifyTransaction/$1');
$routes->add('register', 'Auth::signup');
$routes->add('login', 'Auth::login');
$routes->add('logout', 'Auth::logout');
$routes->add('forget_password', 'Auth::forget');
$routes->add('auth/web', 'Auth::web');

$routes->add('admin/dashboard', 'Viewcontroller::view/admin/dashboard');
$routes->add('uploaded/(:any)/(:any)', 'Api::accessFiles/$1/$2');

// this route is used for webhook
$routes->add('paystack_pay_verify', 'Authhook::verifyTransaction/paystack');
$routes->add('init_payment', 'Authhook::init_payment');
$routes->add('stripe_pay_verify', 'Authhook::verifyTransaction/stripe');



/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
