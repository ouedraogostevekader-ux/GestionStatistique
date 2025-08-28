@extends('errors::layout')

@section('title', __('Service Unavailable'))
@section('code', '503')
@section('message')
Oups ! Une erreur est survenue.
@endsection
@section('message_body')
Désolé, quelque chose s'est mal passé de notre côté. Notre équipe technique a été notifiée.<br><br>

En attendant, vous pouvez essayer les étapes suivantes pour résoudre ce problème :<br>
<ol>
    <li>Actualisez la page pour voir si le problème persiste.</li>
    <li>Si le problème persiste toujours, veuillez contacter notre équipe d'assistance à st.trhimo@infrastructures.gov.bf !
        <br>
        Notre équipe de support sera ravie de vous aider!
    </li>
</ol>

<br><br>
Nous vous remercions de votre compréhension et de votre patience
@endsection
