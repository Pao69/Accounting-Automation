<?php
session_start();
require_once 'config/config.php';
require_once 'core/Router.php';
require_once 'core/Controller.php';
require_once 'core/Database.php';

// Initialize Router
$router = new Router();

// Define routes
$router->addRoute('/', 'DashboardController@index');
$router->addRoute('/journal', 'JournalController@index');
$router->addRoute('/journal/create', 'JournalController@create');
$router->addRoute('/ledger', 'LedgerController@index');
$router->addRoute('/financial-statements', 'FinancialStatementsController@index');
$router->addRoute('/financial-statements/income', 'FinancialStatementsController@incomeStatement');
$router->addRoute('/financial-statements/equity', 'FinancialStatementsController@equityStatement');
$router->addRoute('/financial-statements/balance', 'FinancialStatementsController@balanceSheet');
$router->addRoute('/closing', 'ClosingController@index');

// Handle the request
$router->dispatch(); 