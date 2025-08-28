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
        <link href="{{ asset('errors/minimun.css') }}" rel="stylesheet" />
    </head>
    <body class="antialiased">
        <div class="relative flex items-top justify-center min-h-screen bg-gray-100 dark:bg-gray-900 sm:items-center sm:pt-0">
            <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
                <div class="flex items-center pt-8 sm:justify-start sm:pt-0">
                    <div class="px-4 text-lg text-gray-500 border-r border-gray-400 tracking-wider">
                        @yield('code')
                    </div>

                    <div class="ml-4 text-lg text-gray-500 uppercase tracking-wider">
                        @yield('message')
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
