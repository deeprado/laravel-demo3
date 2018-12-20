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

Route::get('/news/socket', 'NewsController@socket')->name('news.socket');


Auth::routes();

Route::get('/', function () {return view('welcome');})->name('welcome');
Route::get('/manager','\App\Manager\Controllers\HomeController@index')->name('manager');
Route::get('/home','\App\Manager\Controllers\HomeController@index')->name('home');


# 忘记密码 & 修改密码
Route::any("forgot",'Auth\ForgotController@rest')->name('auth.forgot');
Route::any("restPassword",'Auth\ForgotController@password')->name('auth.restPassword');

Route::get('gee/init','Auth\GeeController@init')->name('gee.init');


Route::get('security/password','\App\Manager\Controllers\SecurityController@password')->name('security.password');
Route::post('security/update','\App\Manager\Controllers\SecurityController@update')->name('security.update');


/**
 * ----------------------------------------
 *
 * 权限管理 permission
 *
 * ----------------------------------------
 */
Route::get('role','\App\Manager\Controllers\RoleController@index')->name('role');
Route::post('role/create','\App\Manager\Controllers\RoleController@create')->name('role.create');
Route::post('role/update','\App\Manager\Controllers\RoleController@update')->name('role.update');
Route::post('role/delete','\App\Manager\Controllers\RoleController@delete')->name('role.delete');
Route::post('role/AjaxGetPermission','\App\Manager\Controllers\RoleController@AjaxGetPermission')->name('role.AjaxGetPermission');
Route::post('role/AjaxGetUser','\App\Manager\Controllers\RoleController@AjaxGetUser')->name('role.AjaxGetUser');
Route::post('role/allot/user','\App\Manager\Controllers\RoleController@allot_user')->name('permission.allot.user');
Route::post('role/allot/permission','\App\Manager\Controllers\RoleController@allot_permission')->name('permission.allot.permission');

Route::get('permission','\App\Manager\Controllers\PermissionController@index')->name('permission');
Route::post('permission/create','\App\Manager\Controllers\PermissionController@create')->name('permission.create');
Route::post('permission/update','\App\Manager\Controllers\PermissionController@update')->name('permission.update');
Route::post('permission/delete','\App\Manager\Controllers\PermissionController@delete')->name('permission.delete');
Route::get('permission/user','\App\Manager\Controllers\PermissionController@user')->name('permission.user');
Route::post('permission/AjaxGetUser','\App\Manager\Controllers\PermissionController@AjaxGetUser')->name('permission.AjaxGetUser');
Route::post('permission/AjaxGetPermission','\App\Manager\Controllers\PermissionController@AjaxGetUser')->name('permission.AjaxGetPermission');
Route::post('permission/allot/user','\App\Manager\Controllers\PermissionController@allot_user')->name('permission.allot.user');
