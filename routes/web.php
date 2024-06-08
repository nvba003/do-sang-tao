<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ContainerController;
use App\Http\Controllers\InventoryTransactionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderEcommerceController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AuxpackingController;
use App\Http\Controllers\TaskController;
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

Route::get('/get-path', function () {
    return base_path();
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';

//Route::post('/login-endpoint', 'AuthController@login');
Route::post('/login-endpoint', [AuthController::class, 'login']);

Route::get('/fetch-products', [ProductController::class, 'fetchAndStoreProducts'])->name('fetch.products');//lấy dữ liệu products on Sapo web
Route::get('/product', [ProductController::class, 'showProducts'])->name('products.show');
Route::put('/update-info-product', [ProductController::class, 'updateInfoProduct'])->name('products.updateInfo');
Route::put('/update-size-weight', [ProductController::class, 'updateSizeWeight'])->name('products.updateSizeWeight');
Route::post('/product/bundle', [ProductController::class, 'saveBundleItems']);
Route::delete('/product/bundle/{bundleId}', [ProductController::class, 'destroyBundle']);

Route::get('/containers', [ContainerController::class, 'showContainers'])->name('containers.show');//hiển thị trang danh sách thùng hàng
Route::post('/containers', [ContainerController::class, 'store'])->name('containers.store');//tạo thùng hàng mới

Route::get('/container-transactions', [InventoryTransactionController::class, 'showTransactions'])->name('transactions.show');//trang hiện giao dịch thùng hàng
Route::post('/container-transactions', [InventoryTransactionController::class, 'store'])->name('transactions.store');//tạo giao dịch thùng hàng
Route::post('/fetch-transaction-product', [InventoryTransactionController::class, 'fetchTransactionProduct'])->name('transactions.fetchProduct');//tìm SP theo thùng hàng
Route::post('/container-transactions/search/container', [InventoryTransactionController::class, 'searchContainer'])->name('transactions.searchContainer');//lấy dữ liệu thùng hàng bằng ajax để hiển thị lịch sử thùng hàng
Route::post('/container-transactions/data', [InventoryTransactionController::class, 'getTransactions'])->name('transactions.data');//lấy danh sách json transactions

Route::get('/container-transactions/{transaction}/edit', [InventoryTransactionController::class, 'edit'])->name('transactions.edit');//chưa dùng

Route::resource('roles', RoleController::class);
Route::resource('permissions', PermissionController::class);
Route::get('/users', [UserController::class, 'index'])->name('users.index');
Route::post('/prepare-edit-user', [UserController::class, 'prepareEdit'])->name('users.prepareEdit');
Route::get('/edit-user', [UserController::class, 'edit'])->name('users.edit');
Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
Route::get('/create-user', [UserController::class, 'create'])->name('users.create');
Route::post('/store-user', [UserController::class, 'store'])->name('users.store');
Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
// Định nghĩa các resource routes cho UserController
// Route::resource('users', UserController::class);

Route::get('/order-sendo/{platform_id}', [OrderEcommerceController::class, 'showOrderSendos'])->name('orderSendo.show');
// Route::post('/store-order-sendo', [OrderEcommerceController::class, 'storeOrderSendos'])->name('orderSendo.store');
Route::post('/send-order-sendo-to-order', [OrderEcommerceController::class, 'sendOrderSendos'])->name('orderSendo.send');
Route::get('/order-shopee/{platform_id}', [OrderEcommerceController::class, 'showOrderShopees'])->name('orderShopee.show');
Route::post('/send-order-shopee-to-order', [OrderEcommerceController::class, 'sendOrderShopees'])->name('orderShopee.send');
Route::get('/order-lazada/{platform_id}', [OrderEcommerceController::class, 'showOrderLazadas'])->name('orderLazada.show');
Route::post('/send-order-lazada-to-order', [OrderEcommerceController::class, 'sendOrderLazadas'])->name('orderLazada.send');
Route::get('/order-tiki/{platform_id}', [OrderEcommerceController::class, 'showOrderTikis'])->name('orderTiki.show');
Route::post('/send-order-tiki-to-order', [OrderEcommerceController::class, 'sendOrderTikis'])->name('orderTiki.send');
Route::get('/order-tiktok/{platform_id}', [OrderEcommerceController::class, 'showOrderTiktoks'])->name('orderTiktok.show');
Route::post('/send-order-tiktok-to-order', [OrderEcommerceController::class, 'sendOrderTiktoks'])->name('orderTiktok.send');

Route::get('/order/{branch_id}', [OrderController::class, 'showOrders'])->name('order.show');
Route::get('/order-process/{branch_id}', [OrderController::class, 'showOrderProcesses'])->name('orderProcess.show');
Route::post('/order/update-info-order', [OrderController::class, 'updateInfoOrder']);
Route::post('/order/add-finance-order', [OrderController::class, 'addFinanceOrder']);
Route::get('/order/bundle-items/{bundleId}', [OrderController::class, 'getBundleItems']);
Route::get('/get-packing-orders', [OrderController::class, 'getPackingOrders'])->name('packingOrder.fetch');
Route::post('/order/update-order', [OrderController::class, 'updateOrder']);
Route::post('/order-process/update-order-cancel-return', [OrderController::class, 'updateOrderCanCelReturn']);
Route::post('/order-process/update-order-process', [OrderController::class, 'updateOrderProcess']);

Route::post('/order/add-packing-details', [AuxpackingController::class, 'addPackings']);
Route::get('/cal', [AuxpackingController::class, 'updateProductSummaries']);
Route::get('/auxpacking-product/{branch_id}', [AuxpackingController::class, 'showProducts']);
Route::post('/update-auxpacking-container', [AuxpackingController::class, 'updateContainer'])->name('auxPackingContainer.update');
Route::post('/add-auxpacking-container', [AuxpackingController::class, 'addContainer'])->name('auxPackingContainer.add');
Route::delete('/remove-auxpacking-container', [AuxpackingController::class, 'removeContainer'])->name('auxPackingContainer.remove');
Route::delete('/remove-auxpacking-scan', [AuxpackingController::class, 'removeScan'])->name('auxPackingScan.remove');
Route::post('/update-order-statuses', [AuxpackingController::class, 'updateOrderStatuses'])->name('auxPackingContainer.updateStatuses');

Route::get('/auxpacking-order/{branch_id}', [AuxpackingController::class, 'showOrders']);
Route::get('/auxpacking-container/{branch_id}', [AuxpackingController::class, 'showContainers']);
Route::get('/auxpacking-scan/{branch_id}', [AuxpackingController::class, 'showScans'])->name('scans.show');
Route::post('/auxpacking-scan/store', [AuxpackingController::class, 'storeScan'])->name('scans.store');

Route::resource('tasks', TaskController::class);
Route::post('/add-comment', [TaskController::class, 'addComment'])->name('tasks.addComment');
Route::post('/update-comment', [TaskController::class, 'updateComment'])->name('tasks.updateComment');
Route::delete('/delete-comment', [TaskController::class, 'deleteComment'])->name('tasks.deleteComment');
Route::post('/tasks/{task}/attachments', [TaskController::class, 'storeAttachment']);
Route::delete('/attachments/{attachment}', [TaskController::class, 'destroyAttachment']);

Route::post('/tasks/{task}/tags', [TaskController::class, 'addTag']);
Route::delete('/tags/{task}', [TaskController::class, 'destroyTag']);

Route::post('/tasks/{task}/add-user', [TaskController::class, 'addUser']);
Route::delete('/tasks/{task}/remove-user/{user}', [TaskController::class, 'removeUser']);

Route::post('/tasks/add-order', [TaskController::class, 'addOrder']);
Route::delete('/tasks/remove-order/{orderId}', [TaskController::class, 'removeOrder']);

Route::post('/tasks/add-product', [TaskController::class, 'addProduct']);
Route::delete('/tasks/remove-product/{productId}', [TaskController::class, 'removeProduct']);

Route::post('/tasks/add-customer', [TaskController::class, 'addCustomer']);
Route::delete('/tasks/remove-customer/{customerId}', [TaskController::class, 'removeCustomer']);

Route::delete('/tasks/remove-related-task/{relatedTaskId}', [TaskController::class, 'removeRelatedTask']);
Route::post('/tasks/add-dependent', [TaskController::class, 'addDependent']);
Route::post('/tasks/add-dependency', [TaskController::class, 'addDependency']);
Route::post('/tasks/add-related-task', [TaskController::class, 'addRelatedTask']);
Route::delete('/tasks/remove-related-task/{taskId}', [TaskController::class, 'removeRelatedTask']);



