<?php

use Illuminate\Support\Facades\Route;

Route::get('/clear', function(){
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
});

/*
====================Payment Gateways====================
*/
Route::namespace('Gateway')->prefix('ipn')->name('ipn.')->group(function () {
    Route::post('paypal', 'Paypal\ProcessController@ipn')->name('Paypal');
    Route::get('paypal-sdk', 'PaypalSdk\ProcessController@ipn')->name('PaypalSdk');
    Route::post('perfect-money', 'PerfectMoney\ProcessController@ipn')->name('PerfectMoney');
    Route::post('stripe', 'Stripe\ProcessController@ipn')->name('Stripe');
    Route::post('stripe-js', 'StripeJs\ProcessController@ipn')->name('StripeJs');
    Route::post('stripe-v3', 'StripeV3\ProcessController@ipn')->name('StripeV3');
    Route::any('ipnpaid/stripe-v3', 'StripeV3\ProcessController@ipnpaid')->name('stripepaid');
    Route::any('ipndeposit/stripe-v3', 'StripeV3\ProcessController@ipndeposit')->name('stripedeposit');
    Route::post('skrill', 'Skrill\ProcessController@ipn')->name('Skrill');
    Route::post('paytm', 'Paytm\ProcessController@ipn')->name('Paytm');
    Route::post('payeer', 'Payeer\ProcessController@ipn')->name('Payeer');
    Route::post('paystack', 'Paystack\ProcessController@ipn')->name('Paystack');
    Route::post('voguepay', 'Voguepay\ProcessController@ipn')->name('Voguepay');
    Route::get('flutterwave/{trx}/{type}', 'Flutterwave\ProcessController@ipn')->name('Flutterwave');
    Route::post('razorpay', 'Razorpay\ProcessController@ipn')->name('Razorpay');
    Route::post('instamojo', 'Instamojo\ProcessController@ipn')->name('Instamojo');
    Route::get('blockchain', 'Blockchain\ProcessController@ipn')->name('Blockchain');
    Route::get('blockio', 'Blockio\ProcessController@ipn')->name('Blockio');
    Route::post('coinpayments', 'Coinpayments\ProcessController@ipn')->name('Coinpayments');
    Route::post('coinpayments-fiat', 'Coinpayments_fiat\ProcessController@ipn')->name('CoinpaymentsFiat');
    Route::post('coingate', 'Coingate\ProcessController@ipn')->name('Coingate');
    Route::post('coinbase-commerce', 'CoinbaseCommerce\ProcessController@ipn')->name('CoinbaseCommerce');
    Route::get('mollie', 'Mollie\ProcessController@ipn')->name('Mollie');
    Route::post('cashmaal', 'Cashmaal\ProcessController@ipn')->name('Cashmaal');
    Route::post('authorize-net', 'AuthorizeNet\ProcessController@ipn')->name('AuthorizeNet');
    Route::post('2check-out', 'TwoCheckOut\ProcessController@ipn')->name('TwoCheckOut');
    Route::post('mercado-pago', 'MercadoPago\ProcessController@ipn')->name('MercadoPago');

/*
====================Payment Gateways====================
*/
    Route::get('deposit/flutterwave/{trx}/{type}', 'Flutterwave\DepositController@ipn')->name('Flutterwave');
    Route::post('deposit/StripeJs', 'StripeJs\DepositController@ipn')->name('deposit.StripeJs');
});

// Guest Support Ticket or Contact Message
Route::prefix('ticket')->group(function () {
    Route::get('/', 'TicketController@index')->name('ticket');
    Route::get('/new', 'TicketController@openNewTicket')->name('ticket.open');
    Route::post('/create', 'TicketController@store')->name('ticket.store');
    Route::get('/view/{ticket}', 'TicketController@viewTicket')->name('ticket.view');

    Route::get('/view-message/{ticket}', 'TicketController@viewGuestTicket')->name('ticket.view.guest');

    Route::post('/reply/{ticket}', 'TicketController@reply')->name('ticket.reply');
    Route::get('/download/{ticket}', 'TicketController@ticketDownload')->name('ticket.download');
});

Route::post('/subscribe', 'SiteController@addSubscriber')->name('subscribe');
Route::get('/contact', 'SiteController@contact')->name('contact');
Route::post('/contact', 'SiteController@contactSubmit');
Route::get('/change/{lang?}', 'SiteController@changeLanguage')->name('lang');
Route::get('/currency/{lang?}', 'SiteController@changecur')->name('cur');


 Route::middleware('currency')->group(function () {
// Products
Route::get('products', 'ShopController@products')->name('products');
Route::get('products/filter', 'ShopController@products')->name('products.filter');

Route::get('product/details/{id}/{order_id?}', 'ShopController@productDetails')->name('product.details');
Route::get('product/detail/{id}/{slug}/', 'ShopController@productDetails')->name('product.detail');
Route::get('product/get-stock-by-variant/', 'ShopController@getStockByVariant')->name('product.get-stock-by-variant');
Route::get('product/get-image-by-variant/', 'ShopController@getImageByVariant')->name('product.get-image-by-variant');
Route::get('/products/search/', 'ShopController@productSearch')->name('product.search');
Route::get('/products/search/{perpage?}', 'ShopController@productSearch')->name('product.search.filter');
Route::get('product/load_review', 'ShopController@loadMore')->name('product_review.load_more');
Route::get('quick-view/', 'ShopController@quickView')->name('quick-view');

//Cart
Route::post('add-to-cart/', 'CartController@addToCart')->name('add-to-cart');
Route::get('cart-data', 'CartController@getCart')->name('get-cart-data');
Route::get('get_cart-total/', 'CartController@getCartTotal')->name('get-cart-total');
Route::get('my-cart/', 'CartController@shoppingCart')->name('shopping-cart');
Route::post('apply_coupon/', 'CouponController@applyCoupon')->name('applyCoupon');
Route::post('remove_coupon/', 'CouponController@removeCoupon')->name('removeCoupon');
Route::post('remove_cart_item/{id}', 'CartController@removeCartItem')->name('remove-cart-item');
Route::post('update_cart_item/{id}', 'CartController@updateCartItem')->name('update-cart-item');

//Wishlist
Route::get('add_to_wishlist/', 'WishlistController@addToWishList')->name('add-to-wishlist');
Route::get('get_wishlist_data/', 'WishlistController@getWsihList')->name('get-wishlist-data');
Route::get('get_wishlist_total/', 'WishlistController@getWsihListTotal')->name('get-wishlist-total');
Route::get('wishlist/', 'WishlistController@wishList')->name('wishlist');
Route::get('wishlist/remove/{id}', 'WishlistController@removeFromwishList')->name('removeFromWishlist')->where('id', '[0-9]+');

//Compare
Route::get('add_to_compare/', 'ShopController@addToCompare')->name('addToCompare');
Route::get('get_compare_data/', 'ShopController@getCompare')->name('get-compare-data');
Route::get('compare/', 'ShopController@compare')->name('compare');
Route::post('remove_from_compare/{id}', 'ShopController@removeFromCompare')->name('del-from-compare');


// Categories
Route::get('categories', 'ShopController@categories')->name('categories');
Route::get('category/{id}/{slug}', 'ShopController@productsByCategory')->name('products.category');
Route::get('category/filter/{id}/{slug}', 'ShopController@productsByCategory')->name('category.filter')->where('id', '[0-9]+');

// Brands
Route::get('brands', 'ShopController@brands')->name('brands');
Route::get('brands/{id}/{slug}', 'ShopController@productsByBrand')->name('products.brand')->where('id', '[0-9]+');
Route::get('brands/filter/{id}/{slug}', 'ShopController@productsByBrand')->name('brands.filter')->where('id', '[0-9]+');

Route::get('our-sellers', 'ShopController@allSellers')->name('all.sellers');
Route::get('seller/{id}-{slug}', 'ShopController@sellerDetails')->name('seller.details');

Route::get('print/{order}', 'OrderController@printInvoice')->name('print.invoice');
Route::get('track-order', 'OrderController@trackOrder')->name('orderTrack');
Route::post('track-order', 'OrderController@getOrderTrackData')->name('order-track');

Route::get('cookie/accept', 'SiteController@cookieAccept')->name('cookie.accept');
Route::get('blog/{id}/{slug}', 'SiteController@blogDetails')->name('blog.details');
Route::get('pages/{id}/{slug}', 'SiteController@pageDetails')->name('page.details');
Route::get('placeholder-image/{size}', 'SiteController@placeholderImage')->name('placeholder.image');

Route::get('/{slug}', 'SiteController@pages')->name('pages');
Route::get('/', 'SiteController@index')->name('home');
Route::get('/test-mail/send', 'SiteController@testemail');
//Route::get('/migrate/customer', 'SiteController@migratecustomer');
//Route::get('/migrate/subcat', 'SiteController@migratesubcat');
//Route::get('/trigger/staffemail', 'SiteController@staffemail');
//Route::get('/trigger/orderemail', 'SiteController@orderemail');
//Route::get('/trigger/analytics', 'SiteController@triggeranalitics');
 });
