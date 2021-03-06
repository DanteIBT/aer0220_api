<?php

use Slim\App;
use App\Controllers\UserController;
use Slim\Routing\RouteCollectorProxy;
use App\Controllers\PaymentController;
use App\Controllers\StudentController;
use App\Controllers\CatalogueController;
use Slim\Exception\HttpNotFoundException;

return function (App $app, array $middlewares) {

    // Call container
    $container = $app->getContainer();
    $settings = $container->get('settings');

    define('__BASE_PATH__', $settings['basePath']);

    // CORS requests
    $app->options( __BASE_PATH__ . '{routes:.+}', function ($request, $response, $args) {
        return $response;
    });

    // Students
    $app->group( __BASE_PATH__ . 'students/', function (RouteCollectorProxy $group) use ($middlewares) {

        // View Students
        $group->get('', StudentController::class . ':getAll')->add($middlewares['authMiddleware']);                                       // Token Validation
        $group->get('{student_id}', StudentController::class . ':getStudent')->add($middlewares['authMiddleware']);                       // Token Validation
        // Count
        $group->get('count/', StudentController::class . ':getCount')->add($middlewares['authMiddleware']);                               // Total students enrolled
        $group->get('courses/{courses_id}', StudentController::class . ':getStudentsCourses')->add($middlewares['authMiddleware']);       // Total students in course
        $group->get('amount/{courses_id}', StudentController::class . ':getAmountCourses')->add($middlewares['authMiddleware']);          // Total amount per course
        $group->get('age/{courses_id}', StudentController::class . ':getBirthDate')->add($middlewares['authMiddleware']);                 // Total Age / Average
        $group->get('registration/{courses_id}', StudentController::class . ':getLastRegistration')->add($middlewares['authMiddleware']); // Last Registration

        // Insert Students / Create Payment
        $group->post('', StudentController::class . ':create');
    });

    // Payments
    $app->group( __BASE_PATH__ . 'payments/', function (RouteCollectorProxy $group) use ($middlewares) {

        // View Payments
        $group->get('', PaymentController::class . ':getAll');
        $group->get('{payment_id}', PaymentController::class . ':getPayment');
        // Count
        $group->get('amount/', PaymentController::class . ':getTotalAmount'); // Total amount
        // Update -> Payment Status
        $group->put('{student_id}', PaymentController::class . ':updatePayment');
    })->add($middlewares['authMiddleware']); // Token Validation

    // Users
    $app->group( __BASE_PATH__ . 'users/', function (RouteCollectorProxy $group) use ($middlewares) {

        // View Users
        $group->get('', UserController::class . ':getAll');
        $group->get('{user_id}', UserController::class . ':getUser');
        // Count
        $group->get('count/', UserController::class . ':getCount'); // Total users enrolled
        // Insert Users
        $group->post('', UserController::class . ':create');
    })->add($middlewares['authMiddleware']); // Token Validation

    // Catalogues
    $app->group( __BASE_PATH__ . 'catalogues/', function (RouteCollectorProxy $group) use ($middlewares) {
        // View Catalogues
        $group->get('cities', CatalogueController::class . ':getCities');
        $group->get('courses', CatalogueController::class . ':getCourses');
        $group->get('genders', CatalogueController::class . ':getGenders');
        $group->get('grades', CatalogueController::class . ':getGrades');
        $group->get('meet_us', CatalogueController::class . ':getMeetUs');
        $group->get('payments_status', CatalogueController::class . ':getPaymentsStatus');
        $group->get('payment_types', CatalogueController::class . ':getPaymentTypes');
        $group->get('relationships', CatalogueController::class . ':getRelationships');
        $group->get('user_types', CatalogueController::class . ':getUserTypes');
    }); // Dont Need Token Validation

    // SignIn
    $app->post( __BASE_PATH__ . 'sign-in', UserController::class . ':authenticate');

    // CORS requests
    $app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'],  __BASE_PATH__ . '{routes:.+}', function ($request, $response) {
        throw new HttpNotFoundException($request);
    });
};
