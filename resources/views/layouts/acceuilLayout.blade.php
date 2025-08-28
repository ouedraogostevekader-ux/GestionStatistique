<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>GestStat | MID</title>
    <!-- Favicon-->
    <link href="{{ asset('img/armoiries.jpeg') }}" rel="icon">
    <!-- Bootstrap Icons-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google fonts-->
    <link href="https://fonts.googleapis.com/css?family=Merriweather+Sans:400,700" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css?family=Merriweather:400,300,300italic,400italic,700,700italic"
        rel="stylesheet" type="text/css" />
    <!-- SimpleLightbox plugin CSS-->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/SimpleLightbox/2.1.0/simpleLightbox.min.css" rel="stylesheet" />
    <!-- Core theme CSS (includes Bootstrap)-->
    <link href="{{ asset('style_acceuil/css/style.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">

    {{-- <!-- Phone number CSS-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.1/build/css/intlTelInput.css"> --}}
</head>

<body id="page-top">
    <header>
        <!-- Navigation-->
        <nav class="navbar navbar-expand-lg navbar-light fixed-top py-2" id="mainNav">
            <div class="container px-1 px-lg-1">
                <a class="navbar-brand" href="/"><img src="{{ asset('img/armoiries.jpeg') }}" id="logoStyle"
                        alt="Logo" class="me-4"></a>
                <a class="navbar-brand" href="/">
                    <p class="p1 h3">DGESS </p>
                </a>
                <button class="navbar-toggler navbar-toggler-right" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false"
                    aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
                <div class="collapse navbar-collapse" id="navbarResponsive">
                    <ul class="navbar-nav ms-auto my-2 my-lg-0">
                        <li class="nav-item"><a class="nav-link" href="#">A propos</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Connexion</a>
                        </li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">Inscription</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    <!-- Masthead-->
    <header class="masthead">
        <div class="container px-4 px-lg-5 h-100">
            <div class="row gx-4 gx-lg-5 h-100 align-items-center justify-content-center text-center">
                <div class="col-lg-8 align-self-end">
                    <h1 class="text-white font-weight-bold">Bienvenue sur la plateforme de gestion des statistiques
                        .</h1>
                    <hr class="divider" />
                </div>
                <div class="col-lg-8 align-self-baseline">
                    <p class="text-white-75 mb-5">La DGESS gère les statistiques
                        du Ministère des Infrastructures et du Désenclavement.</p>
                    <a class="btn btn-warning btn-xl voirplus" href="{{ route('login') }}">Connexion</a>
                    <a class="btn btn-success btn-xl" href="{{ route('register') }}">Inscription</a>

                </div>
            </div>
        </div>
    </header>
    {{-- <main class="mt-5 y-5">
        @yield('content')
    </main> --}}

    <!-- Footer-->
    <footer class="bg py-3">
        <div class="container px-4 px-lg-5">
            <div class="text-center text-white">&copy; {{ date('Y') }} Copyright <span>DSI-MID</span> <a
                    href="{{ route('login') }}">Admin</a></div>
        </div>
    </footer>

    {{-- Bootstrap js --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SimpleLightbox plugin JS-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/SimpleLightbox/2.1.0/simpleLightbox.min.js"></script>


    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

    <!-- Extensions (si besoin) -->
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

    {{-- <!--Phone number script-->
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.1/build/js/intlTelInput.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.1/build/js/utils.js"></script> --}}

    <!-- Core theme JS-->
    <script src="{{ asset('style_acceuil/js/scripts.js') }}"></script>
</body>

</html>
