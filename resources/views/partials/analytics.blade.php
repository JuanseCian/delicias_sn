@if (filled(config('delicias.ga4_id')))
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ urlencode(config('delicias.ga4_id')) }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', @json(config('delicias.ga4_id')));
    </script>
@endif
