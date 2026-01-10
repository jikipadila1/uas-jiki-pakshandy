<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// route default - redirect to login page
$routes->get('/', 'Home::index');

// Grup API dengan namespace khusus
$routes->group('api', ['namespace' => 'App\Controllers\API'], static function ($routes) {

    // Auth routes (no JWT required for login)
    $routes->group('auth', static function ($routes) {
        // POST /api/auth/login
        $routes->post('login', 'Auth::login');

        // GET /api/auth/validate - validate token
        $routes->get('validate', 'Auth::validate');

        // GET /api/auth/me - get current user (butuh JWT)
        $routes->get('me', 'Auth::me');
    });

    // Pelanggaran routes (butuh JWT untuk semua)
    $routes->group('pelanggaran', ['filter' => 'jwt'], static function ($routes) {
        // GET /api/pelanggaran - list dengan pagination (20 baris per halaman)
        $routes->get('/', 'Pelanggaran::index');

        // GET /api/pelanggaran/1 - detail
        $routes->get('(:num)', 'Pelanggaran::show/$1');

        // POST /api/pelanggaran - create
        $routes->post('/', 'Pelanggaran::create');

        // PUT /api/pelanggaran/1 - update
        $routes->put('(:num)', 'Pelanggaran::update/$1');

        // DELETE /api/pelanggaran/1 - delete
        $routes->delete('(:num)', 'Pelanggaran::delete/$1');
    });

    // Objek Melintas routes (butuh JWT untuk semua)
    $routes->group('objek-melintas', ['filter' => 'jwt'], static function ($routes) {
        // GET /api/objek-melintas - list dengan pagination (10 baris per halaman)
        $routes->get('/', 'ObjekMelintas::index');

        // GET /api/objek-melintas/1 - detail
        $routes->get('(:num)', 'ObjekMelintas::show/$1');

        // POST /api/objek-melintas - create
        $routes->post('/', 'ObjekMelintas::create');

        // PUT /api/objek-melintas/1 - update
        $routes->put('(:num)', 'ObjekMelintas::update/$1');

        // DELETE /api/objek-melintas/1 - delete
        $routes->delete('(:num)', 'ObjekMelintas::delete/$1');
    });

    // Legacy routes (optional, for backward compatibility)
    $routes->group('', ['namespace' => 'App\Controllers\Api'], static function ($routes) {
        // Auth
        $routes->group('auth', static function ($routes) {
            // POST /api/auth/login
            $routes->post('login', 'AuthController::login');

            // GET /api/auth/me  (butuh JWT filter)
            $routes->get('me', 'AuthController::me', ['filter' => 'jwt']);
        });

        // Cities (Cities Master)
        // JWT
        $routes->group('cities', ['filter' => 'jwt'], static function ($routes) {

            // GET /api/cities
            // list + pagination + search
            $routes->get('/', 'CityController::index');

            // POST /api/cities
            // add city
            $routes->post('/', 'CityController::store');

            // PUT /api/cities/1
            $routes->put('(:num)', 'CityController::update/$1');

            // DELETE /api/cities/1
            $routes->delete('(:num)', 'CityController::delete/$1');
        });

        // Census (Data Census)
        $routes->group('census', ['filter' => 'jwt'], static function ($routes) {

            // GET /api/census
            // list + pagination + search
            $routes->get('/', 'CensusController::index');

            // GET /api/census/1
            // detail
            $routes->get('(:num)', 'CensusController::show/$1');

            // POST /api/census
            // add census
            $routes->post('/', 'CensusController::store');

            // PUT /api/census/1
            $routes->put('(:num)', 'CensusController::update/$1');

            // DELETE /api/census/1
            $routes->delete('(:num)', 'CensusController::delete/$1');
        });
    });
});