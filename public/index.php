<?php

require_once dirname(__DIR__).'/vendor/autoload.php';

$loader = new \Twig\Loader\ArrayLoader();
$twig = new \Twig\Environment($loader);

error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');

session_start();

$router = new Core\Router();

$router->add('', ['controller' => 'Home', 'action' => 'index']);
$router->add('{controller}/{action}');
$router->add('{controller}/{id:\d+}/{action}');
$router->add('signup', ['controller' => 'Signup', 'action' => 'new']);
$router->add('signup/activate/{token:[\da-f]+}', ['controller' => 'Signup', 'action' => 'activate']);
$router->add('login', ['controller' => 'Login', 'action' => 'new']);
$router->add('menu', ['controller' => 'Menu', 'action' => 'main']);
$router->add('logout', ['controller' => 'Login', 'action' => 'destroy']);
$router->add('password/reset/{token:[\da-f]+}', ['controller' => 'Password', 'action' => 'reset']);
$router->add('income', ['controller' => 'Income', 'action' => 'new']);
$router->add('incomeCategories', ['controller' => 'Income', 'action' => 'displayIncomeCategories']);
$router->add('expenseCategories', ['controller' => 'Expense', 'action' => 'displayExpenseCategories']);
$router->add('paymentMethods', ['controller' => 'Setting', 'action' => 'displayPaymentMethods']);

    
$router->dispatch($_SERVER['QUERY_STRING']); 
