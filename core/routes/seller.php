<?php
use Illuminate\Support\Facades\Route;

Route::name('seller.')->namespace('Seller')->group(function () {
    Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
    Route::post('login', 'Auth\LoginController@login');
    Route::get('logout', 'Auth\LoginController@logout')->name('logout');

    Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
    Route::post('register', 'Auth\RegisterController@register')->middleware('regStatus');
    Route::post('check-mail', 'Auth\RegisterController@checkSeller')->name('checkSeller');
    Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetCodeEmail')->name('password.email');
    Route::get('password/code-verify', 'Auth\ForgotPasswordController@codeVerify')->name('password.code.verify');
    Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');
    Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
    Route::post('password/verify-code', 'Auth\ForgotPasswordController@verifyCode')->name('password.verify.code');

    Route::middleware('seller')->group(function () {
        Route::get('authorization', 'AuthorizationController@authorizeForm')->name('authorization');
        Route::get('resend-verify', 'AuthorizationController@sendVerifyCode')->name('send.verify.code');
        Route::post('verify-email', 'AuthorizationController@emailVerification')->name('verify.email');
        Route::post('verify-sms', 'AuthorizationController@smsVerification')->name('verify.sms');
        Route::post('verify-g2fa', 'AuthorizationController@g2faVerification')->name('go2fa.verify');

        Route::middleware('sellerCheckStatus')->group(function () {
            Route::get('/', 'SellerController@home')->name('home');
            Route::get('profile', 'SellerController@profile')->name('profile');
            Route::post('profile', 'SellerController@submitProfile');
            Route::get('change-password', 'SellerController@changePassword')->name('password');
            Route::post('change-password', 'SellerController@submitPassword');
            Route::get('twofactor', 'SellerController@show2faForm')->name('twofactor');
            Route::post('twofactor/enable', 'SellerController@create2fa')->name('twofactor.enable');
            Route::post('twofactor/disable', 'SellerController@disable2fa')->name('twofactor.disable');

            // Shop Setting
            Route::get('/shop', 'SellerController@shop')->name('shop');
            Route::post('/shop', 'SellerController@shopUpdate');

            //Manage Products
            Route::get('products', 'ProductController@index')->name('products.all');
            Route::get('products/pending', 'ProductController@pending')->name('products.pending');
            Route::get('product/create', 'ProductController@create')->name('products.create');
            Route::post('product/store/{id}', 'ProductController@store')->name('products.product.store');
            Route::get('product/edit/{id}/{slug}/{seller?}', 'ProductController@edit')->name('products.edit')->where('id', '[0-9]+');
            Route::post('product/delete/{id}', 'ProductController@delete')->name('products.delete')->where('id', '[0-9]+');
            Route::get('product/search/', 'ProductController@productSearch')->name('products.search');
            Route::get('product/trashed', 'ProductController@trashed')->name('products.trashed');
            Route::get('product/trashed/search', 'ProductController@productTrashedSearch')->name('products.trashed.search');
            Route::get('product/reviews', 'ProductController@reviews')->name('products.reviews');
            Route::get('product/reviews/search/{key?}', 'ProductController@reviewSearch')->name('products.reviews.search');

            Route::get('product/add-variant/{id}', 'ProductController@addVariant')->name('products.variant.store');
            Route::post('product/add-variant/{id}', 'ProductController@storeVariant')->name('products.variant.store');
            Route::get('product/edit-variant/{pid}/{aid}', 'ProductController@editAttribute')->name('products.variant.edit');
            Route::post('product/edit-variant-update/{id}', 'ProductController@updateVariant')->name('products.variant.update');
            Route::post('product/delete-variant/{id}', 'ProductController@deleteVariant')->name('products.variant.delete');

            Route::get('product/add-variant-images/{id}', 'ProductController@addVariantImages')->name('products.add-variant-images');
            Route::post('product/add-variant-images/{id}', 'ProductController@saveVariantImages');

            //Stock

            Route::any('product/stock/create/{product_id}', 'ProductStockController@stockCreate')->name('products.stock.create');
            Route::post('product/add-to-stock/{product_id}', 'ProductStockController@stockAdd')->name('products.stock.add');
            Route::get('product/stock/{id}/', 'ProductStockController@stockLog')->name('products.stock.log');

            //Order
            Route::post('orders', 'OrderController@changeStatus')->name('order.status');
            Route::get('orders/', 'OrderController@allOrders')->name('order.index');
            Route::get('orders/pending', 'OrderController@pending')->name('order.to_deliver');
            Route::get('orders/processing', 'OrderController@onProcessing')->name('order.on_processing');
            Route::get('orders/dispatched', 'OrderController@dispatched')->name('order.dispatched');
            Route::get('orders/delivered', 'OrderController@deliveredOrders')->name('order.delivered');
            Route::get('orders/canceled', 'OrderController@canceledOrders')->name('order.canceled');
            Route::get('orders/cod', 'OrderController@codOrders')->name('order.cod');
            Route::get('order/details/{id}', 'OrderController@orderDetails')->name('order.details');

            //Sell log
            Route::get('sales-log', 'sellerController@sellLogs')->name('sell.log');
            Route::get('transaction-logs', 'SellerController@trxLogs')->name('trx.log');

            // Withdraw
            Route::get('/withdraw', 'SellerController@withdrawMoney')->name('withdraw');
            Route::post('/withdraw', 'SellerController@withdrawStore')->name('withdraw.money');
            Route::get('/withdraw/preview', 'SellerController@withdrawPreview')->name('withdraw.preview');
            Route::post('/withdraw/preview', 'SellerController@withdrawSubmit')->name('withdraw.submit');
            Route::get('/withdraw/history', 'SellerController@withdrawLog')->name('withdraw.history');

            // Support Ticket
            Route::prefix('support-tickets')->group(function () {
                Route::get('/', 'TicketController@index')->name('ticket.index');
                Route::get('/open-new-ticket', 'TicketController@openNewTicket')->name('ticket.open');
                Route::post('/open-new-ticket/store', 'TicketController@store')->name('ticket.store');
                Route::get('/view/{ticket}', 'TicketController@viewTicket')->name('ticket.view');
                Route::post('/reply/{ticket}', 'TicketController@reply')->name('ticket.reply');
            });
        });
    });
});
