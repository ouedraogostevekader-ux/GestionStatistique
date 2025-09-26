<!-- Name Field -->
<div class="col-sm-6">
    <label class="form-label fw-bold">Nom & prénom</label> :
    {{ $user->name }}
</div>

<!-- Email Field -->
<div class="col-sm-6">
    <label class="form-label fw-bold">Email</label> :
    {{ $user->email }}
</div>
<!-- Email Field -->
<div class="col-sm-6">
    <label class="form-label fw-bold">Téléphone</label> :
    {{ $user->telephone }}
</div>

<div class="col-sm-6">
    <label class="form-label fw-bold">Matricule</label> :
    {{ $user->matricule }}
</div>

<div class="col-sm-6">
    <label class="form-label fw-bold">Fonction</label> :
    {{ $user->fonction }}
</div>

<div class="col-sm-6">
    <label class="form-label fw-bold">Structure</label> :
    {{ $user->structure->sigle }} = {{ $user->structure->nom }}
</div>

<div class="form-group col-sm-12">
    <label class="form-label fw-bold">Role</label> : |
    @foreach ($user->roles as $role)
        {{ $role->name }} |
    @endforeach
</div>

<div class="col-sm-6">
    <label class="form-label fw-bold">Photo de profile</label> :
    <div class="avatar-preview">
        @if ($user->photo != null)
            <img src="{{ asset('storage/' . $user->photo) }}" alt="PHOTO PROFIL">
        @else
            Pas de photo
        @endif
    </div>
</div>
<div class="col-sm-6">
    <div>
        <label for="form-label fw-bold">Status</label>
        : {!! $user->is_active
            ? '<span class="badge bg-success">Actif</span>'
            : '<span
                                                                                                                                                    class="badge bg-danger">Désactivé</span>' !!}
        @if ($user->is_active)
            <!-- Bouton désactiver -->
            <form action="{{ route('users.deactivate', $user->id) }}" method="POST" style="display:inline;">
                @csrf
                @method('PATCH')
                <button class="btn btn-outline-danger desactive-btn" title="désactiver ce compte"
                    data-url="{{ route('users.deactivate', $user->id) }}"
                    data-message="L'utilisateur <strong>{{ $user->nom }} {{ $user->prenom }}</strong> sera désactivé. Continuer ?">
                    <i class="bi bi-dash-circle"></i></i>Désactiver
                </button>
            </form>
        @else
            <!-- Bouton activer -->
            <form action="{{ route('users.activate', $user->id) }}" method="POST" style="display:inline;">
                @csrf
                @method('PATCH')
                <button class="btn btn-outline-success active-btn" title="activer ce compte"
                    data-url="{{ route('users.activate', $user->id) }}"
                    data-message="L'utilisateur <strong>{{ $user->nom }} {{ $user->prenom }}</strong> sera réactivé. Continuer ?">
                    <i class="bi bi-check-circle-fill"></i>Réactiver
                </button>
            </form>
        @endif
    </div>
</div>
