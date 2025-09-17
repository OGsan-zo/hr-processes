<!DOCTYPE html>
<html>
<head>
    <title>Ajouter un Employé</title>
</head>
<body>
    <h1>Ajouter un Employé</h1>

    @if(session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    <form action="{{ route('employes.store') }}" method="POST">
        @csrf
        <label>Nom :</label>
        <input type="text" name="nom" required><br><br>

        <label>Prénom :</label>
        <input type="text" name="prenom" required><br><br>

        <label>Poste :</label>
        <input type="text" name="poste"><br><br>

        <label>Salaire :</label>
        <input type="number" step="0.01" name="salaire"><br><br>

        <label>Compétences :</label>
        <textarea name="competences"></textarea><br><br>

        <label>Historique :</label>
        <textarea name="historique"></textarea><br><br>

        <button type="submit">Enregistrer</button>
    </form>
</body>
</html>
