<div class="sidebar-wrapper">
    <nav class="mt-2">
        <!--begin::Sidebar Menu-->
        <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">

            @can('manageAdministration')
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon bi bi-person-fill-gear"></i>
                        <p>
                            Administration
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        @can('manageLog')
                            <li class="nav-item">
                                <a href="{{ route('logs.index') }}" class="nav-link {{ Request::is('logs*') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-clipboard-data-fill"></i>
                                    <p>Logs</p>
                                </a>
                            </li>
                        @endcan
                        @can('manageUser')
                            <li class="nav-item">
                                <a href="{{ route('users.index') }}"
                                    class="nav-link {{ Request::is('users*') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-people-fill"></i>
                                    <p>Utilisateurs</p>
                                </a>
                            </li>
                        @endcan
                        @can('manageRole')
                            <li class="nav-item">
                                <a href="{{ route('roles.index') }}"
                                    class="nav-link {{ Request::is('roles*') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-person-check-fill"></i>
                                    <p>Roles</p>
                                </a>
                            </li>
                        @endcan

                        {{-- @can('managePermission') --}}
                        <li class="nav-item">
                            <a href="{{ route('permissions.index') }}"
                                class="nav-link {{ Request::is('permissions*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-shield-fill-check"></i>
                                <p>Permissions</p>
                            </a>
                        </li>
                        {{-- @endcan --}}
                    </ul>
                </li>
            @endcan

            @can('manageParametre')
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon bi bi-gear-fill"></i>
                        <p>
                            Parametre
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        {{-- @can('manageObjectifStrategique') --}}

                        {{-- @endcan --}}
                    </ul>
                </li>
            @endcan

            @can('manageDeProgramme')
                <li
                    class="nav-item {{ Request::is('programmebudgetaire*') || Request::is('objectifStrategiques*') || Request::is('activites*') ? 'menu-open' : '' }}">
                    <a href="#"
                        class="nav-link {{ Request::is('programmebudgetaire*') || Request::is('objectifStrategiques*') || Request::is('activites*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-folder-fill"></i>
                        <p>
                            Programmation
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            @can('manageProgramme_Budgetaire')
                                <a href="{{ route('programmebudgetaire.index') }}"
                                    class="nav-link {{ Request::is('programmebudgetaire*') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-journal-text"></i>
                                    <p>Programme budgetaire</p>
                                </a>
                            @endcan
                        </li>
                        <li class="nav-item">
                            @can('manageObjectif_Strategique')
                                <a href="{{ route('objectifStrategiques.index') }}"
                                    class="nav-link {{ Request::is('objectifStrategiques*') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-house-gear"></i>
                                    <p>Objectifs Strategiques</p>
                                </a>
                            @endcan
                        </li>
                        <li class="nav-item">
                            @can('manageAction')
                                <a href="{{ route('actions.index') }}"
                                    class="nav-link {{ Request::is('actions*') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-house-gear"></i>
                                    <p>Actions</p>
                                </a>
                            @endcan
                        </li>
                        <li class="nav-item">
                            @can('manageObjectif_Specifique')
                                <a href="{{ route('objectifs-specifiques.index') }}"
                                    class="nav-link {{ Request::is('objectifs-specifiques*') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-house-gear"></i>
                                    <p>Objectifs spécifiques</p>
                                </a>
                            @endcan
                        </li>
                        <li class="nav-item">
                            @can('manageActivite')
                                <a href="{{ route('activites.index') }}"
                                    class="nav-link {{ Request::is('activites*') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-clipboard-check-fill"></i>
                                    <p>Activités</p>
                                </a>
                            @endcan
                        </li>
                    </ul>
                </li>
            @endcan
        </ul>
        <!--end::Sidebar Menu-->
    </nav>
</div>
