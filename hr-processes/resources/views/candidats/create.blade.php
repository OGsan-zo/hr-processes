<!DOCTYPE html>
<html>
<head>
    <title>Enregistrer un Candidat</title>
</head>
<body>
    <h1>Ajouter un candidat</h1>

    @if(session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    <form action="{{ route('candidats.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <label>Nom :</label>
        <input type="text" name="nom" required><br><br>

        <label>Prénom :</label>
        <input type="text" name="prenom" required><br><br>

        <label>Âge :</label>
        <input type="number" name="age" min="18" required><br><br>

        <label>Diplôme :</label>
        <input type="text" name="diplome"><br><br>

        <label>CV (PDF) :</label>
        <input type="file" name="cv"><br><br>

        <button type="submit">Enregistrer</button>
    </form>
</body>
</html>
