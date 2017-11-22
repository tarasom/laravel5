<?php

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
    $redirectUrl = route('login');
    $authUri = \App\Services\Auth\AuthorizationService::getAuthUrl($redirectUrl);

    return view('welcome')->withUri($authUri);
})->name('main');

Route::get('login', Auth\LoginController::class . '@login')->name('login');

Route::get('/profile', Controller::class . '@showProfile')->name('profile');