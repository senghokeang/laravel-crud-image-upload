<?php

use App\Http\Controllers\CustomerController;
use Illuminate\Support\Facades\Route;

Route::controller(CustomerController::class)->group(function () {
    Route::get('/', 'index');
    Route::view('create',  'customer/form');
    Route::get('edit/{id}',  'edit');
    Route::match(['post', 'put'], 'submit', 'submit');
    Route::delete('delete', 'delete');
});
