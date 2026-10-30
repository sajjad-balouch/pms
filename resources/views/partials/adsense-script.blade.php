{{-- AdSense sirf asal domain par load hoga: APP_ENV par depend nahi, local par nahi chalega --}}
@if(config('services.adsense.client') && ! in_array(request()->getHost(), ['localhost', '127.0.0.1', '10.0.2.2'], true))
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client={{ config('services.adsense.client') }}" crossorigin="anonymous"></script>
@endif
