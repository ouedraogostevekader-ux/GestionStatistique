@component('mail::message')
    # Bonjour {{ $user->nom }} {{ $user->prenom }}

    Voici vos identifiants de connexion :

    - **Email: {{ $user->email }}

    Veuillez cliquer sur le bouton ci-dessous pour confirmer et activer votre compte.
    @component('mail::button', ['url' => $url])
        Confirmer votre compte
    @endcomponent


    Merci,
    {{ config('app.name') }}
@endcomponent
