@extends('layouts.rh')

@section('title', 'Nouvelle Affiliation')

@section('content')
<div class="container">
    <h1>Nouvelle Affiliation</h1>

    <form action="{{ route('affiliations.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Informations principales</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <label class="form-label">Employé <span class="text-danger">*</span></label>
                            <select name="employe_id" class="form-control" required>
                                <option value="">Sélectionner un employé</option>
                                @foreach($employes as $employe)
                                    <option value="{{ $employe->id }}">
                                        {{ $employe->nom }} {{ $employe->prenom }} - {{ $employe->poste }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">Type d'affiliation <span class="text-danger">*</span></label>
                            <select name="type" class="form-control" required>
                                <option value="">Sélectionner</option>
                                <option value="cnaps">CNAPS (Caisse Nationale de Prévoyance Sociale)</option>
                                <option value="ostie">OSTIE (Organisme de Sécurité Sociale des Travailleurs Indépendants)</option>
                                <option value="amit">AMIT (Assurance Maladie et Invalidité des Travailleurs)</option>
                                <option value="autre">Autre</option>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">Numéro d'affiliation <span class="text-danger">*</span></label>
                            <input type="text" name="numero_affiliation" class="form-control" required 
                                   placeholder="Ex: CNAPS-123456789">
                            <small class="form-text text-muted">Format unique pour chaque affiliation</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">Date de début <span class="text-danger">*</span></label>
                                    <input type="date" name="date_debut" class="form-control" required 
                                           value="{{ now()->format('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">Date d'expiration</label>
                                    <input type="date" name="date_expiration" class="form-control" 
                                           value="{{ now()->addYear()->format('Y-m-d') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Documents</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <label class="form-label">Documents justificatifs</label>
                            <div class="dropzone border-dashed border-2 border-gray-300 rounded p-4 mb-3 text-center" id="documents-dropzone">
                                <p class="text-muted mb-0">Glisser-déposer ou cliquer pour ajouter des fichiers</p>
                                <input type="file" name="documents[]" multiple accept=".pdf,.jpg,.jpeg,.png" class="d-none">
                            </div>
                            <small class="form-text text-muted">Formats acceptés : PDF, JPG, PNG (max 2MB chacun)</small>
                        </div>

                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input" name="valide" id="valide" checked>
                            <label class="form-check-label" for="valide">
                                Affiliation actuellement valide
                            </label>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control" rows="3" 
                                      placeholder="Informations supplémentaires..."></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12 text-end">
                <button type="submit" class="btn btn-success btn-lg">💾 Enregistrer l'affiliation</button>
                <a href="{{ route('affiliations.index') }}" class="btn btn-secondary btn-lg">❌ Annuler</a>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dropzone = document.getElementById('documents-dropzone');
    const input = dropzone.querySelector('input[type="file"]');

    // Drag & drop
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        dropzone.addEventListener(eventName, () => dropzone.classList.add('border-primary', 'bg-primary-subtle'), false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, () => dropzone.classList.remove('border-primary', 'bg-primary-subtle'), false);
    });

    dropzone.addEventListener('drop', handleDrop, false);

    function handleDrop(e) {
        const files = e.dataTransfer.files;
        input.files = files;
        handleFiles(files);
    }

    // Click to upload
    dropzone.addEventListener('click', () => input.click());

    input.addEventListener('change', (e) => {
        handleFiles(e.target.files);
    });

    function handleFiles(files) {
        Array.from(files).forEach(previewFile);
    }

    function previewFile(file) {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        
        reader.onloadend = () => {
            // Créer aperçu
            const preview = document.createElement('div');
            preview.className = 'file-preview d-flex align-items-center mb-2 p-2 border rounded';
            preview.innerHTML = `
                <i class="fas fa-file-pdf me-2"></i>
                <div class="flex-grow-1">
                    <small class="d-block text-truncate" style="max-width: 200px;">${file.name}</small>
                    <small class="text-muted">${(file.size / 1024).toFixed(1)} KB</small>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger ms-2" onclick="this.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            `;
            dropzone.parentElement.insertBefore(preview, dropzone);
        };
    }
});
</script>
@endsection