<?php

use Illuminate\Support\Facades\Route;

Route::name('user.')->group(function () {
    Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
    Route::post('login', 'Auth\LoginController@login');
    Route::get('logout', 'Auth\LoginController@logout')->name('logout');

    Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
    Route::post('register', 'Auth\RegisterController@register')->middleware('regStatus');

    Route::post('check-mail', 'Auth\RegisterController@checkUser')->name('checkUser');

    Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetCodeEmail')->name('password.email');
    Route::get('password/code-verify', 'Auth\ForgotPasswordController@codeVerify')->name('password.code.verify');
    Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');
    Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
    Route::post('password/verify-code', 'Auth\ForgotPasswordController@verifyCode')->name('password.verify.code');

    Route::middleware('auth')->group(function () {
        Route::get('authorization', 'AuthorizationController@authorizeForm')->name('authorization');
        Route::get('resend-verify', 'AuthorizationController@sendVerifyCode')->name('send.verify.code');
        Route::post('verify-email', 'AuthorizationController@emailVerification')->name('verify.email');
        Route::post('verify-sms', 'AuthorizationController@smsVerification')->name('verify.sms');
        Route::post('verify-g2fa', 'AuthorizationController@g2faVerification')->name('go2fa.verify');

        Route::middleware(['checkStatus'])->group(function () {
            Route::get('dashboard', 'UserController@home')->name('home');
            Route::get('profile-setting', 'UserController@profile')->name('profile.setting');
            Route::post('profile-setting', 'UserController@submitProfile');
            Route::get('profile-referral', 'UserController@referral')->name('profile.referral');
            Route::get('withdraw-referral-earning', 'UserController@withdrawreferral')->name('profile.referralwithdraw');
            Route::get('change-password', 'UserController@changePassword')->name('password.change');
            Route::post('change-password', 'UserController@submitPassword');
            Route::get('change-pin', 'UserController@changePin')->name('pin.change');

            //2FA
            Route::get('twofactor', 'UserController@show2faForm')->name('twofactor');
            Route::post('twofactor/enable', 'UserController@create2fa')->name('twofactor.enable');
            Route::post('twofactor/disable', 'UserController@disable2fa')->name('twofactor.disable');

            // Deposit
            Route::any('/fund-wallet', 'Gateway\PaymentController@fundwallet')->name('fundwallet');
            Route::post('fundwallet/insert', 'Gateway\PaymentController@fundwalletInsert')->name('fundwallet.insert');
            Route::get('fundwallet/preview', 'Gateway\PaymentController@fundwalletPreview')->name('fundwallet.preview');
            Route::get('order/confirm', 'Gateway\PaymentController@OrderConfirm')->name('order.confirm');
            Route::get('order/otp', 'Gateway\PaymentController@OrderEnterOTP')->name('order.otp');
            Route::post('order/otp', 'Gateway\PaymentController@OrderConfirmOTP');
            Route::get('deposit/manual', 'Gateway\PaymentController@manualDepositConfirm')->name('deposit.manual.confirm');
            Route::post('deposit/manual', 'Gateway\PaymentController@manualDepositUpdate')->name('deposit.manual.update');
            Route::any('deposit/history', 'UserController@depositHistory')->name('deposit.history');
            // Orders
            Route::get('/orders', 'UserController@orders')->name('orders');
            Route::get('/product/review', 'UserController@productReviews')->name('product.review');
            Route::get('/order/thankyou', 'OrderController@paymentcomplete')->name('paymentsuccess');


            // Deposit
            Route::any('/payment', 'Gateway\PaymentController@deposit')->name('deposit');
            Route::post('payment/insert', 'Gateway\PaymentController@depositInsert')->name('deposit.insert');
            Route::get('payment/preview', 'Gateway\PaymentController@depositPreview')->name('deposit.preview');
            Route::get('payment/confirm', 'Gateway\PaymentController@depositConfirm')->name('deposit.confirm');
            Route::get('payment/manual', 'Gateway\PaymentController@manualDepositConfirm')->name('deposit.manual.confirm');
            Route::post('payment/manual', 'Gateway\PaymentController@manualDepositUpdate')->name('deposit.manual.update');
            Route::get('payment/history', 'UserController@depositHistory')->name('deposit.history');

            Route::get('checkout/', 'CartController@checkout')->name('checkout');
            Route::post('calculatedelivery/', 'CartController@calculatedelivery')->name('calculatedelivery');
            Route::post('/checkout/{type}', 'OrderController@confirmOrder')->name('checkout-to-payment');
            Route::get('/payment_success/thankyou', 'OrderController@paymentcomplete')->name('paymentsuccess');

            //Order
            Route::get('orders/{type}', 'OrderController@orders')->name('orders');
            Route::get('order/{order_number}', 'OrderController@orderDetails')->name('order');
            Route::get('product/review', 'UserController@productsReview')->name('product.review');
            Route::post('product/review/add', 'UserController@addReview')->name('product.review.submit');
            
             //Bills Payment
            Route::get('airtime', 'OrderController@airtime')->name('airtime');
            Route::post('airtime', 'OrderController@airtimebuy');
            Route::get('internet', 'OrderController@internet')->name('internet');
            Route::post('internet', 'OrderController@internetbuy');
            Route::get('cabletv', 'OrderController@cabletv')->name('cabletv');
            Route::post('cabletv', 'OrderController@validatedecoder');
            Route::get('validate-cabletv', 'OrderController@decodervalidated')->name('decodervalidated');
            Route::post('validate-cabletv', 'OrderController@decoderpay');
            Route::get('utility-bills', 'OrderController@utility')->name('utility');
            Route::post('utility-bills', 'OrderController@validatebill');
            Route::get('utility-bills-validated', 'OrderController@billvalidated')->name('billvalidated');
            Route::post('utility-bills-validated', 'OrderController@billpay');
            Route::get('utility-token/{id}', 'OrderController@utilitytoken')->name('utilitytoken');

        });
    });
});
