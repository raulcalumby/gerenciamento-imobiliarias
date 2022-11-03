<?php

/**
 * Front controller
 *
 * PHP version 7.0
 */




$time_session = 3600 * 48;
ini_set('session.gc_maxlifetime', $time_session);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");

session_set_cookie_params($time_session);
session_start();

date_default_timezone_set("America/Sao_Paulo");
setlocale(LC_ALL, NULL);
setlocale(LC_ALL, 'pt_BR');
setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');


require dirname(__DIR__) . '/vendor/autoload.php';



error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');



$router = new Core\Router();



//Login
$router->add('login', ['controller' => 'Login', 'action' => 'index']);
$router->add('auth', ['controller' => 'Login', 'action' => 'auth']);
$router->add('signup', ['controller' => 'Login', 'action' => 'signUp']);


//Logout
$router->add('logout', ['controller' => 'Me', 'action' => 'logout']);
$router->add('sair', ['controller' => 'Me', 'action' => 'logout']);

//Admin
$router->add('admin/users',  ['controller' => 'Users', 'namespace' => 'Admin',  'action' => 'index']);
$router->add('admin/user/{id:\d+}',  ['controller' => 'Users', 'namespace' => 'Admin',  'action' => 'edit']);
$router->add('admin/users/new', ['controller' => 'Users', 'namespace' => 'Admin', 'action' => 'new']);
$router->add('admin/logs', ['controller' => 'Logs', 'namespace' => 'Admin', 'action' => 'index']);

//User Actions
$router->add('api/users/add', ['controller' => 'AccountActions', 'action' => 'addUser']);
$router->add('api/users/edit/pass', ['controller' => 'AccountActions', 'action' => 'editPassAccount']);
$router->add('api/users/disable',  ['controller' => 'Users', 'namespace' => 'Admin',  'action' => 'disable']);
$router->add('api/users/{id:\d+}/update',  ['controller' => 'Users', 'namespace' => 'Admin',  'action' => 'update']);
$router->add('api/users/simplelist',  ['controller' => 'Users', 'namespace' => 'Admin',  'action' => 'SimpleList']);

// Me Actions
$router->add('me', ['controller' => 'Me', 'action' => 'index']);
$router->add('me/pass', ['controller' => 'Me', 'action' => 'index']);

// Home or Dashboard
$router->add('', ['controller' => 'Home', 'action' => 'index']);
$router->add('dashboard', ['controller' => 'Home', 'action' => 'index']);
$router->add('home', ['controller' => 'Home', 'action' => 'index']);
$router->add('index', ['controller' => 'Home', 'action' => 'index']);

//Propietários
$router->add('proprietarios', ['controller' => 'Proprietario', 'action' => 'index']);
$router->add('api/proprietarios/list', ['controller' => 'Proprietario', 'action' => 'list']);
$router->add('api/proprietarios/simplelist', ['controller' => 'Proprietario', 'action' => 'simpleList']);
$router->add('api/proprietarios/add', ['controller' => 'Proprietario', 'action' => 'add']);
$router->add('api/proprietarios/{id:\d+}/update', ['controller' => 'Proprietario', 'action' => 'update']);
$router->add('api/proprietarios/disable', ['controller' => 'Proprietario', 'action' => 'disable']);

//Imoveis
$router->add('imoveis', ['controller' => 'Imoveis', 'action' => 'index']);
$router->add('api/imoveis/dados', ['controller' => 'Imoveis', 'action' => 'getPropertiesById']);
$router->add('api/imoveis/add', ['controller' => 'Imoveis', 'action' => 'add']);
$router->add('api/imoveis/upload/photo', ['controller' => 'Imoveis', 'action' => 'uploadPhoto']);
$router->add('api/imoveis/list', ['controller' => 'Imoveis', 'action' => 'list']);
$router->add('api/imoveis/simplelist', ['controller' => 'Imoveis', 'action' => 'simpleList']);
$router->add('api/imoveis/available', ['controller' => 'Imoveis', 'action' => 'availableProperties']);
$router->add('api/imoveis/disable', ['controller' => 'Imoveis', 'action' => 'disable']);
$router->add('api/imoveis/{id:\d+}/update', ['controller' => 'Imoveis', 'action' => 'update']);
$router->add('api/imoveis/proprietario', ['controller' => 'Imoveis', 'action' => 'defineProprietario']);
$router->add('api/imoveis/proprietario/list', ['controller' => 'Imoveis', 'action' => 'listImoveisByProprietarioId']);

//Indices CRUD
$router->add('indices', ['controller' => 'Indices', 'action' => 'index']);
$router->add('api/indices/list', ['controller' => 'Indices', 'action' => 'list']);
$router->add('api/indices/simplelist', ['controller' => 'Indices', 'action' => 'simpleList']);
$router->add('api/indices/add', ['controller' => 'Indices', 'action' => 'add']);
$router->add('api/indices/{id:\d+}/update', ['controller' => 'Indices', 'action' => 'update']);
$router->add('api/indices/disable', ['controller' => 'Indices', 'action' => 'disable']);


// Locação CRUD
$router->add('locacao', ['controller' => 'Locacao', 'action' => 'index']);
$router->add('api/locacao/list', ['controller' => 'Locacao', 'action' => 'list']);
$router->add('api/locacao/add', ['controller' => 'Locacao', 'action' => 'add']);
$router->add('api/locacao/upload/photo', ['controller' => 'Locacao', 'action' => 'addPhoto']);
$router->add('api/locacao/{id:\d+}/update', ['controller' => 'Locacao', 'action' => 'update']);
$router->add('api/locacao/disable', ['controller' => 'Locacao', 'action' => 'disable']);

//Locatários
$router->add('locatarios', ['controller' => 'Locatarios', 'action' => 'index']);
$router->add('api/locatarios/list', ['controller' => 'Locatarios', 'action' => 'list']);
$router->add('api/locatarios/simplelist', ['controller' => 'Locatarios', 'action' => 'simpleList']);
$router->add('api/locatarios/add', ['controller' => 'Locatarios', 'action' => 'add']);
$router->add('api/locatarios/{id:\d+}/update', ['controller' => 'Locatarios', 'action' => 'update']);
$router->add('api/locatarios/disable', ['controller' => 'Locatarios', 'action' => 'disable']);

/* Leads */
$router->add('leads', ['controller' => 'Leads', 'action' => 'index']);
$router->add('api/leads/add', ['controller' => 'Leads', 'action' => 'add']);
$router->add('api/leads/list', ['controller' => 'Leads', 'action' => 'list']);
$router->add('api/leads/disable', ['controller' => 'Leads', 'action' => 'disable']);
$router->add('api/leads/{id:\d+}/update', ['controller' => 'Leads', 'action' => 'update']);

//Rotas Padrão
$router->add('{controller}/{action}');
$router->add('{controller}/{id:\d+}/{action}');
$router->dispatch($_SERVER['QUERY_STRING']);
