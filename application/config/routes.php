<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'main/view';
$route['(:any)'] = 'main/$1';
$route['(:any)/(:any)'] = 'main/$1/$2';

$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
