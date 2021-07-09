<?php

use App\Application;
use App\Controllers\AuthController;
use App\Controllers\UserController;
use App\Http\Request;
use App\Http\Response;
use App\Routing\Route;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . "/../../");
$dotenv->load();

$app = new Application();

$app->cors();

/*
|------------------------------------------------------------------
| API Routes
|------------------------------------------------------------------
|
| Here is where you can register API routes for your application. 
|
*/

Route::post('/login', [UserController::class, 'index']);
Route::post('/register', [UserController::class, 'store']);
Route::post('/distance', [UserController::class, 'show']);
Route::post('/auth', [AuthController::class, 'index']);


Route::get('/hello', function (Request $request) {
  Response::json(['hello' => 'atlastrip']);
});

$app->run();
