<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\WebRoomController;
use App\Http\Controllers\WebBookingController;
use App\Http\Controllers\WebOrderController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\Payment\PayhereController;
use Illuminate\Support\Facades\Route;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::middleware(['auth','permission:rooms'])->group(function () {
    Route::prefix('/dashboard')->group(function () {
        //Rooms CRUD
        Route::get('/rooms', [RoomController::class, 'index'])->name('room.index');
        Route::prefix('/room')->group(function(){
            Route::get('/add',[RoomController::class, 'create'])->name('room.create');
            Route::post('/add',[RoomController::class, 'store'])->name('room.store');
            Route::get('/{room}/edit',[RoomController::class, 'edit'])->name('room.edit');
            Route::put('/{room}/edit',[RoomController::class, 'update'])->name('room.update');
            Route::delete('/{room}/delete',[RoomController::class, 'destroy'])->name('room.destroy');
            Route::post('/image-upload', [RoomController::class, 'imageUpload'])->name('room.image.upload');
        });
        Route::prefix('/orders')->group(function(){
            Route::get('/', [OrdersController::class, 'index'])->name('admin.orders');
            Route::get('/table', [OrdersController::class, 'dataTable'])->name('admin.ordersTable');
            Route::put('/{order}/cancel', [OrdersController::class, 'cancel'])->name('admin.orders.cancel');
            Route::put('/{order}/approve', [OrdersController::class, 'approve'])->name('admin.orders.approve');
        });
    });
});

Route::get('/rooms', [WebRoomController::class, 'index'])->name('rooms');
Route::get('/room/{slug}', [WebRoomController::class, 'show'])->name('room.show');

// Booking
Route::post('booking/create', [WebBookingController::class, 'create'])->name('booking.create');
Route::post('booking/confirm', [WebBookingController::class, 'confirm'])->name('booking.confirm');

// Cart
Route::get('cart', [WebOrderController::class, 'cart'])->name('cart');
Route::post('cart/checkout', [WebOrderController::class, 'create'])->name('order.create');
Route::get('cart/checkout/{id}', [WebOrderController::class, 'checkout'])->name('cart.checkout');
Route::post('cart/confirm/{id}', [WebOrderController::class, 'confirm'])->name('cart.confirm');
Route::get('email', [WebOrderController::class, 'email']);

//payhere
Route::get('payhere/pay/{id}', [PayhereController::class, 'pay'])->name('payhere.pay');   
Route::get('payhere/return/{id}', [PayhereController::class, 'return'])->name('payhere.return');
Route::get('payhere/cancel/{id}', [PayhereController::class, 'cancel'])->name('payhere.cancel');



require __DIR__.'/auth.php';
Auth::routes();
