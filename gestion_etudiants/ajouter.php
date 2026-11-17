<?php
require_once __DIR__ . '/functions.php';
exiger_connexion();

$liste = lire_etudiants();
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$modification = $id > 0;

if ($modification && !isset($liste[$id])) {
    flash('Étudiant introuvable.', 'error');
    header('Location: liste.php');
    exit;
}

$etudiant = $modification
    ? $liste[$id]
    : ['nom' => '', 'prenom' => '', 'filiere' => 'GI', 'note' => '', 'photo' => ''];

$valeurs = $etudiant;
$erreurs = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $valeurs['nom'] = nettoyer((string) ($_POST['nom'] ?? ''));
    $valeurs['prenom'] = nettoyer((string) ($_POST['prenom'] ?? ''));
    $valeurs['filiere'] = (string) ($_POST['filiere'] ?? '');
    $noteSaisie = trim((string) ($_POST['note'] ?? ''));
    $valeurs['note'] = $noteSaisie;

    if ($valeurs['nom'] === '') $erreurs[] = 'Le nom est obligatoire.';
    if ($valeurs['prenom'] === '') $erreurs[] = 'Le prénom est obligatoire.';
    if (!in_array($valeurs['filiere'], FILIERES, true)) $erreurs[] = 'Veuillez choisir une filière valide.';
    if (!is_numeric($noteSaisie) || (float) $noteSaisie < 0 || (float) $noteSaisie > 20) {
        $erreurs[] = 'La note doit être un nombre compris entre 0 et 20.';
    }

    $anciennePhoto = $modification ? $liste[$id]['photo'] : '';
    $nouvellePhoto = traiter_photo($_FILES['photo'] ?? null, $erreurs);

    if (!$erreurs) {
        $photo = $nouvellePhoto ?? $anciennePhoto;

        if ($modification) {
            $liste[$id] = [
                'id' => $id,
                'nom' => $valeurs['nom'],
                'prenom' => $valeurs['prenom'],
                'filiere' => $valeurs['filiere'],
                'note' => (float) $noteSaisie,
                'photo' => $photo,
            ];
            if ($nouvellePhoto !== null && $anciennePhoto !== '' && file_exists(__DIR__ . '/uploads/' . $anciennePhoto)) {
                unlink(__DIR__ . '/uploads/' . $anciennePhoto);
            }
            sauver_etudiants($liste);
            flash('Les informations de l’étudiant ont été modifiées.');
        } else {
            $nouvelId = empty($liste) ? 1 : max(array_keys($liste)) + 1;
            $liste[$nouvelId] = [
                'id' => $nouvelId,
                'nom' => $valeurs['nom'],
                'prenom' => $valeurs['prenom'],
                'filiere' => $valeurs['filiere'],
                'note' => (float) $noteSaisie,
                'photo' => $photo,
            ];
            sauver_etudiants($liste);
            flash('Étudiant ajouté avec succès.');
        }

        header('Location: liste.php');
        exit;
    }
}

$titre = $modification ? 'Modifier un étudiant' : 'Ajouter un étudiant';
include __DIR__ . '/header.php';
?>
<section class="form-card">
    <h1><?= $modification ? 'Modifier un étudiant' : 'Ajouter un étudiant' ?></h1>

    <?php if ($erreurs): ?>
        <div class="alert error">
            <strong>Veuillez corriger les erreurs suivantes :</strong>
            <ul><?php foreach ($erreurs as $erreur): ?><li><?= h($erreur) ?></li><?php endforeach; ?></ul>
        </div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" action="ajouter.php<?= $modification ? '?id=' . $id : '' ?>">
        <label for="nom">Nom</label>
        <input type="text" id="nom" name="nom" value="<?= h((string) $valeurs['nom']) ?>" required>

        <label for="prenom">Prénom</label>
        <input type="text" id="prenom" name="prenom" value="<?= h((string) $valeurs['prenom']) ?>" required>

        <label for="filiere">Filière</label>
        <select id="filiere" name="filiere" required>
            <?php foreach (FILIERES as $f): ?>
                <option value="<?= h($f) ?>" <?= $valeurs['filiere'] === $f ? 'selected' : '' ?>><?= h($f) ?></option>
            <?php endforeach; ?>
        </select>

        <label for="note">Note / 20</label>
        <input type="number" id="note" name="note" min="0" max="20" step="0.01" value="<?= h((string) $valeurs['note']) ?>" required>

        <label for="photo">Photo (facultative, JPG/PNG, 1 Mo maximum)</label>
        <input type="file" id="photo" name="photo" accept=".jpg,.jpeg,.png,image/jpeg,image/png">
        <?php if ($modification && $etudiant['photo'] !== ''): ?>
            <p class="muted">Photo actuelle : <?= h($etudiant['photo']) ?>. Laissez vide pour la conserver.</p>
        <?php endif; ?>

        <div class="form-actions">
            <button type="submit" class="btn"><?= $modification ? 'Enregistrer les modifications' : 'Ajouter' ?></button>
            <a class="btn btn-light" href="liste.php">Annuler</a>
        </div>
    </form>
</section>
<?php include __DIR__ . '/footer.php'; ?>
