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
                        {{-- @can('managePermission') --}}
                        <li class="nav-item">
                            <a href="{{ route('permissions.index') }}"
                                class="nav-link {{ Request::is('permissions*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-people-fill"></i>
                                <p>Permissions</p>
                            </a>
                        </li>
                        {{-- @endcan --}}
                        {{-- @can('manageBrigadier')
                            <li class="nav-item">
                                <a href="{{ route('recrutement_vc.ouvert') }}"
                                    class="nav-link {{ Request::is('brigadier*') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-house-gear"></i>
                                    <p>Candidature</p>
                                </a>
                            </li>
                        @endcan --}}
                        @can('manageUser')
                            <li class="nav-item">
                                <a href="{{ route('users.index') }}"
                                    class="nav-link {{ Request::is('users*') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-people-fill"></i>
                                    <p>Utilisateurs</p>
                                </a>
                            </li>
                        @endcan
                        {{-- @can('manageRole') --}}
                        <li class="nav-item">
                            <a href="{{ route('roles.index') }}"
                                class="nav-link {{ Request::is('roles*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-person-check-fill"></i>
                                <p>Roles</p>
                            </a>
                        </li>
                        {{-- @endcan --}}

                        {{-- @can('managePermission') --}}
                        <li class="nav-item">
                            <a href="{{ route('permissions.index') }}"
                                class="nav-link {{ Request::is('permissions*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-shield-fill-check"></i>
                                <p>Permissions</p>
                            </a>
                        </li>
                        {{-- @endcan --}}
                        {{-- @can('manageLog') --}}
                        <li class="nav-item">
                            <a href="{{ route('logs.index') }}"
                                class="nav-link {{ Request::is('logs*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-file-text-fill"></i>
                                <p>Logs</p>
                            </a>
                        </li>
                        {{-- @endcan  --}}
                    </ul>
                </li>
            @endcan

            {{-- @can('manageRecrutementCommune')
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon bi bi-house-fill"></i>
                        <p>
                            Commune
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">
                        @can('recrutementCommune-list')
                            <li class="nav-item">
                                <a href="{{ route('recrutements.mesures-sociales') }}"
                                    class="nav-link {{ Request::is('recrutements.mesures-sociales*') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-clipboard-data-fill"></i>
                                    <p>Mesures sociales</p>
                                </a>
                            </li>
                        @endcan
                        @can('recrutementCommune-create')
                            <li class="nav-item">
                                <a href="{{ route('recrutements.commune') }}"
                                    class="nav-link {{ Request::is('recrutements.commune*') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-people-fill"></i>
                                    <p>Recrutement</p>
                                </a>
                            </li>
                        @endcan
                        @can('manageVeillecitoyenne')
                            <li class="nav-item">
                                <a href="{{ route('veillecitoyennes.index') }}"
                                    class="nav-link {{ Request::is('veillecitoyennes*') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-people-fill"></i>
                                    <p>Veille citoyenne</p>
                                </a>
                            </li>
                        @endcan
                        @can('managePdi')
                            <li class="nav-item">
                                <a href="{{ route('pdis.index') }}"
                                    class="nav-link {{ Request::is('pdis*') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-people-fill"></i>
                                    <p>PDI</p>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan --}}
            @can('manageParametre')
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon bi bi-gear-fill"></i>
                        <p>
                            Parametre
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>

                    {{-- <ul class="nav nav-treeview">
                        @can('manageCommune')
                            <li class="nav-item">
                                <a href="{{ route('communes.index') }}"
                                    class="nav-link {{ Request::is('communes*') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-house-gear"></i>
                                    <p>Communes</p>
                                </a>
                            </li>
                        @endcan
                        @can('manageProvince')
                            <li class="nav-item">
                                <a href="{{ route('provinces.index') }}"
                                    class="nav-link {{ Request::is('provinces*') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-house-gear"></i>
                                    <p>Provinces</p>
                                </a>
                            </li>
                        @endcan
                        @can('manageRegion')
                            <li class="nav-item">
                                <a href="{{ route('regions.index') }}"
                                    class="nav-link {{ Request::is('regions*') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-house-gear"></i>
                                    <p>Regions</p>
                                </a>
                            </li>
                        @endcan

                        @can('manageSituationSociale')
                            <li class="nav-item">
                                <a href="{{ route('situation-sociales.index') }}"
                                    class="nav-link {{ Request::is('situation-sociales*') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-house-gear"></i>
                                    <p>Situations sociales</p>
                                </a>
                            </li>
                        @endcan

                    </ul> --}}
                </li>
            @endcan
            <li class="nav-item">
                <a href="{{ route('activites.index') }}"
                    class="nav-link {{ Request::is('activites*') ? 'active' : '' }}">
                    <i class="nav-icon bi bi-clipboard-check-fill"></i>
                    <p>Activités</p>
                </a>
            </li>
        </ul>
        <!--end::Sidebar Menu-->
    </nav>
</div>
