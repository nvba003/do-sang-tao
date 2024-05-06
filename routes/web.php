<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ProductApiController;
use App\Http\Controllers\ContainerController;
use App\Http\Controllers\InventoryTransactionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\UserController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';

//Route::post('/login-endpoint', 'AuthController@login');
Route::post('/login-endpoint', [AuthController::class, 'login']);

Route::get('/fetch-products', [ProductApiController::class, 'fetchAndStoreProducts'])->name('fetch.products');//lấy dữ liệu products on Sapo web
Route::get('/show-web-products', [ProductApiController::class, 'showWebProducts'])->name('web.products');

Route::get('/containers', [ContainerController::class, 'showContainers'])->name('containers.show');//hiển thị trang danh sách thùng hàng
Route::post('/containers', [ContainerController::class, 'store'])->name('containers.store');//tạo thùng hàng mới
Route::post('/container/search/product', [ContainerController::class, 'searchProduct'])->name('containers.searchProduct');//lấy dữ liệu sản phẩm bằng ajax để hiển thị thùng hàng
Route::post('/container/search/container', [ContainerController::class, 'searchContainer'])->name('containers.searchContainer');//lấy dữ liệu thùng hàng bằng ajax để hiển thị thùng hàng

Route::get('/container-transactions', [InventoryTransactionController::class, 'show'])->name('transactions.show');//trang hiện giao dịch thùng hàng
Route::post('/container-transactions', [InventoryTransactionController::class, 'store'])->name('transactions.store');//tạo giao dịch thùng hàng
Route::post('/container-transactions/search/container', [InventoryTransactionController::class, 'searchContainer'])->name('transactions.searchContainer');//lấy dữ liệu thùng hàng bằng ajax để hiển thị lịch sử thùng hàng
Route::post('/container-transactions/data', [InventoryTransactionController::class, 'getTransactions'])->name('transactions.data');//lấy danh sách json transactions

Route::get('/container-transactions/{transaction}/edit', [InventoryTransactionController::class, 'edit'])->name('transactions.edit');//chưa dùng

Route::resource('roles', RoleController::class);
Route::resource('permissions', PermissionController::class);
Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
// Định nghĩa các resource routes cho UserController
Route::resource('users', UserController::class);


