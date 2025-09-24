@extends('layouts.rh')

@section('content')
<div style="padding: 20px;">
    <h1>Planning des entretiens</h1>
    
    @if(session('success'))
        <div style="color: green; margin-bottom: 20px;">{{ session('success') }}</div>
    @endif

    <div style="margin-bottom: 20px;">
        <a href="{{ route('entretiens.create') }}" style="background: #007bff; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;">
            Planifier un nouvel entretien
        </a>
        <a href="{{ route('entretiens.index') }}" style="background: #28a745; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; margin-left: 10px;">
            Vue liste
        </a>
    </div>

    <!-- Conteneur du calendrier -->
    <div id="calendar"></div>
</div>

<!-- Inclusion de FullCalendar depuis CDN -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/main.min.css' rel='stylesheet' />
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/locales/fr.js'></script>

<style>
/* Styles personnalisés pour le calendrier */
#calendar {
    max-width: 100%;
    margin: 20px 0;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    padding: 20px;
}

/* Couleurs par statut */
.fc-event.statut-planifie { background-color: #007bff; border-color: #0056b3; }
.fc-event.statut-confirme { background-color: #28a745; border-color: #1e7e34; }
.fc-event.statut-reporte { background-color: #ffc107; border-color: #d39e00; color: #212529; }
.fc-event.statut-annule { background-color: #dc3545; border-color: #bd2130; }
.fc-event.statut-termine { background-color: #6c757d; border-color: #545b62; }

/* Style pour les événements */
.fc-event {
    cursor: pointer;
    font-size: 12px;
}

.fc-event:hover {
    opacity: 0.8;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    
    var calendar = new FullCalendar.Calendar(calendarEl, {
        // Configuration de base
        locale: 'fr',
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        },
        
        // Options d'affichage
        height: 'auto',
        navLinks: true,
        selectable: true,
        selectMirror: true,
        dayMaxEvents: true,
        
        // Format des heures
        eventTimeFormat: {
            hour: '2-digit',
            minute: '2-digit',
            hour12: false
        },
        
        // Données des événements depuis Laravel
        events: [
            @foreach($entretiens as $entretien)
            {
                id: '{{ $entretien->id }}',
                title: '{{ addslashes($entretien->candidat->nom . " " . $entretien->candidat->prenom) }} - {{ addslashes($entretien->annonce->titre) }}',
                start: '{{ $entretien->date_entretien }}',
                end: '{{ \Carbon\Carbon::parse($entretien->date_entretien)->addMinutes($entretien->duree ?? 60)->format('Y-m-d H:i:s') }}',
                className: 'statut-{{ strtolower($entretien->statut ?? 'planifie') }}',
                extendedProps: {
                    candidat: '{{ addslashes($entretien->candidat->nom . " " . $entretien->candidat->prenom) }}',
                    annonce: '{{ addslashes($entretien->annonce->titre) }}',
                    duree: '{{ $entretien->duree ?? 60 }}',
                    statut: '{{ $entretien->statut ?? 'planifié' }}'
                }
            },
            @endforeach
        ],
        
        // Événement lors du clic sur un événement
        eventClick: function(info) {
            alert(
                'Entretien: ' + info.event.extendedProps.candidat + '\n' +
                'Poste: ' + info.event.extendedProps.annonce + '\n' +
                'Date: ' + info.event.start.toLocaleString('fr-FR') + '\n' +
                'Durée: ' + info.event.extendedProps.duree + ' minutes\n' +
                'Statut: ' + info.event.extendedProps.statut
            );
        },
        
        // Événement lors de la sélection d'une date
        select: function(info) {
            if (confirm('Voulez-vous planifier un entretien le ' + info.startStr + ' ?')) {
                var url = '{{ route("entretiens.create") }}' + '?date=' + info.startStr;
                window.location.href = url;
            }
            calendar.unselect();
        },
        
        // Style des événements
        eventDidMount: function(info) {
            info.el.title = 
                'Candidat: ' + info.event.extendedProps.candidat + '\n' +
                'Poste: ' + info.event.extendedProps.annonce + '\n' +
                'Durée: ' + info.event.extendedProps.duree + ' minutes\n' +
                'Statut: ' + info.event.extendedProps.statut;
        }
    });
    
    calendar.render();
});
</script>
@endsection