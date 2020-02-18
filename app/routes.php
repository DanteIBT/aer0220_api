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

    // CORS requests
    $app->options($settings['basePath'] . '{routes:.+}', function ($request, $response, $args) {
        return $response;
    });

    // Students
    $app->group($settings['basePath'] . 'students', function (RouteCollectorProxy $group) use ($middlewares) {

        // View Students
        $group->get('', StudentController::class . ':getAll');
        $group->get('/{student_id}', StudentController::class . ':getStudent');
        // Insert Students / Create Payment
        $group->post('', StudentController::class . ':create');
    })->add($middlewares['authMiddleware']); // Token Validation

    // Payments
    $app->group($settings['basePath'] . 'payments', function (RouteCollectorProxy $group) use ($middlewares) {

        // View Payments
        $group->get('', PaymentController::class . ':getAll');
        $group->get('/{payment_id}', PaymentController::class . ':getPayment');
        // Update -> Payment Status
        $group->put('/{payment_id}', PaymentController::class . ':updatePayment');
    })->add($middlewares['authMiddleware']); // Token Validation

    // Users
    $app->group($settings['basePath'] . 'users', function (RouteCollectorProxy $group) use ($middlewares) {

        // View Users
        $group->get('', UserController::class . ':getAll');
        $group->get('/{user_id}', UserController::class . ':getUser');
        // Insert Users
        $group->post('', UserController::class . ':create');
    })->add($middlewares['authMiddleware']); // Token Validation

    // Catalogues
    $app->group($settings['basePath'] . 'cat/', function (RouteCollectorProxy $group) use ($middlewares) {
        // View Catalogues
        $group->get('cities', CatalogueController::class . ':getCities');
        $group->get('courses', CatalogueController::class . ':getCourses');
        $group->get('genders', CatalogueController::class . ':getGenders');
        $group->get('grade', CatalogueController::class . ':getGrade');
        $group->get('meet_us', CatalogueController::class . ':getMeetUs');
        $group->get('payment_status', CatalogueController::class . ':getPaymentStatus');
        $group->get('payment_type', CatalogueController::class . ':getPaymentType');
        $group->get('relationship', CatalogueController::class . ':getRelationship');
        $group->get('user_types', CatalogueController::class . ':getUserTypes');
    })->add($middlewares['authMiddleware']); // Token Validation

    // Count students

    // SignIn
    $app->post($settings['basePath'] . 'sign-in', UserController::class . ':authenticate');

    // CORS requests
    $app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], $settings['basePath'] . '{routes:.+}', function ($request, $response) {
        throw new HttpNotFoundException($request);
    });
};
