<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// defined routes before getting called from endpoints
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\NotFoundController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


// GET /api/employees
// GET /api/employees/{id}
// POST /api/employees
// PUT /api/employees/{id}
// DELETE /api/employees/{id}

// Departments
Route::resource('departments', DepartmentController::class);
Route::fallback([NotFoundController::class, 'index']);


// Employees
Route::apiResource('employees', EmployeeController::class);



// Addresses
Route::resource('addresses', AddressController::class);


