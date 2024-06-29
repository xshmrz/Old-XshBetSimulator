<?php
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
Route::group(['prefix' => LaravelLocalization::setLocale()], function () {
Route::group(['as' => 'site.', 'prefix' => '', 'middleware' => ['MiddlewareSite']], function () {
Route::get('', ['App\Http\Controllers\Site\Home', 'index'])->name('index');
Route::resource('bet', 'App\Http\Controllers\Site\Base\Bet');
Route::resource('coupon', 'App\Http\Controllers\Site\Base\Coupon');
Route::resource('migration', 'App\Http\Controllers\Site\Base\Migration');
Route::resource('user', 'App\Http\Controllers\Site\Base\User');
});
Route::group(['as' => 'panel.', 'prefix' => 'panel', 'middleware' => ['MiddlewarePanel']], function () {
Route::get('', ['App\Http\Controllers\Panel\Home', 'index'])->name('index');
Route::resource('bet', 'App\Http\Controllers\Panel\Base\Bet');
Route::resource('coupon', 'App\Http\Controllers\Panel\Base\Coupon');
Route::resource('migration', 'App\Http\Controllers\Panel\Base\Migration');
Route::resource('user', 'App\Http\Controllers\Panel\Base\User');
});
Route::group(['as' => 'dashboard.', 'prefix' => 'dashboard', 'middleware' => ['MiddlewareDashboard']], function () {
Route::get('', ['App\Http\Controllers\Dashboard\Home', 'index'])->name('index');
Route::resource('bet', 'App\Http\Controllers\Dashboard\Base\Bet');
Route::resource('coupon', 'App\Http\Controllers\Dashboard\Base\Coupon');
Route::resource('migration', 'App\Http\Controllers\Dashboard\Base\Migration');
Route::resource('user', 'App\Http\Controllers\Dashboard\Base\User');
});
Route::group(['as' => 'api.', 'prefix' => 'api', 'middleware' => ['MiddlewareApi']], function () {
Route::resource('bet', 'App\Http\Controllers\Api\Base\Bet');
Route::resource('coupon', 'App\Http\Controllers\Api\Base\Coupon');
Route::resource('migration', 'App\Http\Controllers\Api\Base\Migration');
Route::resource('user', 'App\Http\Controllers\Api\Base\User');
});
Route::group(['as' => 'site.', 'prefix' => '', 'middleware' => []], function () {
Route::get('login', ['App\Http\Controllers\Site\Authorize', 'login'])->name('login');
Route::post('login', ['App\Http\Controllers\Site\Authorize', 'loginDo'])->name('loginDo');
Route::get('register', ['App\Http\Controllers\Site\Authorize', 'register'])->name('register');
Route::post('register', ['App\Http\Controllers\Site\Authorize', 'registerDo'])->name('registerDo');
Route::get('lost-password', ['App\Http\Controllers\Site\Authorize', 'lostPassword'])->name('lostPassword');
Route::post('lost-password', ['App\Http\Controllers\Site\Authorize', 'lostPasswordDo'])->name('lostPasswordDo');
Route::get('logout', ['App\Http\Controllers\Site\Authorize', 'logout'])->name('logout');
Route::post('logout', ['App\Http\Controllers\Site\Authorize', 'logoutDo'])->name('logoutDo');
});
Route::group(['as' => 'panel.', 'prefix' => 'panel', 'middleware' => []], function () {
Route::get('login', ['App\Http\Controllers\Panel\Authorize', 'login'])->name('login');
Route::post('login', ['App\Http\Controllers\Panel\Authorize', 'loginDo'])->name('loginDo');
Route::get('register', ['App\Http\Controllers\Panel\Authorize', 'register'])->name('register');
Route::post('register', ['App\Http\Controllers\Panel\Authorize', 'registerDo'])->name('registerDo');
Route::get('lost-password', ['App\Http\Controllers\Panel\Authorize', 'lostPassword'])->name('lostPassword');
Route::post('lost-password', ['App\Http\Controllers\Panel\Authorize', 'lostPasswordDo'])->name('lostPasswordDo');
Route::get('logout', ['App\Http\Controllers\Panel\Authorize', 'logout'])->name('logout');
Route::post('logout', ['App\Http\Controllers\Panel\Authorize', 'logoutDo'])->name('logoutDo');
});
Route::group(['as' => 'dashboard.', 'prefix' => 'dashboard', 'middleware' => []], function () {
Route::get('login', ['App\Http\Controllers\Dashboard\Authorize', 'login'])->name('login');
Route::post('login', ['App\Http\Controllers\Dashboard\Authorize', 'loginDo'])->name('loginDo');
Route::get('register', ['App\Http\Controllers\Dashboard\Authorize', 'register'])->name('register');
Route::post('register', ['App\Http\Controllers\Dashboard\Authorize', 'registerDo'])->name('registerDo');
Route::get('lost-password', ['App\Http\Controllers\Dashboard\Authorize', 'lostPassword'])->name('lostPassword');
Route::post('lost-password', ['App\Http\Controllers\Dashboard\Authorize', 'lostPasswordDo'])->name('lostPasswordDo');
Route::get('logout', ['App\Http\Controllers\Dashboard\Authorize', 'logout'])->name('logout');
Route::post('logout', ['App\Http\Controllers\Dashboard\Authorize', 'logoutDo'])->name('logoutDo');
});
Route::group(['as' => 'api.', 'prefix' => 'api', 'middleware' => []], function () {
Route::get('login', ['App\Http\Controllers\Api\Authorize', 'login'])->name('login');
Route::post('login', ['App\Http\Controllers\Api\Authorize', 'loginDo'])->name('loginDo');
Route::get('register', ['App\Http\Controllers\Api\Authorize', 'register'])->name('register');
Route::post('register', ['App\Http\Controllers\Api\Authorize', 'registerDo'])->name('registerDo');
Route::get('lost-password', ['App\Http\Controllers\Api\Authorize', 'lostPassword'])->name('lostPassword');
Route::post('lost-password', ['App\Http\Controllers\Api\Authorize', 'lostPasswordDo'])->name('lostPasswordDo');
Route::get('logout', ['App\Http\Controllers\Api\Authorize', 'logout'])->name('logout');
Route::post('logout', ['App\Http\Controllers\Api\Authorize', 'logoutDo'])->name('logoutDo');
});
});
