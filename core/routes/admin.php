<?php

use Illuminate\Support\Facades\Route;

Route::namespace('Admin')->name('admin.')->group(function () {

    Route::namespace('Auth')->group(function () {
        Route::get('/', 'LoginController@showLoginForm')->name('login');
        Route::post('/', 'LoginController@login')->name('login');
        Route::get('logout', 'LoginController@logout')->name('logout');

        // Admin Password Reset
        Route::get('password/reset', 'ForgotPasswordController@showLinkRequestForm')->name('password.reset');
        Route::post('password/reset', 'ForgotPasswordController@sendResetCodeEmail');
        Route::post('password/verify-code', 'ForgotPasswordController@verifyCode')->name('password.verify.code');
        Route::get('password/reset/{token}', 'ResetPasswordController@showResetForm')->name('password.reset.form');
        Route::post('password/reset/change', 'ResetPasswordController@reset')->name('password.change');
    });


    Route::middleware('admin')->group(function () {
        Route::get('analytics/all-shop', 'AdminController@dashboard')->name('dashboard');
        
        Route::get('profile', 'AdminController@profile')->name('profile');
        Route::post('profile', 'AdminController@profileUpdate')->name('profile.update');
        Route::get('password', 'AdminController@password')->name('password');
        Route::post('password', 'AdminController@passwordUpdate')->name('password.update');
        
        Route::middleware('superadmin')->group(function () {
        Route::get('analytics/my-shop', 'AdminController@dashboardSelf')->name('dashboard.self');
        
        //Notification
        Route::get('notifications','AdminController@notifications')->name('notifications');
        Route::get('notification/read/{id}','AdminController@notificationRead')->name('notification.read');
        Route::get('notifications/read-all','AdminController@readAll')->name('notifications.readAll');

        //Report Bugs
        Route::get('request-report','AdminController@requestReport')->name('request.report');
        Route::post('request-report','AdminController@reportSubmit');

        Route::get('system-info','AdminController@systemInfo')->name('system.info');


        // Users Manager
        Route::get('dispatch/staff', 'ManageUsersController@allDispatch')->name('staff.dispatch');
        Route::get('sub-admin/staff', 'ManageUsersController@allStaff')->name('staff.staff');
        Route::post('add/staff', 'ManageUsersController@addstaff')->name('users.addstaff');
        Route::get('delete/staff/{id}', 'ManageUsersController@deletestaff')->name('staff.deletestaff');
        Route::get('edit/staff/{id}', 'ManageUsersController@editstaff')->name('staff.editstaff');
        Route::post('edit/staff/{id}', 'ManageUsersController@editstaffpost')->name('staff.editupdate');
        Route::get('staff/send-email/{id}', 'ManageUsersController@staffshowEmailSingleForm')->name('staff.email.single');
        Route::post('staff/send-email/{id}', 'ManageUsersController@staffsendEmailSingle')->name('staff.email.single');
        Route::get('staff/email-logs/{id}', 'ManageUsersController@staffemailLog')->name('staff.email.log');
        Route::get('staff/email-details/{id}', 'ManageUsersController@staffemailDetails')->name('staff.email.details');
        Route::post('credit/staff/{id}', 'ManageUsersController@creditstaff')->name('staff.credit');
        Route::get('rcedit-log/staff/{id}', 'ManageUsersController@creditstafflog')->name('staff.creditlog');
        Route::post('debit/staff/{id}', 'ManageUsersController@debitstaff')->name('staff.debit');
        Route::get('debit-log/staff/{id}', 'ManageUsersController@debitstafflog')->name('staff.debitlog');

        Route::get('sales/consultant', 'ManageUsersController@allconsultant')->name('sales.consultant');
        Route::get('users', 'ManageUsersController@allUsers')->name('users.all');
        Route::get('users/active', 'ManageUsersController@activeUsers')->name('users.active');
        Route::get('users/banned', 'ManageUsersController@bannedUsers')->name('users.banned');
        Route::get('users/email-verified', 'ManageUsersController@emailVerifiedUsers')->name('users.email.verified');
        Route::get('users/email-unverified', 'ManageUsersController@emailUnverifiedUsers')->name('users.email.unverified');
        Route::get('users/sms-unverified', 'ManageUsersController@smsUnverifiedUsers')->name('users.sms.unverified');
        Route::get('users/sms-verified', 'ManageUsersController@smsVerifiedUsers')->name('users.sms.verified');

        Route::get('users/{scope}/search', 'ManageUsersController@search')->name('users.search');
        Route::get('user-detail/{id}', 'ManageUsersController@detail')->name('users.detail');
        Route::post('user-update/{id}', 'ManageUsersController@update')->name('users.update');
        Route::post('user/add-sub-balance/{id}', 'ManageUsersController@addSubBalance')->name('users.add.sub.balance');
        Route::get('user/send-email/{id}', 'ManageUsersController@showEmailSingleForm')->name('users.email.single');
        Route::post('user/send-email/{id}', 'ManageUsersController@sendEmailSingle')->name('users.email.single');
        Route::get('user/login/{id}', 'ManageUsersController@login')->name('users.login');
        Route::get('user/transactions/{id}', 'ManageUsersController@transactions')->name('users.transactions');
        Route::get('user/deposits/{id}', 'ManageUsersController@deposits')->name('users.deposits');

        Route::get('report/user_order/{id}', 'ReportController@orderByUser')->name('report.order.user');
        Route::get('report/user_order/{id}/search', 'ReportController@userOrderSearch')->name('report.order.user_search');

        Route::get('user/deposits/via/{method}/{type?}/{userId}', 'ManageUsersController@depositViaMethod')->name('users.deposits.method');



        // Login History
        Route::get('users/login/history/{id}', 'ManageUsersController@userLoginHistory')->name('users.login.history.single');

        Route::get('users/send-email', 'ManageUsersController@showEmailAllForm')->name('users.email.all');
        Route::post('users/send-email', 'ManageUsersController@sendEmailAll')->name('users.email.send');
        Route::get('users/email-logs/{id}', 'ManageUsersController@emailLog')->name('users.email.log');
        Route::get('users/email-details/{id}', 'ManageUsersController@emailDetails')->name('users.email.details');


        // Manage Sellers

        Route::get('sellers', 'ManageSellerController@allSeller')->name('sellers.all');
        Route::get('sellers/active', 'ManageSellerController@activeSeller')->name('sellers.active');
        Route::get('sellers/banned', 'ManageSellerController@bannedSeller')->name('sellers.banned');
        Route::get('sellers/email-verified', 'ManageSellerController@emailVerifiedSeller')->name('sellers.email.verified');
        Route::get('sellers/email-unverified', 'ManageSellerController@emailUnverifiedSeller')->name('sellers.email.unverified');
        Route::get('sellers/sms-unverified', 'ManageSellerController@smsUnverifiedSeller')->name('sellers.sms.unverified');
        Route::get('sellers/sms-verified', 'ManageSellerController@smsVerifiedSeller')->name('sellers.sms.verified');

        Route::get('sellers/{scope}/search', 'ManageSellerController@search')->name('sellers.search');
        Route::get('seller/detail/{id}', 'ManageSellerController@detail')->name('sellers.detail');
        Route::post('seller/update/{id}', 'ManageSellerController@update')->name('sellers.update');
        Route::post('seller/add-sub-balance/{id}', 'ManageSellerController@addSubBalance')->name('sellers.add.sub.balance');
        Route::get('seller/send-email/{id}', 'ManageSellerController@showEmailSingleForm')->name('sellers.email.single');
        Route::post('seller/send-email/{id}', 'ManageSellerController@sendEmailSingle');
        Route::get('seller/login/{id}', 'ManageSellerController@login')->name('sellers.login');
        Route::get('seller/transactions/{id}', 'ManageSellerController@transactions')->name('sellers.transactions');

        Route::get('sellers/email-details/{id}', 'ManageSellerController@emailDetails')->name('sellers.email.details');

        Route::get('seller/withdrawals/{id}', 'ManageSellerController@withdrawals')->name('sellers.withdrawals');
        Route::get('seller/withdrawals/via/{method}/{type?}/{userId}', 'ManageSellerController@withdrawalsViaMethod')->name('sellers.withdrawals.method');
        Route::get('seller/login/history/{id}', 'ManageSellerController@sellerLoginHistory')->name('sellers.login.history.single');


        Route::get('seller/shop-detail/{id}', 'ManageSellerController@shopDetails')->name('sellers.shop.details');
        Route::post('/shop-update', 'ManageSellerController@shopUpdate')->name('sellers.shop.update');
        Route::get('seller/send-email', 'ManageSellerController@showEmailAllForm')->name('sellers.email.all');
        Route::post('seller/send-email', 'ManageSellerController@sendEmailAll')->name('sellers.email.send');
        Route::get('seller/email-log/{id}', 'ManageSellerController@emailLog')->name('sellers.email.log');
        Route::get('seller/email-details/{id}', 'ManageSellerController@emailDetails')->name('sellers.email.details');
        Route::post('seller/feature-status/{id}', 'ManageSellerController@featureStatus')->name('sellers.feature');
        Route::get('seller/sell-logs/{id}', 'ManageSellerController@sellLogs')->name('sellers.sell.logs');
        Route::get('seller/products/{id}', 'ManageSellerController@sellerProducts')->name('sellers.products');
        Route::post('seller/add-sub-balance/{id}', 'ManageSellerController@addSubBalance')->name('sellers.addSubBalance');

        //Coupons
        Route::get('promotion/coupons', 'ManageCouponsController@index')->name('coupon.index');
        Route::get('promotion/coupon/create', 'ManageCouponsController@create')->name('coupon.create');
        Route::get('promotion/coupon/edit/{id}', 'ManageCouponsController@edit')->name('coupon.edit');
        Route::post('promotion/coupon/save/{id}', 'ManageCouponsController@save')->name('coupon.store');
        Route::post('promotion/coupon/delete/{id}', 'ManageCouponsController@delete')->name('coupon.delete');
        Route::post('promotion/coupon-status', 'ManageCouponsController@changeStatus')->name('coupon.status');
        Route::get('products_for_coupon', 'ManageCouponsController@prordutsForCoupon')->name('products_for_coupon');

        //Offers
        Route::get('promotion/offers', 'ManageOffersController@index')->name('offer.index');
        Route::get('promotion/offer/create', 'ManageOffersController@create')->name('offer.create');
        Route::get('promotion/offer/edit/{id}', 'ManageOffersController@edit')->name('offer.edit');
        Route::post('promotion/offer/save/{id}', 'ManageOffersController@save')->name('offer.store');
        Route::post('promotion/offer/delete/{id}', 'ManageOffersController@delete')->name('offer.delete');
        Route::get('products_for_offer', 'ManageOffersController@prordutsForOffer')->name('products_for_offer');
        Route::post('promotion/offerstatus', 'ManageOffersController@changeStatus')->name('offer.status');

        // Subscriber
        Route::get('subscriber', 'SubscriberController@index')->name('subscriber.index');
        Route::get('subscriber/send-email', 'SubscriberController@sendEmailForm')->name('subscriber.sendEmail');
        Route::post('subscriber/remove', 'SubscriberController@remove')->name('subscriber.remove');
        Route::post('subscriber/send-email', 'SubscriberController@sendEmail')->name('subscriber.sendEmail');

        // Deposit Gateway
        Route::name('gateway.')->prefix('gateway')->group(function(){
            // Automatic Gateway
            Route::get('automatic', 'GatewayController@index')->name('automatic.index');
            Route::get('automatic/edit/{alias}', 'GatewayController@edit')->name('automatic.edit');
            Route::post('automatic/update/{code}', 'GatewayController@update')->name('automatic.update');
            Route::post('automatic/remove/{code}', 'GatewayController@remove')->name('automatic.remove');
            Route::post('automatic/activate', 'GatewayController@activate')->name('automatic.activate');
            Route::post('automatic/deactivate', 'GatewayController@deactivate')->name('automatic.deactivate');

            // Manual Methods
            Route::get('manual', 'ManualGatewayController@index')->name('manual.index');
            Route::get('manual/new', 'ManualGatewayController@create')->name('manual.create');
            Route::post('manual/new', 'ManualGatewayController@store')->name('manual.store');
            Route::get('manual/edit/{alias}', 'ManualGatewayController@edit')->name('manual.edit');
            Route::post('manual/update/{id}', 'ManualGatewayController@update')->name('manual.update');
            Route::post('manual/activate', 'ManualGatewayController@activate')->name('manual.activate');
            Route::post('manual/deactivate', 'ManualGatewayController@deactivate')->name('manual.deactivate');
        });

        // DEPOSIT SYSTEM
        Route::name('deposit.')->prefix('payments')->group(function(){
            Route::get('/', 'DepositController@deposit')->name('list');
            Route::get('pending', 'DepositController@pending')->name('pending');
            Route::get('rejected', 'DepositController@rejected')->name('rejected');
            Route::get('approved', 'DepositController@approved')->name('approved');
            Route::get('successful', 'DepositController@successful')->name('successful');
            Route::get('details/{id}', 'DepositController@details')->name('details');
            Route::post('reject', 'DepositController@reject')->name('reject');
            Route::post('approve', 'DepositController@approve')->name('approve');
            Route::get('via/{method}/{type?}', 'DepositController@depositViaMethod')->name('method');
            Route::get('/{scope}/search', 'DepositController@search')->name('search');
            Route::get('date-search/{scope}', 'DepositController@dateSearch')->name('dateSearch');
            
            
            Route::get('pending/deposits', 'DepositController@pendingdepo')->name('pendingdepo');
            Route::get('successful/deposits', 'DepositController@successfuldepo')->name('successfuldepo');
            Route::get('all/deposits', 'DepositController@listdepo')->name('listdepo');
        });

        // WITHDRAW SYSTEM
        Route::name('withdrawals.')->prefix('payouts')->group(function(){
            Route::get('pending', 'WithdrawalController@pending')->name('pending');
            Route::get('approved', 'WithdrawalController@approved')->name('approved');
            Route::get('rejected', 'WithdrawalController@rejected')->name('rejected');
            Route::get('log', 'WithdrawalController@log')->name('log');
            Route::get('via/{method_id}/{type?}', 'WithdrawalController@logViaMethod')->name('method');
            Route::get('{scope}/search', 'WithdrawalController@search')->name('search');
            Route::get('date-search/{scope}', 'WithdrawalController@dateSearch')->name('dateSearch');
            Route::get('details/{id}', 'WithdrawalController@details')->name('details');
            Route::post('approve', 'WithdrawalController@approve')->name('approve');
            Route::post('reject', 'WithdrawalController@reject')->name('reject');
        });

        Route::name('withdraw.')->prefix('payout')->group(function(){
            // Withdraw Method
            Route::get('method/', 'WithdrawMethodController@methods')->name('method.index');
            Route::get('method/create', 'WithdrawMethodController@create')->name('method.create');
            Route::post('method/create', 'WithdrawMethodController@store')->name('method.store');
            Route::get('method/edit/{id}', 'WithdrawMethodController@edit')->name('method.edit');
            Route::post('method/edit/{id}', 'WithdrawMethodController@update')->name('method.update');
            Route::post('method/activate', 'WithdrawMethodController@activate')->name('method.activate');
            Route::post('method/deactivate', 'WithdrawMethodController@deactivate')->name('method.deactivate');
        });

        // Report
        Route::get('report/commission-log', 'ReportController@commissionLogs')->name('report.commission.log');
        Route::get('report/transaction', 'ReportController@transaction')->name('report.transaction');
        Route::get('report/transaction/search', 'ReportController@transactionSearch')->name('report.transaction.search');
        Route::get('report/login/history', 'ReportController@loginHistory')->name('report.login.history');
        Route::get('report/seller/login/history', 'ReportController@sellerLoginHistory')->name('report.seller.login.history');
        Route::get('report/login/ipHistory/{ip}', 'ReportController@loginIpHistory')->name('report.login.ipHistory');
        Route::get('report/seller-login/ipHistory/{ip}', 'ReportController@sellerLoginIpHistory')->name('report.seller.login.ipHistory');
        Route::get('report/email/history', 'ReportController@emailHistory')->name('report.email.history');

        // Admin Support
        Route::get('tickets', 'SupportTicketController@tickets')->name('ticket');
        Route::get('tickets/pending', 'SupportTicketController@pendingTicket')->name('ticket.pending');
        Route::get('tickets/closed', 'SupportTicketController@closedTicket')->name('ticket.closed');
        Route::get('tickets/answered', 'SupportTicketController@answeredTicket')->name('ticket.answered');
        Route::get('tickets/view/{id}', 'SupportTicketController@ticketReply')->name('ticket.view');
        Route::post('ticket/reply/{id}', 'SupportTicketController@reply')->name('ticket.reply');
        Route::get('ticket/download/{ticket}', 'SupportTicketController@ticketDownload')->name('ticket.download');
        Route::post('ticket/delete', 'SupportTicketController@ticketDelete')->name('ticket.delete');

        // Language Manager
        Route::get('/language', 'LanguageController@langManage')->name('language.manage');
        Route::post('/language', 'LanguageController@langStore')->name('language.manage.store');
        Route::post('/language/delete/{id}', 'LanguageController@langDel')->name('language.manage.del');
        Route::post('/language/update/{id}', 'LanguageController@langUpdate')->name('language.manage.update');
        Route::get('/language/edit/{id}', 'LanguageController@langEdit')->name('language.key');
        Route::post('/language/import', 'LanguageController@langImport')->name('language.importLang');
        Route::post('language/store/key/{id}', 'LanguageController@storeLanguageJson')->name('language.store.key');
        Route::post('language/delete/key/{id}', 'LanguageController@deleteLanguageJson')->name('language.delete.key');
        Route::post('language/update/key/{id}', 'LanguageController@updateLanguageJson')->name('language.update.key');

        // General Setting
        Route::get('general-setting', 'GeneralSettingController@index')->name('setting.index');
        Route::post('general-setting', 'GeneralSettingController@update')->name('setting.update');
        Route::get('optimize', 'GeneralSettingController@optimize')->name('setting.optimize');

        // Logo-Icon
        Route::get('setting/logo-icon', 'GeneralSettingController@logoIcon')->name('setting.logo.icon');
        Route::post('setting/logo-icon', 'GeneralSettingController@logoIconUpdate')->name('setting.logo.icon');

        //Shipping Methods
        Route::get('shipping-methods', 'ShippingMethodsController@index')->name('shipping.methods');
        Route::get('shipping-methods/create', 'ShippingMethodsController@create')->name('shipping.methods.create');
        Route::post('shipping-methods/create/{id}', 'ShippingMethodsController@store')->name('shipping.methods.store')->where('id', '[0-9]+');
        Route::get('shipping-methods/edit/{id}', 'ShippingMethodsController@edit')->name('shipping.methods.edit')->where('id', '[0-9]+');
        Route::post('shipping-methods/delete/{id}', 'ShippingMethodsController@delete')->name('shipping.methods.delete')->where('id', '[0-9]+');
        Route::get('shipping-methods/status-change/', 'ShippingMethodsController@changeStatus')->name('shipping.methods.status-change');



        //Shipping Calculator
        Route::get('shipping-calculator', 'ShippingMethodsController@calcindex')->name('shipping.calculator');
        Route::get('shipping-calculator/create', 'ShippingMethodsController@createcalc')->name('shipping.calculator.create');
        Route::post('shipping-calculator/create/{id}', 'ShippingMethodsController@calcstore')->name('shipping.calculator.store')->where('id', '[0-9]+');
        Route::get('shipping-calculator/edit/{id}', 'ShippingMethodsController@calcedit')->name('shipping.calculator.edit')->where('id', '[0-9]+');
        Route::post('shipping-calculator/delete/{id}', 'ShippingMethodsController@calcdelete')->name('shipping.calculator.delete')->where('id', '[0-9]+');
        Route::get('shipping-calculator/status-change/', 'ShippingMethodsController@calcchangeStatus')->name('shipping.calculator.status-change');


        //Custom CSS
        Route::get('custom-css','GeneralSettingController@customCss')->name('setting.custom.css');
        Route::post('custom-css','GeneralSettingController@customCssSubmit');

        //Cookie
        Route::get('cookie','GeneralSettingController@cookie')->name('setting.cookie');
        Route::post('cookie','GeneralSettingController@cookieSubmit');

        // Plugin
        Route::get('extensions', 'ExtensionController@index')->name('extensions.index');
        Route::post('extensions/update/{id}', 'ExtensionController@update')->name('extensions.update');
        Route::post('extensions/activate', 'ExtensionController@activate')->name('extensions.activate');
        Route::post('extensions/deactivate', 'ExtensionController@deactivate')->name('extensions.deactivate');

        // Email Setting
        Route::get('email-template/global', 'EmailTemplateController@emailTemplate')->name('email.template.global');
        Route::post('email-template/global', 'EmailTemplateController@emailTemplateUpdate')->name('email.template.global');
        Route::get('email-template/setting', 'EmailTemplateController@emailSetting')->name('email.template.setting');
        Route::post('email-template/setting', 'EmailTemplateController@emailSettingUpdate')->name('email.template.setting');
        Route::get('email-template/index', 'EmailTemplateController@index')->name('email.template.index');
        Route::get('email-template/{id}/edit', 'EmailTemplateController@edit')->name('email.template.edit');
        Route::post('email-template/{id}/update', 'EmailTemplateController@update')->name('email.template.update');
        Route::post('email-template/send-test-mail', 'EmailTemplateController@sendTestMail')->name('email.template.test.mail');

        // SMS Setting
        Route::get('sms-template/global', 'SmsTemplateController@smsTemplate')->name('sms.template.global');
        Route::post('sms-template/global', 'SmsTemplateController@smsTemplateUpdate')->name('sms.template.global');
        Route::get('sms-template/setting','SmsTemplateController@smsSetting')->name('sms.templates.setting');
        Route::post('sms-template/setting', 'SmsTemplateController@smsSettingUpdate')->name('sms.template.setting');
        Route::get('sms-template/index', 'SmsTemplateController@index')->name('sms.template.index');
        Route::get('sms-template/edit/{id}', 'SmsTemplateController@edit')->name('sms.template.edit');
        Route::post('sms-template/update/{id}', 'SmsTemplateController@update')->name('sms.template.update');
        Route::post('email-template/send-test-sms', 'SmsTemplateController@sendTestSMS')->name('sms.template.test.sms');

        // SEO
        Route::get('seo', 'FrontendController@seoEdit')->name('seo');

        // Frontend
        Route::name('frontend.')->prefix('frontend')->group(function () {
            Route::get('templates', 'FrontendController@templates')->name('templates');
            Route::post('templates', 'FrontendController@templatesActive')->name('templates.active');
            Route::get('frontend-sections/{key}', 'FrontendController@frontendSections')->name('sections');
            Route::post('frontend-content/{key}', 'FrontendController@frontendContent')->name('sections.content');
            Route::get('frontend-element/{key}/{id?}', 'FrontendController@frontendElement')->name('sections.element');
            Route::post('remove', 'FrontendController@remove')->name('remove');
        });
        
        //Seller Log
        Route::get('sales-log/', 'OrderController@adminSellsLog')->name('order.sells.log.admin');
        Route::get('sales-log/seller', 'OrderController@sellerSellsLog')->name('order.sells.log.seller');
        });

        //Category Setting
        Route::get('product/categories', 'CategoryController@index')->name('category.all');
        Route::get('product/categories/trashed', 'CategoryController@trashed')->name('category.trashed');
        Route::get('product/categories/trashed/search/','CategoryController@categoryTrashedSearch')->name('category.trashed.search');
        Route::post('product/category/create/{id}', 'CategoryController@store')->name('category-store')->where('id', '[0-9]+');
        Route::post('product/category/delete/{id}', 'CategoryController@delete')->name('category.delete')->where('id', '[0-9]+');

        //Brand
        Route::get('product/brands', 'BrandController@index')->name('brand.all');
        Route::post('product/brand/create/{id}', 'BrandController@store')->name('brand.store');
        Route::post('product/brand/{id}', 'BrandController@delete')->name('brand.delete')->where('id', '[0-9]+');

        Route::get('product/brands/search/', 'BrandController@brandSearch')->name('brand.search');
        Route::get('product/brands/trashed', 'BrandController@trashed')->name('brand.trashed');
        Route::get('product/brands/trashed/search/', 'BrandController@brandTrashedSearch')->name('brand.trashed.search');
        Route::post('product/brand/set-top/', 'BrandController@setTop')->name('brand.settop');

        //Product Attributes
        Route::get('attribute-types', 'ProductAttributeController@index')->name('attributes');
        Route::get('attribute/create', 'ProductAttributeController@create')->name('attributes.create');
        Route::post('attribute/create/{id}', 'ProductAttributeController@store')->name('attributes.store');
        Route::get('attribute/edit/{id}/', 'ProductAttributeController@edit')->name('attributes.edit');
        Route::post('attribute/{id}', 'ProductAttributeController@delete')->name('attributes.delete');

        //Manage Products
        Route::get('product/all', 'ProductController@index')->name('products.all');
        Route::get('products-by-admin', 'ProductController@adminProducts')->name('products.admin');
        Route::get('products-by-seller', 'ProductController@sellerProducts')->name('products.seller');
        Route::get('products/pending', 'ProductController@pending')->name('products.pending');
        Route::get('product/create', 'ProductController@create')->name('products.create');
        Route::post('product/store/{id}', 'ProductController@store')->name('products.product-store');
        Route::get('product/edit/{id}/{slug}', 'ProductController@edit')->name('products.edit')->where('id', '[0-9]+');
        Route::post('product/delete/{id}', 'ProductController@delete')->name('products.delete')->where('id', '[0-9]+');
        Route::get('product/search/', 'ProductController@productSearch')->name('products.search');
        Route::get('product/trashed', 'ProductController@trashed')->name('products.trashed');
        Route::post('product-featured', 'ProductController@featured')->name('products.featured');
        Route::post('product-status/action', 'ProductController@statusAction')->name('products.action');
        Route::post('product-approve/all', 'ProductController@approveAll')->name('products.approve.all');

        Route::get('product/reviews', 'ProductController@reviews')->name('product.reviews');
        Route::get('product/reviews/trashed', 'ProductController@trashedReviews')->name('product.reviews.trashed');
        Route::get('product/reviews/search/{key?}', 'ProductController@reviewSearch')->name('product.reviews.search');
        Route::post('product/reviews/{id}', 'ProductController@reviewDelete')->name('product.review.delete');

        // Product Variant
        Route::get('product/add-variant/{id}', 'ProductController@addVariant')->name('products.variant.store');
        Route::post('product/add-variant/{id}', 'ProductController@storeVariant')->name('products.variant.store');
        Route::get('product/edit-variant/{pid}/{aid}', 'ProductController@editAttribute')->name('products.variant.edit');
        Route::post('product/edit-variant-update/{id}', 'ProductController@updateVariant')->name('products.variant.update');
        Route::post('product/delete-variant/{id}', 'ProductController@deleteVariant')->name('products.variant.delete');
        Route::get('product/add-variant-images/{id}', 'ProductController@addVariantImages')->name('products.add-variant-images');
        Route::post('product/add-variant-images/{id}', 'ProductController@saveVariantImages');
        Route::any('product/in_stock', 'ProductController@productstock')->name('product.stock');

        //Stock
        Route::any('product/stock/create/{product_id}', 'ProductStockController@stockCreate')->name('products.stock.create');
        Route::post('product/add-to-stock/{product_id}', 'ProductStockController@stockAdd')->name('products.stock.add');
        Route::get('product/stock/{id}/', 'ProductStockController@stockLog')->name('products.stock.log');
        Route::get('product/stocks', 'ProductStockController@stocks')->name('products.stocks');
        Route::get('product/stocks/low', 'ProductStockController@stocksLow')->name('products.stocks.low');
        Route::get('product/stocks/empty', 'ProductStockController@stocksEmpty')->name('products.stocks.empty');

        //Order
        Route::post('orders', 'OrderController@changeStatus')->name('order.status');
        Route::get('orders/', 'OrderController@ordered')->name('order.index');
        Route::get('orders/pending', 'OrderController@pending')->name('order.to_deliver');
        Route::get('orders/processing', 'OrderController@onProcessing')->name('order.on_processing');
        Route::get('orders/dipatched', 'OrderController@dispatched')->name('order.dispatched');
        Route::get('orders/delivered', 'OrderController@deliveredOrders')->name('order.delivered');
        Route::get('orders/canceled', 'OrderController@canceledOrders')->name('order.canceled');
        Route::get('orders/cod', 'OrderController@codOrders')->name('order.cod');
        Route::post('orders/send_cancelation_alert/{id}', 'OrderController@orderSendCancalationAlert')->name('order.send_cancelation_alert');
        Route::get('order/details/{id}', 'OrderController@orderDetails')->name('order.details');
        Route::get('debit-log', 'ManageUsersController@mydebitstafflog')->name('mydebitlog');
         Route::get('credit-log', 'ManageUsersController@mycreditlog')->name('mycreditlog');


        //Bill Payments
        Route::get('airtime', 'OrderController@airtime')->name('bills.airtime');
        Route::get('internet', 'OrderController@internet')->name('bills.internet');
        Route::get('cabletv', 'OrderController@cabletv')->name('bills.cabletv');
        Route::get('utility', 'OrderController@utility')->name('bills.utility');



    });
});
