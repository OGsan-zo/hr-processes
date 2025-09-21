@extends('layouts.rh')

@section('title', 'Modifier Affiliation : ' . $affiliation->type_formatted)

@section('content')
<div class="container">
    <h1>Modifier Affiliation</h1>

    <form action="{{ route('affiliations.update', $affiliation) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
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
                                    <option value="{{ $employe->id }}" {{ $affiliation->employe_id == $employe->id ? 'selected' : '' }}>
                                        {{ $employe->nom }} {{ $employe->prenom }} - {{ $employe->poste }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">Type d'affiliation <span class="text-danger">*</span></label>
                            <select name="type" class="form-control" required>
                                <option value="">Sélectionner</option>
                                <option value="cnaps" {{ $affiliation->type == 'cnaps' ? 'selected' : '' }}>CNAPS</option>
                                <option value="ostie" {{ $affiliation->type == 'ostie' ? 'selected' : '' }}>OSTIE</option>
                                <option value="amit" {{ $affiliation->type == 'amit' ? 'selected' : '' }}>AMIT</option>
                                <option value="autre" {{ $affiliation->type == 'autre' ? 'selected' : '' }}>Autre</option>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">Numéro d'affiliation <span class="text-danger">*</span></label>
                            <input type="text" name="numero_affiliation" class="form-control" 
                                   value="{{ $affiliation->numero_affiliation }}" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">Date de début <span class="text-danger">*</span></label>
                                    <input type="date" name="date_debut" class="form-control" 
                                           value="{{ $affiliation->date_debut->format('Y-m-d') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">Date d'expiration</label>
                                    <input type="date" name="date_expiration" class="form-control" 
                                           value="{{ $affiliation->date_expiration?->format('Y-m-d') }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input" name="valide" id="valide" {{ $affiliation->valide ? 'checked' : '' }}>
                            <label class="form-check-label" for="valide">Affiliation valide</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Documents actuels</h5>
                    </div>
                    <div class="card-body">
                        @if($affiliation->documents && count($affiliation->documents) > 0)
                            <div class="list-group mb-3">
                                @foreach($affiliation->documents as $index => $document)
                                    <a href="{{ Storage::url($document) }}" target="_blank" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas fa-file-pdf me-2"></i>
                                            Document {{ $index + 1 }}
                                        </div>
                                        <span class="badge bg-primary rounded-pill">PDF</span>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted">Aucun document joint actuellement</p>
                        @endif

                        <hr>
                        <h6>Nouveaux documents</h6>
                        <div class="form-group mb-3">
                            <label class="form-label">Ajouter de nouveaux documents</label>
                            <div class="dropzone border-dashed border-2 border-gray-300 rounded p-3 text-center" id="documents-dropzone">
                                <p class="text-muted mb-0">Glisser-déposer ou cliquer</p>
                                <input type="file" name="documents[]" multiple accept=".pdf,.jpg,.jpeg,.png" class="d-none">
                            </div>
                            <small class="form-text text-muted">Formats : PDF, JPG, PNG (max 2MB)</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12 text-end">
                <button type="submit" class="btn btn-success btn-lg">💾 Mettre à jour</button>
                <a href="{{ route('affiliations.index') }}" class="btn btn-secondary btn-lg">❌ Annuler</a>
            </div>
        </div>
    </form>
</div>

<script>
// Même script drag & drop que dans create.blade.php
document.addEventListener('DOMContentLoaded', function() {
    const dropzone = document.getElementById('documents-dropzone');
    const input = dropzone.querySelector('input[type="file"]');

    if (dropzone && input) {
        // Drag & drop events
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

        dropzone.addEventListener('click', () => input.click());

        input.addEventListener('change', (e) => {
            handleFiles(e.target.files);
        });

        function handleFiles(files) {
            Array.from(files).forEach(previewFile);
        }

        function previewFile(file) {
            const preview = document.createElement('div');
            preview.className = 'file-preview d-flex align-items-center mb-2 p-2 border rounded bg-light';
            preview.innerHTML = `
                <i class="fas fa-file-pdf me-2 text-primary"></i>
                <div class="flex-grow-1">
                    <small class="d-block text-truncate" style="max-width: 200px;">${file.name}</small>
                    <small class="text-muted">${(file.size / 1024).toFixed(1)} KB</small>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger ms-2" onclick="this.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            `;
            dropzone.parentElement.insertBefore(preview, dropzone);
        }
    }
});
</script>
@endsection