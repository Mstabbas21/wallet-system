<?php

use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\PurchasesController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\BraintreeController;
use Illuminate\Support\Facades\Http;

//signup
Route::post('/users',[UserController::class,'store']);
//login
Route::post('/login',[UserController::class,'login']);


Route::middleware('auth:sanctum')->group(function (){
//show balance
Route::get('users/balance', [UserController::class, 'showBalance']);
//transcation from paypal to account deposit withdraw
Route::post('/transaction',[TransactionController::class,'store']);
//transfer from user to user
Route::post('/transfer',[TransferController::class,'store']);
//show all services mother
Route::get('/services/mother', [ServiceController::class, 'indexMother']);
//show all servies
Route::get('/services', [ServiceController::class, 'index']);
//show services spesific
Route::get('/services/{id}',[ServiceController::class,'showService']);
// phurcase a services
Route::post('/purchases',[PurchasesController::class,'purchase']);

   }); 
 












//Route::get('/payment/token',[BraintreeController::class,'getToken']);
//Route::post('payment/checkout',[BraintreeController::class,'checkout']);










 








//Route::middleware('is_admin')->group(function (){
  



//});

