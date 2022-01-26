<?php

use Illuminate\Support\Facades\Route;

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

Auth::routes();

Route::get('/', [App\Http\Controllers\ProductsController::class, 'index'])->name('home');

/* Admin routes */
Route::get('/admin', [App\Http\Controllers\Admin\AdminController::class, 'index'])->name('admin.settings');
Route::post('/admin/settings/save', [App\Http\Controllers\Admin\AdminController::class, 'save'])->name('admin.settings.save');
Route::get('/admin/import', [App\Http\Controllers\Admin\AdminController::class, 'import'])->name('admin.import');

/* Telegram */
Route::get('/admin/webhook', [App\Http\Controllers\TelegramController::class, 'setWebhook'])->name('admin.webhook');
