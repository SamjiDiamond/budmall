@php
    $footer    = getContent('footer.content', true);
    if($footer)
    $footer    = $footer->data_values;

    $categories = \App\Models\Category::where('is_top', 1)->inRandomOrder()->take(6)->get();
    $topBrands =  \App\Models\Brand::top()->inRandomOrder()->take(6)->get();

@endphp
<!--Start of Tawk.to Script-->
<script type="text/javascript">
var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/61bc4cf0c82c976b71c1e00a/1fn3op43v';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();
</script>
<!--End of Tawk.to Script--> 
<footer class="section-bg bg--dark">
    <div class="container">
        <div class="padding-bottom padding-top">
            <div class="row gy-5">
                <div class="col-lg-6">
                    <div class="row justify-content-between g-4">
                        <div class="col-md-5">
                            <div class="footer__widget footer__widget-about">
                                <div class="logos">
                                    <a href="{{ route('home') }}">
                                        <img src="{{getImage(imagePath()['logoIcon']['path'] .'/logo_2.png')}}" width="90" alt="logo">
                                    </a>
                                </div>
                                <p class="addr">
                                    <br>
                              {{--      @lang(@$footer->footer_note) --}}
                               Nigeria Address: Bud Logistic Limited. 7b, Ondo Street, Osborne Foreshore Estate, Ikoyi, Lagos, Nigeria<hr>
                    US Address: Bud Infrastructure LLC 444 Alaska Avenue, Suite #BHD985, Torrance, CA 90503 USA.
                                </p>

                                @php
                                    $socials    = getContent('social_media_links.element');
                                @endphp

                                <ul class="social__icons">
                                    @if($socials->count() >0)
                                        @foreach ($socials as $item)
                                        <li>
                                            <a href="{{ $item->data_values->url }}" target="blank">
                                                @php
                                                    echo $item->data_values->social_icon
                                                @endphp
                                            </a>
                                        </li>
                                        @endforeach
                                    @endif
                                </ul>
                            </div>
                        </div>

                        <div class="col-md-7 col-xl-6">
                            <div class="footer__widget widget__info">
                                <h5 class="widget--title text-white">@lang('Contact Us')</h5>
                                <div>
                                    <div class="contact__info">
                                        <div class="icon">
                                            <i class="las la-headset"></i>
                                        </div>
                                        <div class="content">
                                            <h6 class="contact__info-title  text-white">
                                                <a href="Tel:{{ @$footer->cell_number }}" class=" text-white">{{ @$footer->cell_number }}</a>
                                            </h6>
                                            <span class="info  text-white">{{ @$footer->time }}</span>
                                        </div>
                                    </div>
                                    <div class="contact__info style-two">
                                        <div class="icon">
                                            <i class="las la-envelope-open"></i>
                                        </div>
                                        <div class="content">
                                            <h6 class="contact__info-title  text-white">
                                                <a href="mailto:{{ @$footer->email }}" class=" text-white">{{ @$footer->email }}</a>
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="row g-4 pl-xl-5">
                        <div class="col-lg-4 col-6">
                            <div class="footer__widget">
                                <h5 class="widget--title  text-white">@lang('Accounts')</h5>
                                <ul class="footer__links ">
                                    <li style="color:white">
                                        <a href="{{ route('user.login') }}">@lang('Login as Customer')</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('user.register') }}">@lang('Register as Customer')</a>
                                    </li>
                                    
                                </ul>
                            </div>
                        </div>

                        @php
                            $pages  = \App\Models\Frontend::where('data_keys', 'pages.element')->get();
                        @endphp
                        <div class="col-lg-4 col-md-6">
                            <div class="footer__widget">
                                <h5 class="widget--title  text-white">@lang('Useful Links')</h5>
                                <ul class="footer__links  text-white">
                                    @if($pages->count() > 0)
                                        @foreach ($pages as $item)
                                            <li class=" text-white"><a href="{{route('page.details', [$item->id, slug($item->data_values->pageTitle)])}}">@php echo __($item->data_values->pageTitle) @endphp</a></li>
                                        @endforeach
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom body-bg text-center  bg--dark">
        <div class="container">
            <div class="d-flex flex-wrap justify-content-around justify-content-lg-between align-items-center">
                <div class="left py-2">
                   
                   {{ __(@$footer->copyright_text) }} 
                </div>
                <div class="right py-2">
                    @isset($footer->payment_methods)
                    <img src="{{ getImage('assets/images/frontend/footer/'.@$footer->payment_methods, "250x30")}}" alt="@lang('footer')">
                    @endisset
                </div>
            </div>
        </div>
    </div>
</footer>


<div class="modal fade" id="quickView">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content py-4">
            <button type="button" class="close modal-close-btn" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <div class="modal-body">
                <div class="ajax-loader-wrapper d-flex align-items-center justify-content-center">
                    <div class="spinner-border" role="status">
                      <span class="sr-only">@lang('Loading')...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
