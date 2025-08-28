<nav class="app-header navbar navbar-expand bg-body">
    <!--begin::Container-->
    <div class="container-fluid">
        <!--begin::Start Navbar Links-->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                    <i class="bi bi-list"></i>
                </a>
            </li>
            <li class="nav-item d-none d-md-block"><a href="#" class="nav-link">Accueil</a></li>
        </ul>
        <!--end::Start Navbar Links-->
        <!--begin::End Navbar Links-->
        <ul class="navbar-nav ms-auto">
            <li class="nav-item dropdown">
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                    <div class="dropdown-divider"></div>
                    <div class="dropdown-divider"></div>
                    <div class="dropdown-divider"></div>
                </div>
            </li>
            <!--begin::Fullscreen Toggle-->
            <li class="nav-item">
                <a class="nav-link" href="#" data-lte-toggle="fullscreen">
                    <i data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i>
                    <i data-lte-icon="minimize" class="bi bi-fullscreen-exit"></i>
                </a>
            </li>
            <!--end::Fullscreen Toggle-->
            <!--begin::User Menu Dropdown-->
            <li class="nav-item dropdown user-menu">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                    @php $url = Auth::user()->photo ? 'storage/'.Auth::user()->photo : 'dist/assets/img/user2-160x160.jpg' ; @endphp
                    <img src="{{ asset($url) }}" class="user-image rounded-circle shadow"
                        alt="User Image" />
                    <span class="d-none d-md-inline">{{ Auth::user()->nom }}</span>
                </a>
                <nav class="header-nav ms-auto">
            <li class="nav-item dropdown pe-3">

                <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
                    <span class="d-none d-md-block dropdown-toggle ps-2 text-white">{{ Auth::user()->nom }}</span>
                </a><!-- End Profile Iamge Icon -->

                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">

                    <li>
                        <hr class="dropdown-divider">
                    </li>

                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="#">
                            <i class="bi bi-envelope"></i>
                            <span class="badge border-success border-1 text-success">
                                <h6>{{ Auth::user()->email }}</h6>
                            </span>
                        </a>
                    </li>
                    <hr class="dropdown-divider">
                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="{{ route('users.profile', Auth::user()->id) }}">
                            <i class="bi bi-person"></i>
                            <span>Mon Profil</span>
                        </a>
                    </li>
                    <li>
                        <br>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link href="#" id="logout-link" class="text-warning">
                                <i class="bi bi-box-arrow-right"></i> {{ __('Se déconnecter') }}
                            </x-dropdown-link>
                        </form>
                    </li>

                </ul><!-- End Profile Dropdown Items -->
            </li><!-- End Profile Nav -->

</nav>
<!-- End Icons Navigation -->
</li>
<!--end::User Menu Dropdown-->
<!--end::End Navbar Links-->
</div>
<!--end::Container-->
</nav>
