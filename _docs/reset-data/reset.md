Parfait 👍 tu es sur PostgreSQL (`pgsql`), donc il faut adapter la commande pour **désactiver/réactiver les contraintes de clés étrangères** (Postgres ne supporte pas `SET FOREIGN_KEY_CHECKS=0` comme MySQL).

Avec PostgreSQL, on fait :

```sql
SET session_replication_role = replica;
-- ... truncate ici ...
SET session_replication_role = DEFAULT;
```

---

## 📌 Étapes complètes à suivre

### 1. Créer la commande artisan

Dans ton terminal à la racine du projet :

```bash
php artisan make:command ResetDatabase
```

---

### 2. Modifier `app/Console/Commands/ResetDatabase.php`

Ouvre le fichier généré et mets ceci :

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ResetDatabase extends Command
{
    protected $signature = 'db:reset-project';
    protected $description = 'Réinitialiser uniquement les tables métiers sans toucher aux utilisateurs et rôles (PostgreSQL)';

    public function handle()
    {
        $tablesToTruncate = [
            'employes',
            'candidatures',
            'annonces',
            'entretiens',
            'contrats',
            'cv_analyses',
            'candidats',
            'questions',
            'reponses',
            'resultats_tests',
            'affiliations',
            'tests',
        ];

        // Désactiver les contraintes de FK
        DB::statement('SET session_replication_role = replica;');

        foreach ($tablesToTruncate as $table) {
            DB::statement("TRUNCATE TABLE {$table} RESTART IDENTITY CASCADE;");
            $this->info("Table {$table} vidée ✅");
        }

        // Réactiver les contraintes de FK
        DB::statement('SET session_replication_role = DEFAULT;');

        $this->info('✅ Réinitialisation terminée sans toucher aux utilisateurs et rôles !');
    }
}
```

---

### 3. Vérifie ta config `.env`

Ton `.env` doit contenir :

```
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=hr_processes
DB_USERNAME=hr_user
DB_PASSWORD=hruser
```

Laravel se connectera directement à `hr_processes` avec `hr_user`.

---

### 4. Exécuter la commande

Maintenant tu peux lancer :

```bash
php artisan db:reset-project
```

Cela va :

* Désactiver temporairement les contraintes FK
* **Vider uniquement les tables métiers** (`employes`, `candidatures`, etc.)
* Réactiver les contraintes FK
* Laisser `users`, `roles`, `permissions`, etc. intacts

---

### 5. (Optionnel) Réensemencer les rôles & users

Si tu veux être sûr que tout reste cohérent après un reset, tu peux reseeder :

```bash
php artisan db:seed --class=RoleSeeder
php artisan db:seed --class=UserSeeder
```

---

👉 Et voilà 🚀 Tu as une commande prête qui remet à zéro les données métiers **sans toucher à tes utilisateurs et rôles**.

Veux-tu que je t’ajoute aussi une **option `--with-seed`** dans cette commande (pour qu’elle relance directement tes seeders après reset) ?
