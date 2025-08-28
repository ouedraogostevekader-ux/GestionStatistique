@extends('errors::layout')

@section('title', "Accès Refusé")
@section('code', '401')
@section('message')
Accès Refusé
@endsection
@section('message_body')
Désolé, vous n'avez pas l'autorisation d'accéder à cette page.<br><br>

Cela peut être dû à l'une des raisons suivantes :<br>
<ul>
    <li>Vous n'êtes pas connecté avec un compte autorisé.</li>
    <li>Votre compte ne dispose pas des permissions nécessaires pour accéder à cette fonctionnalité.</li>
    <li>L'accès à cette page est limité à un groupe spécifique d'utilisateurs.</li>
</ul>

Si vous pensez qu'il s'agit d'une erreur ou si vous avez besoin d'accéder à cette page pour une raison légitime,<br>
veuillez contacter l'administrateur du système ou l'équipe de support à l'adresse st.trhimo@infrastructures.gov.bf.<br>
<br><br>
Merci pour votre compréhension.
@endsection
