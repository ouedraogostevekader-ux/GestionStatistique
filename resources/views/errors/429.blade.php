@extends('errors::layout')

@section('title', "Vous êtes trop rapide")
@section('code', '429')
@section('message')
Oups ! Vous êtes trop rapide.
@endsection
@section('message_body')
Désolé, vous envoyez trop de requêtes.<br>
Patientez 02 minutes avant de réessayer !
<br><br>
Nous vous remercions pour votre compréhension et pour votre patience
@endsection
