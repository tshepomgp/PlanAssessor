<!DOCTYPE html>
<html>
<head>
    <script>(function (w, d, s, l, i) {
            w[l] = w[l] || [];
            w[l].push({
                'gtm.start':
                    new Date().getTime(), event: 'gtm.js'
            });
            var f = d.getElementsByTagName(s)[0],
                j = d.createElement(s), dl = l != 'dataLayer' ? '&l=' + l : '';
            j.async = true;
            j.src =
                'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
            f.parentNode.insertBefore(j, f);
        })(window, document, 'script', 'dataLayer', 'GTM-WSQN3GX');</script>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="apple-touch-icon" sizes="57x57" href="/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="/apple-icon-120x120.png">
    <meta name="author" content="Drip Data Software @ https://dripdata.net">
    <link rel="apple-touch-icon" sizes="144x144" href="/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href='https://fonts.googleapis.com/css?family=Roboto:100,300,400,700,500&display=swap' rel='stylesheet'
          type='text/css'>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@500&display=swap" rel="stylesheet">
    <link id="style-css" rel="stylesheet" type="text/css" href="{{ url('/css/main.min.css.gz') }}">
    <title>SACAP</title>
</head>
<body>
<noscript>
    <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-WSQN3GX"
            height="0" width="0" style="display:none;visibility:hidden"></iframe>
</noscript>
<div id="fb-root"></div>
<script async defer crossorigin="anonymous"
        src="https://connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v9.0&appId=291116029030683&autoLogAppEvents=1"
        nonce="Z6MO1KGx"></script>
@include('layouts.header')
<div class="@if(empty($fullPage) === true ) container @endif">
    
    @yield('content')
</div>
@include('global.footer')

<div id="chatIcon">
    <button id="openChatRegistrationButton"
            data-content="@if(Session::has('channel_id')) {{ Session::get('channel_id') }}@endif"
            class="btn btn-primary bottom-right"><i class="fa fa-comment"></i></button>
</div>
@widget('footer')
<script src="{{ url('/js/main.min.js.gz') }}"></script>
@yield('extra-scripts')
<div id="toTop" class="hidden-sm">
    <i class="fa fa-angle-up"></i>
</div>
</body>
