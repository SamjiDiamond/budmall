@extends($activeTemplate.'layouts.frontend')
@section('content')



    <main class="banner-body bg--section">
        <div class="container">
            <div class="banner-section overflow-hidden">
                @include($activeTemplate.'partials.left_category_menu')
                @include($activeTemplate.'sections.banner_sliders')
                @include($activeTemplate.'sections.banner_promotional')
            </div>
        </div>
        @include($activeTemplate.'sections.banner_categories')
    </main>

   <!-- @include($activeTemplate.'sections.invite')-->
    @if ($offers->count() > 0)
     @include($activeTemplate.'sections.offers')
    @endif
    @if ($featuredProducts->count() > 0)
      @include($activeTemplate.'sections.featured_products')
    @endif
     @if($catego->count() > 0)
      @include($activeTemplate.'sections.category')
    @endif

    @if($latestProducts->count() > 0)
      @include($activeTemplate.'sections.latest_products')
    @endif
  <!--  @if ($featuredSeller->count() > 0)
     @include($activeTemplate.'sections.featured_seller')
    @endif
    @include($activeTemplate.'sections.invite_seller')
    @if ($topBrands->count() > 0)
      @include($activeTemplate.'sections.brands')
    @endif-->
    @if ($topSellingProducts->count() > 0)
      @include($activeTemplate.'sections.trending_products')
    @endif
    <!--@include($activeTemplate.'sections.subscribe')-->
     
 
 
 
 
 <div class="modal fade" id="global-modal" role="dialog">
  <div class="modal-dialog modal-lg">
    <!--Modal Content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" style="margin-top: -16px; font-size: 28px;" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>

      </div>
      <div class="modal-body" style="padding: 0;">
        <img class="img-full img-responsive"  data-dismiss="modal" aria-label="Close"  src="{{url('/')}}/assets/images/popup3.jpeg" style='height: 100%; width: 100%; object-fit: contain'>
        <br>
        @php
            $subscribe = getContent('subscribe.content', true);
        @endphp
 
<section class="newsletter-section bg--base padding-top padding-bottom">
    <div class="container">
        <div class="section-header mb-4">
            <h6 class="" style="color:white">@lang(@$subscribe->data_values->text)</h6>
        </div>
        <div class="subscribe-form ml-auto mr-auto">
            <input type="text" placeholder="Enter Your Email Address" class="form-control" name="email">
            <button type="button" class="subscribe-btn"  data-dismiss="modal">@lang('Subscribe')</button>
              
        </div>
    </div>
</section>  
        <p></p>
      </div>
    </div>
  </div>
</div> 

@endsection

@push('script')
<script>
    $(document).ready(function() {
  $('#global-modal').modal('show');
});
</script>
    <script>
        'use strict';
        (function($){
            $(document).on('click','.subscribe-btn' , function(){
                var email = $('input[name="email"]').val();
                $.ajax({
                    headers: {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
                    url:"{{ route('subscribe') }}",
                    method:"POST",
                    data:{email:email},
                    success:function(response)
                    {
                        if(response.success) {
                            notify('success', response.success);
                        }else{
                            notify('error', response);
                        }
                    }
                });
            });
        })(jQuery)
    </script>
@endpush
