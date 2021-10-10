<?php

use Illuminate\Support\Facades\Auth;
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


Auth::routes(['verify' => true]);


Route::get('/', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('/', 'Auth\LoginController@login')->name('login');

Route::get('/register', 'Auth\RegisterController@showRegisterForm')->name('register');
Route::post('/register', 'Auth\RegisterController@register')->name('register');
Route::get("/getkabupaten/{id}", "Auth\RegisterController@kabupaten_baru");
Route::get("/getkecamatan/{id}", "Auth\RegisterController@kecamatan_baru");
Route::get("/getdesa/{id}", "Auth\RegisterController@desa_baru");


Route::get('account/password', 'Account\PasswordController@edit')->name('password.edit');
Route::patch('account/password', 'Account\PasswordController@update')->name('password.edit');

Route::group(
    ['middleware' => 'auth'],
    function () {
        // ------------------------------------------------------------------------
        // MODUL POS
        // DASHBOARD
        Route::prefix('pos')
            ->namespace('PointOfSales')
            ->middleware(['admin_kasir', 'verified'])
            ->group(function () {
                Route::get('/', 'DashboardPOSController@index')
                    ->name('dashboardpointofsales');
            });

        // PEMBAYARAN LAYANAN SERVICE
        Route::prefix('pos')
            ->namespace('PointOfSales\Pembayaran')
            ->middleware(['admin_kasir', 'verified'])
            ->group(function () {
                Route::resource('pembayaranservice', 'PembayaranServiceController');

                Route::resource('pembayaransparepart', 'PembayaranSparepartController');
                Route::get('cetak-pembayaran-penjualan/{id}', 'PembayaranSparepartController@CetakPembayaran')->name('cetak-pembayaran-penjualan');
            });


        // PEMBAYARAN PENJUALAN SPAREPART
        Route::prefix('pos')
            ->namespace('PointOfSales\Laporan')
            ->middleware(['admin_kasir', 'verified'])
            ->group(function () {
                Route::resource('laporanservice', 'LaporanServiceController');
                Route::get('invoice-service/{id}', 'LaporanServiceController@CetakPembayaran')->name('cetak-invoice-service');
                Route::resource('laporansparepart', 'LaporanSparepartController');
                Route::get('invoice-sparepart/{id}', 'LaporanSparepartController@CetakInvoice')->name('cetak-invoice-sparepart');
            });
    }
);
