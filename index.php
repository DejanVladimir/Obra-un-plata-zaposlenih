<?php
error_reporting(E_ALL);
ini_set('display_errors', true);
ob_start();
session_start();

require 'vendor/autoload.php';

$database = new \MyApp\Helpers\Database(
	\MyApp\Configuration::DATABASE_HOST,
	\MyApp\Configuration::DATABASE_USER,
	\MyApp\Configuration::DATABASE_PASS,
	\MyApp\Configuration::DATABASE_NAME
);

$database->connect();

$data = [];

$data['database'] = $database;
$data['username'] = \MyApp\LoginSystem::getUsername();
$data['www'] = \MyApp\Configuration::WWW;
$data['app_name'] = _i18n(\MyApp\Configuration::APP_NAME);

$router = new \MyApp\Router($data);

$router->bind('/', 'HomeController');
$router->bind('/login', 'LoginController');
$router->bind('/logout', 'LogoutController');
$router->bind('/benefits', 'BenefitsController');
$router->bind('/benefits/add', 'AddBenefitsController');
$router->bind('/benefits/@id/edit', 'EditBenefitsController');
$router->bind('/benefits/@id/delete', 'DeleteBenefitsController');
$router->bind('/paygrades', 'PayGradesController');
$router->bind('/paygrades/add', 'AddPayGradesController');
$router->bind('/paygrades/@id/edit', 'EditPayGradesController');
$router->bind('/paygrades/@id/delete', 'DeletePayGradesController');
$router->bind('/employees', 'EmployeesController');
$router->bind('/employees/add', 'AddEmployeeController');
$router->bind('/employees/@id/edit', 'EditEmployeeController');
$router->bind('/employees/@id/delete', 'DeleteEmployeeController');
$router->bind('/workhours', 'WorkHoursController');
$router->bind('/workhours/add/@type', 'AddWorkHoursController');
$router->bind('/workhours/@id/edit', 'EditWorkHoursController');
$router->bind('/workhours/@id/delete', 'DeleteWorkHoursController');
$router->bind('/reports', 'ReportsController');
$router->bind('/reports/@id/@date', 'ViewReportController');
$router->bind('/users', 'UsersController');
$router->bind('/users/add', 'AddUserController');
$router->bind('/users/@id/edit', 'EditUserController');
$router->bind('/users/@id/delete', 'DeleteUserController');
$router->bind('/payments', 'PaymentsController');
$router->bind('/payments/add', 'AddPaymentController');
$router->bind('/payments/@id/edit', 'EditPaymentController');
$router->bind('/payments/@id/delete', 'DeletePaymentController');

$router->run();

$database->close();