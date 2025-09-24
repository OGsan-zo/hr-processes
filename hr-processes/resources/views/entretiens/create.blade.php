@extends('layouts.rh')

@section('content')
<div style="padding: 20px;">
    <h1>Planifier un entretien</h1>

    <div style="margin-bottom: 20px;">
        <a href="{{ route('entretiens.calendar') }}" style="background: #6c757d; color: white; padding: 8px 12px; text-decoration: none; border-radius: 4px;">
            ← Retour au calendrier
        </a>
    </div>

    @if ($errors->any())
        <div style="color: red; margin-bottom: 20px;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('entretiens.store') }}" method="POST" style="background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); max-width: 600px;">
        @csrf
        
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: bold;">Candidat :</label>
            <select name="candidat_id" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                <option value="">-- Sélectionner un candidat --</option>
                @foreach($candidats as $candidat)
                    <option value="{{ $candidat->id }}" {{ old('candidat_id') == $candidat->id ? 'selected' : '' }}>
                        {{ $candidat->nom }} {{ $candidat->prenom }}
                        @if($candidat->competences)
                            - {{ $candidat->competences }}
                        @endif
                    </option>
                @endforeach
            </select>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: bold;">Poste :</label>
            <select name="annonce_id" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                <option value="">-- Sélectionner un poste --</option>
                @foreach($annonces as $annonce)
                    <option value="{{ $annonce->id }}" {{ old('annonce_id') == $annonce->id ? 'selected' : '' }}>
                        {{ $annonce->titre }}
                        @if(isset($annonce->localisation))
                            - {{ $annonce->localisation }}
                        @endif
                    </option>
                @endforeach
            </select>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: bold;">Date et heure :</label>
            <input 
                type="datetime-local" 
                name="date_entretien" 
                value="{{ old('date_entretien', $selectedDate ?? '') }}" 
                required 
                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;"
                min="{{ now()->format('Y-m-d\TH:i') }}"
            >
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: bold;">Durée (minutes) :</label>
            <select name="duree" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                <option value="30" {{ old('duree') == '30' ? 'selected' : '' }}>30 minutes</option>
                <option value="45" {{ old('duree') == '45' ? 'selected' : '' }}>45 minutes</option>
                <option value="60" {{ old('duree', '60') == '60' ? 'selected' : '' }}>1 heure</option>
                <option value="90" {{ old('duree') == '90' ? 'selected' : '' }}>1h30</option>
                <option value="120" {{ old('duree') == '120' ? 'selected' : '' }}>2 heures</option>
            </select>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: bold;">Statut :</label>
            <select name="statut" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                <option value="planifié" {{ old('statut', 'planifié') == 'planifié' ? 'selected' : '' }}>Planifié</option>
                <option value="confirmé" {{ old('statut') == 'confirmé' ? 'selected' : '' }}>Confirmé</option>
                <option value="reporté" {{ old('statut') == 'reporté' ? 'selected' : '' }}>Reporté</option>
            </select>
        </div>

        <div style="text-align: center;">
            <button type="submit" style="background: #007bff; color: white; padding: 12px 30px; border: none; border-radius: 4px; font-size: 16px; cursor: pointer;">
                Planifier l'entretien
            </button>
        </div>
    </form>
</div>

<style>
select:focus, input:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
}

button:hover {
    background: #0056b3 !important;
}
</style>
@endsection