<h1>Liste des annonces</h1>
<a href="{{ route('annonces.create') }}">Nouvelle annonce</a>

@if(session('success'))
    <p style="color: green;">{{ session('success') }}</p>
@endif

<ul>
    @foreach($annonces as $annonce)
        <li>
            {{ $annonce->titre }} ({{ $annonce->statut }})
            <a href="{{ route('annonces.edit', $annonce) }}">Modifier</a>
            <form action="{{ route('annonces.destroy', $annonce) }}" method="POST" style="display:inline;">
                @csrf @method('DELETE')
                <button type="submit">Supprimer</button>
            </form>
        </li>
    @endforeach
</ul>
