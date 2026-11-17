# TP PHP – Gestion des étudiants

## 1. Installation avec XAMPP
1. Copiez le dossier `gestion_etudiants` dans `C:\xampp\htdocs\` (Windows) ou `/opt/lampp/htdocs/` (Linux).
2. Démarrez Apache depuis XAMPP.
3. Ouvrez `http://localhost/gestion_etudiants/test.php` pour vérifier PHP.
4. Ouvrez `http://localhost/gestion_etudiants/`.

## 2. Installation avec le serveur PHP intégré
Depuis le dossier parent du projet, lancez :
```bash
php -S localhost:8000
```
Puis ouvrez `http://localhost:8000/gestion_etudiants/` si le serveur a été lancé depuis le dossier parent. Sinon, depuis le dossier du projet :
```bash
php -S localhost:8000
```
et ouvrez `http://localhost:8000/`.

## 3. Comptes de démonstration
- `admin` / `admin123`
- `prof` / `prof123`

Ces identifiants sont définis dans `functions.php` et sont destinés au TP local.

## 4. Structure
- `functions.php` : session, constantes, sécurité et fonctions de lecture/écriture.
- `index.php` : connexion et cookie du dernier login.
- `liste.php` : tableau, recherche, tri et statistiques.
- `ajouter.php` : ajout et modification, validation et photo.
- `supprimer.php` : suppression d'un étudiant et de sa photo.
- `logout.php` : déconnexion.
- `header.php`, `footer.php` : structure commune.
- `style.css`, `script.js` : présentation et interactions.
- `data/etudiants.txt` : stockage texte au format `id;nom;prenom;filiere;note;photo`.
- `uploads/` : photos envoyées.

## 5. Remarques
Le fichier `data/etudiants.txt` doit être accessible en écriture par PHP. Le dossier `uploads/` doit également être accessible en écriture. Sur Ubuntu, si nécessaire, adaptez les permissions de ces deux emplacements pour l'utilisateur du serveur web.

La taille maximale est contrôlée dans le code à 1 Mo. PHP doit aussi autoriser cette taille dans `php.ini` (`upload_max_filesize` et `post_max_size`).
