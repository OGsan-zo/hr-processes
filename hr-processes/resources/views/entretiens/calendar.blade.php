@extends('layouts.rh')

@section('head')
@parent
<!-- FullCalendar CSS local -->
<link href="{{ asset('fullcalendar/dist/index.global.min.css') }}" rel="stylesheet">
<style>
.calendar-container {
    padding: 20px;
    max-width: 1400px;
    margin: 0 auto;
}

.alert {
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 4px;
}

.alert-success {
    background-color: #d4edda;
    border: 1px solid #c3e6cb;
    color: #155724;
}

.action-buttons {
    margin-bottom: 20px;
}

.btn {
    display: inline-block;
    padding: 10px 15px;
    margin-right: 10px;
    text-decoration: none;
    border-radius: 5px;
    border: none;
    cursor: pointer;
    font-size: 14px;
    transition: background-color 0.3s;
}

.btn-primary {
    background-color: #007bff;
    color: white;
}

.btn-success {
    background-color: #28a745;
    color: white;
}

.btn-outline {
    background-color: transparent;
    border: 1px solid #007bff;
    color: #007bff;
}

.btn:hover {
    opacity: 0.9;
}

.calendar-controls {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
    padding: 0 10px;
}

.legend {
    display: flex;
    justify-content: center;
    gap: 15px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.legend-item {
    padding: 5px 10px;
    border-radius: 3px;
    font-size: 12px;
    color: white;
}

.legend-item.planifie { background-color: #007bff; }
.legend-item.confirme { background-color: #28a745; }
.legend-item.reporte { background-color: #ffc107; color: #212529; }
.legend-item.annule { background-color: #dc3545; }
.legend-item.termine { background-color: #6c757d; }

#calendar {
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    padding: 10px;
}

/* Styles pour les événements selon leur statut */
.fc-event-planifie { background-color: #007bff; border-color: #007bff; }
.fc-event-confirme { background-color: #28a745; border-color: #28a745; }
.fc-event-reporte { background-color: #ffc107; border-color: #ffc107; color: #212529; }
.fc-event-annule { background-color: #dc3545; border-color: #dc3545; }
.fc-event-termine { background-color: #6c757d; border-color: #6c757d; }

/* Modal styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
}

.modal-content {
    background-color: white;
    margin: 10% auto;
    padding: 20px;
    border-radius: 8px;
    width: 90%;
    max-width: 500px;
    position: relative;
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    position: absolute;
    right: 15px;
    top: 10px;
}

.close:hover {
    color: black;
}

@media (max-width: 768px) {
    .calendar-controls {
        flex-direction: column;
        gap: 10px;
    }
    
    .legend {
        justify-content: flex-start;
    }
}
</style>
@endsection

@section('content')
<div class="calendar-container">
    <h1>Planning des entretiens</h1>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="action-buttons">
        <a href="{{ route('entretiens.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Planifier un nouvel entretien
        </a>
        <a href="{{ route('entretiens.index') }}" class="btn btn-success">
            <i class="fas fa-list"></i> Vue liste
        </a>
    </div>

    <!-- Légende -->
    <div class="legend">
        <span class="legend-item planifie">Planifié</span>
        <span class="legend-item confirme">Confirmé</span>
        <span class="legend-item reporte">Reporté</span>
        <span class="legend-item annule">Annulé</span>
        <span class="legend-item termine">Terminé</span>
    </div>

    <!-- Calendrier FullCalendar -->
    <div id="calendar"></div>
</div>

<!-- Modal pour les détails de l'entretien -->
<div id="eventModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <div id="modalBody"></div>
    </div>
</div>

<!-- FullCalendar JS local -->
<script src="{{ asset('fullcalendar/dist/index.global.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Données des entretiens depuis PHP
    const entretiens = [
        @foreach($entretiens as $entretien)
        {
            id: {{ $entretien->id }},
            title: '{{ addslashes(($entretien->candidat->nom ?? '') . " " . ($entretien->candidat->prenom ?? '')) }}',
            start: '{{ $entretien->date_entretien }}',
            @if($entretien->duree)
            end: '{{ \Carbon\Carbon::parse($entretien->date_entretien)->addMinutes($entretien->duree)->toDateTimeString() }}',
            @else
            end: '{{ \Carbon\Carbon::parse($entretien->date_entretien)->addHour()->toDateTimeString() }}',
            @endif
            extendedProps: {
                duree: {{ $entretien->duree ?? 60 }},
                candidat_complet: '{{ addslashes(($entretien->candidat->nom ?? '') . " " . ($entretien->candidat->prenom ?? '')) }}',
                poste: '{{ addslashes($entretien->annonce->titre ?? '') }}',
                statut: '{{ strtolower($entretien->statut ?? 'planifie') }}'
            }
        },
        @endforeach
    ];

    const calendarEl = document.getElementById('calendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'fr',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        },
        buttonText: {
            today: 'Aujourd\'hui',
            month: 'Mois',
            week: 'Semaine',
            day: 'Jour',
            list: 'Liste'
        },
        events: entretiens,
        eventClick: function(info) {
            showEventDetails(info.event);
        },
        dateClick: function(info) {
            if (confirm(`Voulez-vous planifier un entretien le ${info.dateStr} ?`)) {
                window.location.href = `{{ route('entretiens.create') }}?date=${info.dateStr}`;
            }
        },
        eventClassNames: function(arg) {
            // Ajouter la classe CSS selon le statut
            return ['fc-event-' + arg.event.extendedProps.statut];
        },
        editable: false,
        selectable: true,
        selectMirror: true,
        dayMaxEvents: true,
        weekends: true,
        firstDay: 1, // Lundi comme premier jour de la semaine
        businessHours: {
            daysOfWeek: [1, 2, 3, 4, 5], // Lundi à vendredi
            startTime: '08:00',
            endTime: '18:00',
        },
        slotMinTime: '08:00',
        slotMaxTime: '20:00',
        allDaySlot: false,
        height: 'auto',
        navLinks: true, // Permet de cliquer sur les jours/semaines
        nowIndicator: true, // Affiche un indicateur de l'heure actuelle
    });

    calendar.render();

    // Fonction pour afficher les détails de l'événement
    function showEventDetails(event) {
        const modal = document.getElementById('eventModal');
        const modalBody = document.getElementById('modalBody');
        
        const startDate = new Date(event.start);
        const endDate = event.end ? new Date(event.end) : null;
        
        modalBody.innerHTML = `
            <h3>Détails de l'entretien</h3>
            <p><strong>Candidat:</strong> ${event.extendedProps.candidat_complet}</p>
            <p><strong>Poste:</strong> ${event.extendedProps.poste}</p>
            <p><strong>Date:</strong> ${startDate.toLocaleDateString('fr-FR', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}</p>
            <p><strong>Heure:</strong> ${startDate.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' })}</p>
            <p><strong>Durée:</strong> ${event.extendedProps.duree} minutes</p>
            <p><strong>Statut:</strong> <span class="legend-item ${event.extendedProps.statut}">${event.extendedProps.statut}</span></p>
            <div style="margin-top: 20px;">
                <a href="{{ url('entretiens') }}/${event.id}/edit" class="btn btn-primary">Modifier</a>
                <form action="{{ url('entretiens') }}/${event.id}" method="POST" style="display: inline-block;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet entretien ?')">Supprimer</button>
                </form>
                <button class="btn btn-outline" onclick="document.getElementById('eventModal').style.display = 'none'">Fermer</button>
            </div>
        `;
        
        modal.style.display = 'block';
    }

    // Gestion de la fermeture du modal
    document.querySelector('.close').addEventListener('click', () => {
        document.getElementById('eventModal').style.display = 'none';
    });

    window.addEventListener('click', (e) => {
        if (e.target === document.getElementById('eventModal')) {
            document.getElementById('eventModal').style.display = 'none';
        }
    });
});
</script>
@endsection