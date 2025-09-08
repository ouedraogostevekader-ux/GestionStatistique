<div class="sidebar-wrapper">
    <nav class="mt-2">
        <!--begin::Sidebar Menu-->
        <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">

            @can('manageAdministration')
                <a href="{{ route('dashboard') }}" class="nav-link {{ Request::is('admin/dashboard*') ? 'active' : '' }}">
                    <i class="nav-icon bi bi-speedometer2"></i>
                    <p>
                        Tableau de bord
                    </p>
                </a>
                <li
                    class="nav-item {{ Route::is('logs.index*') || Route::is('users*') || Route::is('roles*') || Route::is('permissions*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link">
                        <i class="nav-icon bi bi-person-fill-gear"></i>
                        <p>
                            Administration
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
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

            @can('manageParametre')
                <li class="nav-item {{ Route::is('structures*') || Route::is('imports*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ Route::is('structures*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-gear-fill"></i>
                        <p>
                            Parametre
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        {{-- @can('manageObjectifStrategique') --}}
                        <li class="nav-item">
                            <a href="{{ route('structures.index') }}"
                                class="nav-link {{ Route::is('structures*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-building"></i>
                                <p>Structures</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('imports.index') }}"
                                class="nav-link {{ Route::is('imports*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-folder"></i>
                                <p>Imports</p>
                            </a>
                        </li>
                        {{-- @endcan --}}
                    </ul>
                </li>
            @endcan

            @can('manageDeProgramme')
                <li
                    class="nav-item {{ Request::is('programmebudgetaire*') || Request::is('suivi*') || Request::is('objectifStrategiques*') || Request::is('programme-activites*') || Request::is('activites*') || Request::is('indicateurs-programmes*') || Request::is('objectifs-specifiques*') || Request::is('actions*') ? 'menu-open' : '' }}">
                    <a href="#"
                        class="nav-link {{ Request::is('programmebudgetaire*') || Request::is('objectifStrategiques*') || Request::is('activites*') || Request::is('programme-activites*') || Request::is('indicateurs-programmes*') || Request::is('objectifs-specifiques*') || Request::is('actions*') ? 'active' : '' }}">
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
                                    class="nav-link {{ Request::is('programmebudgetaire*') ? 'active' : '' }}"
                                    title="Programme budgetaire">
                                    <i class="nav-icon bi bi-journal-text"></i>
                                    <p>Programme budgetaire</p>
                                </a>
                            @endcan
                        </li>
                        <li class="nav-item">
                            @can('manageObjectif_Strategique')
                                <a href="{{ route('objectifStrategiques.index') }}"
                                    class="nav-link {{ Request::is('objectifStrategiques*') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-bullseye"></i>
                                    <p>Objectifs Strategiques</p>
                                </a>
                            @endcan
                        </li>
                        <li class="nav-item">
                            @can('manageAction')
                                <a href="{{ route('actions.index') }}"
                                    class="nav-link {{ Request::is('actions*') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-lightning-charge-fill"></i>
                                    <p>Actions</p>
                                </a>
                            @endcan
                        </li>
                        <li class="nav-item">
                            @can('manageObjectif_Specifique')
                                <a href="{{ route('objectifs-specifiques.index') }}"
                                    class="nav-link {{ Request::is('objectifs-specifiques*') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-bullseye"></i>
                                    <p>Objectifs spécifiques</p>
                                </a>
                            @endcan
                        </li>

                        <li class="nav-item">
                            @can('manageIndicateur_Programme')
                                <a href="{{ route('indicateurs-programmes.index') }}"
                                    class="nav-link {{ Request::is('indicateurs-programmes*') ? 'active' : '' }}"
                                    title="Indicateurs programme">
                                    <i class="nav-icon bi bi-graph-up"></i>
                                    <p>Indicateurs programme</p>
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

                        <li class="nav-item">
                            @can('manageProgramme_Activite')
                                <a href="{{ route('programme-activites.index') }}"
                                    class="nav-link {{ Request::is('programme-activites*') ? 'active' : '' }}"
                                    title="Programme d'activité">
                                    <i class="nav-icon bi bi-journal"></i>
                                    <p>Programmes d'activités</p>
                                </a>
                            @endcan
                        </li>
                    </ul>
                </li>
            @endcan

            {{-- @can('manageTableau') --}}
            <li class="nav-item {{ Route::is('tableaus*') || Route::is('tableaus*') ? 'menu-open' : '' }}">
                <a href="#"
                    class="nav-link {{ Route::is('tableaus*') || Route::is('tableaus*') ? 'active' : '' }}">
                    <i class="nav-icon bi bi-journal-text"></i>
                    <p>
                        Tableaux
                        <i class="nav-arrow bi bi-chevron-right"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    {{-- @can('manageObjectifStrategique') --}}
                    <li class="nav-item">
                        <a href="{{ route('colonnes.index') }}"
                            class="nav-link {{ Route::is('colonnes*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-journal-text"></i>
                            <p>Colonnes</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('lignes.index') }}"
                            class="nav-link {{ Request::is('lignes*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-eye-fill"></i>
                            <p>lignes</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('tableaus.index') }}"
                            class="nav-link {{ Request::is('tableaus*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-eye-fill"></i>
                            <p>Elabore Tab</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('tableaus.index') }}"
                            class="nav-link {{ Request::is('tableaus*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-eye-fill"></i>
                            <p>Renseigne Tab</p>
                        </a>
                    </li>

                </ul>
            </li>
            {{-- @endcan --}}

            @can('manageRapport')
                <li class="nav-item {{ Route::is('rapportActivites*') || Route::is('suivis*') ? 'menu-open' : '' }}">
                    <a href="#"
                        class="nav-link {{ Route::is('rapportActivites*') || Route::is('suivis*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-journal-text"></i>
                        <p>
                            Rapports
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        {{-- @can('manageObjectifStrategique') --}}
                        <li class="nav-item">
                            <a href="{{ route('rapportActivites.index') }}"
                                class="nav-link {{ Route::is('rapportActivites*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-journal-text"></i>
                                <p>Rapport d'activités</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('suivis.index') }}"
                                class="nav-link {{ Request::is('suivis*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-eye-fill"></i>
                                <p>Suivis</p>
                            </a>
                        </li>
                    </ul>
                </li>
            @endcan

            <li class="nav-item">
                <a href="{{ route('statistiques.index') }}"
                    class="nav-link {{ Request::is('*statistique*') ? 'active' : '' }}">
                    <i class="nav-icon bi bi-bar-chart-fill"></i>
                    <p>Statistiques</p>
                </a>
            </li>

        </ul>
        <!--end::Sidebar Menu-->
    </nav>
</div>
