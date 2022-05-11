<?php

namespace App\Providers;

use App\Models\AdminNotification;
use App\Models\Category;
use App\Models\Deposit;
use App\Models\Frontend;
use App\Models\GeneralSetting;
use App\Models\Language;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Seller;
use App\Models\SupportTicket;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
//        $this->app['request']->server->set('HTTPS', true);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $activeTemplate = activeTemplate();
        $general = GeneralSetting::first();
        $viewShare['general'] = $general;
        $viewShare['activeTemplate'] = $activeTemplate;
        $viewShare['activeTemplateTrue'] = activeTemplate(true);
        $viewShare['language'] = Language::all();

        $viewShare['allCategories']= Category::with(['allSubcategories','products'=> function($q){
                return $q->whereHas('categories')->whereHas('brand');
            }, 'products.reviews','products.offer.activeOffer'])
            ->where('parent_id', null)->get();

        view()->share($viewShare);


        view()->composer('admin.partials.sidenav', function ($view) {
            $view->with([
                'banned_users_count'           => User::banned()->count(),
                'email_unverified_users_count' => User::emailUnverified()->count(),
                'sms_unverified_users_count'   => User::smsUnverified()->count(),
                'banned_sellers_count'           => Seller::banned()->count(),
                'email_unverified_sellers_count' => Seller::emailUnverified()->count(),
                'sms_unverified_sellers_count'   => Seller::smsUnverified()->count(),
                'pending_ticket_count'          => SupportTicket::whereIN('status', [0,2])->count(),
                'pending_deposits_count'        => Deposit::pending()->count(),
                'pending_withdraw_count'        => Withdrawal::pending()->count(),
                'pending_product_count'         => Product::pending()->count(),
                'pending_orders_count'          => Order::where('status', 0)->where('payment_status',  '!=' ,0)->count(),
                'processing_orders_count'       => Order::where('status', 1)->where('payment_status','!=', 0)->count(),
                'dispatched_orders_count'       => Order::where('status', 2)->where('payment_status','!=', 0)->count(),
                'seller_pending_product'        => Product::where([['status', 0],['seller_id','!=',0]])
                                                   ->count()

            ]);
        });
        view()->composer('seller.partials.sidenav', function ($view) {
            $view->with([
                'pending_product_count'         => Product::pending()->count(),
                'pending_orders_count'          => OrderDetail::where('seller_id',seller()->id)->whereHas('order',function($q){
                                                        $q->where('status', 0)->where('payment_status',  '!=' ,0);
                                                    })->count(),

                'processing_orders_count'       => OrderDetail::where('seller_id',seller()->id)->whereHas('order',function($q){
                                                        $q->where('status', 1)->where('payment_status',  '!=' ,0);
                                                    })->count(),
                'dispatched_orders_count'       => OrderDetail::where('seller_id',seller()->id)->whereHas('order',function($q){
                                                        $q->where('status', 2)->where('payment_status',  '!=' ,0);
                                                    })->count(),


            ]);
        });

        view()->composer('admin.partials.topnav', function ($view) {
            $view->with([
                'adminNotifications'=>AdminNotification::where('read_status',0)->with('user', 'seller')->orderBy('id','desc')->get(),
            ]);
        });


        view()->composer('partials.seo', function ($view) {
            $seo = Frontend::where('data_keys', 'seo.data')->first();
            $view->with([
                'seo' => $seo ? $seo->data_values : $seo,
            ]);
        });

//        if($general->force_ssl){
//            \URL::forceScheme('https');
//        }

        Paginator::useBootstrap();
    }
}
