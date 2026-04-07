<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/userguide3/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'auth';
$route['login'] = 'auth/login';
$route['register'] = 'auth/register';
$route['admin/login'] = 'auth/admin_login';
$route['logout'] = 'auth/logout';
$route['api/login'] = 'api/auth/login';
$route['api/invoices'] = 'api/invoices/index';
$route['api/receipts'] = 'api/receipts/index';
$route['cart'] = 'cart/view';
$route['cart/add/(:num)'] = 'cart/add/$1';
$route['cart/update'] = 'cart/update';
$route['cart/remove/(:num)'] = 'cart/remove/$1';
$route['cart/checkout'] = 'cart/checkout';
$route['products'] = 'products/index';
$route['user/invoices'] = 'user/invoices/index';
$route['user/invoices/view/(:num)'] = 'user/invoices/view/$1';
$route['user/receipts'] = 'user/invoices/receipts';
$route['user/receipts/view/(:num)'] = 'user/invoices/view_receipt/$1';
$route['payment/create-checkout/(:num)'] = 'payment/create_checkout/$1';
$route['payment/success'] = 'payment/success';
$route['payment/cancel'] = 'payment/cancel';
$route['payment/webhook'] = 'payment/webhook';
$route['admin'] = 'admin/dashboard';
$route['admin/dashboard'] = 'admin/dashboard';
$route['admin/invoices'] = 'admin/invoices/index';
$route['admin/invoices/view/(:num)'] = 'admin/invoices/view/$1';
$route['admin/receipts'] = 'admin/invoices/receipts';
$route['admin/receipts/view/(:num)'] = 'admin/invoices/view_receipt/$1';
$route['admin/users/create'] = 'admin/users/create';
$route['admin/products'] = 'admin/products/index';
$route['admin/products/create'] = 'admin/products/create';
$route['admin/products/edit/(:num)'] = 'admin/products/edit/$1';
$route['admin/products/delete/(:num)'] = 'admin/products/delete/$1';
$route['user/dashboard'] = 'account/index';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
