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

//Wishlist
            Route::get('add_to_wishlist/', 'WishlistController@addToWishList')->name('add-to-wishlist');
            Route::get('get_wishlist_data/', 'WishlistController@getWsihList')->name('get-wishlist-data');
            Route::get('get_wishlist_total/', 'WishlistController@getWsihListTotal')->name('get-wishlist-total');
            Route::get('wishlist/', 'WishlistController@wishList')->name('wishlist');
            Route::get('wishlist/remove/{id}', 'WishlistController@removeFromwishList')->name('removeFromWishlist')->where('id', '[0-9]+');


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

	    });
	});
});
