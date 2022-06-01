<?php



Route::namespace('Api')->name('api.')->group(function(){
	Route::get('general-setting','BasicController@generalSetting');
	Route::get('unauthenticate','BasicController@unauthenticate')->name('unauthenticate');
	Route::get('languages','BasicController@languages');
	Route::get('language-data/{code}','BasicController@languageData');

    Route::get('countries', 'BasicController@country');

	Route::namespace('Auth')->group(function(){
		Route::post('login', 'LoginController@login');
		Route::post('register', 'RegisterController@register');

        Route::post('sendcode', 'RegisterController@sendcode');
        Route::post('username-check', 'RegisterController@usernameCheck');

	    Route::post('password/email', 'ForgotPasswordController@sendResetCodeEmail');
	    Route::post('password/verify-code', 'ForgotPasswordController@verifyCode');

	    Route::post('password/reset', 'ResetPasswordController@reset');
	});


	Route::middleware('auth.api:sanctum')->name('user.')->prefix('user')->group(function(){
		Route::get('logout', 'Auth\LoginController@logout');
		Route::get('authorization', 'AuthorizationController@authorization')->name('authorization');
	    Route::get('resend-verify', 'AuthorizationController@sendVerifyCode')->name('send.verify.code');
	    Route::post('verify-email', 'AuthorizationController@emailVerification')->name('verify.email');
	    Route::post('verify-sms', 'AuthorizationController@smsVerification')->name('verify.sms');
	    Route::post('verify-g2fa', 'AuthorizationController@g2faVerification')->name('go2fa.verify');

	    Route::middleware(['checkStatusApi'])->group(function(){
	    	Route::get('dashboard', 'DashboardController@dashboard');
	    	Route::get('categories', 'DashboardController@categorys');
	    	Route::get('productDetails/{id}', 'DashboardController@productDetails');

	    	Route::post('createPin', 'DashboardController@createPin');
	    	Route::post('validatePin', 'DashboardController@validatePin');

	    	Route::get('cart', 'CartController@cart');
	    	Route::post('addToCart', 'CartController@addToCart');
            Route::get('remove_cart_item/{id}', 'CartController@removeCartItem');
            Route::post('shippingFee', 'CartController@calculatedelivery');
            Route::post('validate_coupon', 'CouponController@applyCoupon');
            Route::post('checkout', 'OrderController@confirmOrder');

            Route::get('orders', 'OrderController@orders');
            Route::get('order/{order_number}', 'OrderController@orderDetails');

            Route::get('buy-airtime', 'OrderController@airtime');
            Route::post('buy-airtime', 'OrderController@airtimebuy');

            Route::get('buy-data', 'OrderController@internet');
            Route::post('buy-data', 'OrderController@internetbuy');

            Route::get('cabletv', 'OrderController@cabletv');
            Route::post('validate-iuc', 'OrderController@validatedecoder');
            Route::post('pay-cabletv', 'OrderController@decoderpay');

            Route::get('power', 'OrderController@utility');
            Route::post('validate-meter', 'OrderController@validatebill');
            Route::post('pay-meter', 'OrderController@billpay');

//Wishlist
            Route::post('add_to_wishlist', 'WishlistController@addToWishList');
            Route::get('get_wishlist_data', 'WishlistController@getWsihList');
            Route::get('wishlist/remove/{id}', 'WishlistController@removeFromwishList');


            Route::post('password/reset', 'ResetPasswordController@reset');

            Route::post('profile-setting', 'UserController@submitProfile');
            Route::post('change-password', 'UserController@submitPassword');

            // Withdraw
            Route::get('withdraw/methods', 'UserController@withdrawMethods');
            Route::post('withdraw/store', 'UserController@withdrawStore');
            Route::post('withdraw/confirm', 'UserController@withdrawConfirm');
            Route::get('withdraw/history', 'UserController@withdrawLog');


            // Deposit
            Route::get('deposit/methods', 'PaymentController@depositMethods');
            Route::post('deposit/insert', 'PaymentController@depositInsert');
            Route::get('deposit/confirm', 'PaymentController@depositConfirm');

            Route::get('deposit/manual', 'PaymentController@manualDepositConfirm');
            Route::post('deposit/manual', 'PaymentController@manualDepositUpdate');

            Route::get('deposit/history', 'UserController@depositHistory');

            Route::get('transactions', 'UserController@transactions');

            Route::get('wallet/balance', 'UserController@walletBalance');

            Route::get('address', 'UserController@getAddress');
            Route::post('address', 'UserController@submitAddress');
            Route::post('address/setdefault/{id}', 'UserController@addressSetDefault');

	    });
	});
});
