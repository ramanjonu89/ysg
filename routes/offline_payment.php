<?php
/*

|--------------------------------------------------------------------------

| Offline Payment Routes

|--------------------------------------------------------------------------

|

| Here is where you can register admin routes for your application. These

| routes are loaded by the RouteServiceProvider within a group which

| contains the "web" middleware group. Now create something great!

|

*/
//Admin
Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'admin']], function() {
    Route::get('/offline-wallet-recharge-requests', 'WalletController@offline_recharge_request')->name('offline_wallet_recharge_request.index');
    Route::post('/offline-wallet-recharge/approved', 'WalletController@updateApproved')->name('offline_recharge_request.approved');
    // Seller Package purchase request
    // customer package purchase request
    Route::get('/offline-customer-package-payment-requests', 'CustomerPackagePaymentController@offline_payment_request')->name('offline_customer_package_payment_request.index');
    Route::post('/offline-customer-package-payment/approved', 'CustomerPackagePaymentController@offline_payment_approval')->name('offline_customer_package_payment.approved');
});
//FrontEnd
Route::group(['middleware' => ['user', 'verified']], function() {
    Route::post('/offline-wallet-recharge', 'WalletController@offline_recharge')->name('wallet_recharge.make_payment');
});
// customer package purchase
Route::post('/offline-customer-package-paymnet', 'CustomerPackageController@purchase_package_offline')->name('customer_package.make_offline_payment');
// Seller Package purchase

