@php
	$captcha = loadCustomCaptcha($height = 60, $width = '100%');
@endphp
@if($captcha)
    <div class="contact-group">
        <label for="captcha_code">@lang('Captha')</label>
        <div class="multi-group">
            @php echo $captcha @endphp
        </div>
    </div>
    <div class="contact-group">
        <label for="captcha_code">@lang('Verify Captha')</label>
        <div class="multi-group">
            <input type="text" name="captcha" id="captcha_code" placeholder="@lang('Enter Code')" class="form-control w-100">
        </div>
    </div>
@endif
