<?php

use App\Http\Controllers\authController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\regisController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('login');
});

Route::get('register', function () {
    return view('register');
});

Route::get('admin', [PostController::class, 'index'])->name('admin.beranda');
Route::get('create', [PostController::class, 'create'])->name('posts.create');
Route::post('create', [PostController::class, 'store'])->name('posts.store');

Route::get('login', [authController::class, 'create'])->name('login');
Route::post('login', [authController::class, 'authenticate'])
    ->middleware('throttle:5,1')
    ->name('login.authenticate');
Route::post('register', [regisController::class, 'store'])->name('register.store');
Route::post('logout', [authController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');
Route::get('berandauser', [UserController::class, 'index'])
    ->middleware('auth')
    ->name('user.beranda');
Route::get('posts/{post}', [PostController::class, 'show'])
    ->middleware('auth')
    ->name('posts.show');
Route::get('posts/{post}/edit', [PostController::class, 'edit'])
    ->middleware('auth')
    ->name('posts.edit');
Route::put('posts/{post}', [PostController::class, 'update'])
    ->middleware('auth')
    ->name('posts.update');
Route::delete('posts/{post}', [PostController::class, 'destroy'])
    ->middleware('auth')
    ->name('posts.destroy');
