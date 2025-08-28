<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Mesures sociales du Gouvernement</title>
        <!--begin::Primary Meta Tags-->
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="title" content="Mesures sociales du Gouvernement" />
        <meta name="author" content="DSI MID" />
        <meta name="description"
              content="Mesures sociales du Gouvernement est sous forme d'une redistribution de bien au service public plus précisement les personnes vunérables" />
        <meta name="keywords" content="mesures sociales, HIMO" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link href="{{ asset('dist/assets/img/ARMOIRIES-BF.jpg') }}" rel="icon">

        <title>@yield('title')</title>

        <!-- Styles -->
        <link href="{{ asset('errors/layout.css') }}" rel="stylesheet" />
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            <div class="content">
                <div class="title">
                    @yield('message')
                </div>
            </div>
        </div>
    </body>
</html>
