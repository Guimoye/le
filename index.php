<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('t_base.php');
include('inc/router.php');

$route = new Route();
$route->add('', 'home', 'index');

//$route->add('expenses/:num',        'expenses', 'index');

/*$route->add('foo/:any/:any/bar/:num',       'foo', 'bar');

$route->add('settings',                         'settings', 'index');
$route->add('settings/edit_general',            'settings', 'edit_general');

$route->add('settings/users',                   'users', 'index');
$route->add('settings/users/pager',             'users', 'pager');
$route->add('settings/users/add',               'users', 'add');
$route->add('settings/users/remove',            'users', 'remove');

$route->add('settings/levels',                  'page', 'index');*/
$route->add(':any', '%', '%');

$route->send();