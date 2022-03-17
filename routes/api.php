<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JobController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('jobs', [JobController::class, 'index']);                         					// show all jobs
Route::post('jobs', [JobController::class, 'store']);                          				    // store new jobs
Route::get('jobs/{job}', [JobController::class, 'show']);                       				// show one job in detail
Route::match(['patch', 'put'],'jobs/{job}', [JobController::class, 'update']); 					// edit one job
Route::delete('jobs/{job}', [JobController::class, 'destroy']);                 				// delete one job

Route::get('companies', [CompanyController::class, 'index']);                         			// show all companies
Route::post('companies', [CompanyController::class, 'store']);                      		    // store new companies
Route::get('companies/{company}', [CompanyController::class, 'show']);           	            // show one company in detail
Route::match(['patch', 'put'],'companies/{company}', [CompanyController::class, 'update']);		// edit one company
Route::delete('companies/{company}', [CompanyController::class, 'destroy']);                 	// delete one company

Route::get('users', [UserController::class, 'index']);                         					// show all users
Route::post('users', [UserController::class, 'store']);                      		    		// store new users
Route::get('users/{user}', [UserController::class, 'show']);           	            			// show one user in detail
Route::match(['patch', 'put'],'users/{user}', [UserController::class, 'update']);				// edit one user
Route::delete('users/{user}', [UserController::class, 'destroy']);                 				// delete one user

Route::get('jobs/{job}/companies/', [JobController::class, 'showCompany']);                 	// show the company of a job
Route::get('companies/{company}/jobs', [CompanyController::class, 'showJobs']);                 // show all jobs of a company
Route::get('companies/{company}/users', [CompanyController::class, 'showUsers']);				// show all users of a company 
Route::get('users/{user}/companies', [UserController::class, 'showCompanies']);					// show all companies of a user 