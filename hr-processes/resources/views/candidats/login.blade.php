@extends('layouts.rh')  // Ou layouts.rh si préféré

@section('content')
<h1>Connexion Candidat</h1>
<form method="POST" action="{{ route('candidat.login') }}">
    @csrf
    <label>Email</label>
    <input type="email" name="email" required>
    <label>Password</label>
    <input type="password" name="password" required>
    <button type="submit">Se connecter</button>
</form>
@endsection