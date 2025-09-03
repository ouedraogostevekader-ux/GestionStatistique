<nav class="app-header navbar navbar-expand custom-navbar">
    <div class="container-fluid d-flex justify-content-between align-items-center">

        <!-- Menu gauche -->
        <ul class="navbar-nav d-flex align-items-center">
            <li class="nav-item me-3">
                <a class="nav-link text-white" data-lte-toggle="sidebar" href="#" role="button">
                    <i class="bi bi-list fs-4"></i>
                </a>
            </li>
            <li class="nav-item d-none d-md-block">
                <a href="{{ route('dashboard') }}" class="nav-link fw-bold text-white">Accueil</a>
            </li>
        </ul>

        <!-- Menu droite -->
        <ul class="navbar-nav ms-auto d-flex align-items-center">
            <!-- Plein écran -->
            <li class="nav-item me-3">
                <a class="nav-link text-white" href="#" data-lte-toggle="fullscreen">
                    <i data-lte-icon="maximize" class="bi bi-arrows-fullscreen fs-5"></i>
                    <i data-lte-icon="minimize" class="bi bi-fullscreen-exit fs-5"></i>
                </a>
            </li>

            <!-- Profil utilisateur -->
            <li class="nav-item dropdown user-menu">
                <a href="#" class="nav-link dropdown-toggle d-flex align-items-center" data-bs-toggle="dropdown">
                    @php $url = Auth::user()->photo ? 'storage/'.Auth::user()->photo : 'dist/assets/img/user2-160x160.jpg'; @endphp
                    <img src="{{ asset($url) }}" class="user-image rounded-circle shadow me-2" alt="User Image"
                        width="35" height="35" />
                    <span class="fw-bold text-white">{{ Auth::user()->nom }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                    <li class="dropdown-header text-center">
                        <strong>{{ Auth::user()->nom }}</strong><br>
                        <small>{{ Auth::user()->email }}</small>
                    </li>
                    <hr class="dropdown-divider">
                    <li>
                        <a class="dropdown-item d-flex align-items-center"
                            href="{{ route('users.profile', Auth::user()->id) }}">
                            <i class="bi bi-person me-2"></i>
                            <span>Mon Profil</span>
                        </a>
                    </li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger d-flex align-items-center">
                                <i class="bi bi-box-arrow-right me-2"></i> Se déconnecter
                            </button>
                        </form>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</nav>
