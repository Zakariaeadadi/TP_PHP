<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('FICHIER', __DIR__ . '/data/etudiants.txt');
define('FILIERES', ['GI', 'IDIA', 'GE', 'GC']);
define('UTILISATEURS', [
    'admin' => 'admin123',
    'prof'  => 'prof123',
]);

function h(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function connecte(): bool {
    return isset($_SESSION['login']);
}

function exiger_connexion(): void {
    if (!connecte()) {
        header('Location: index.php');
        exit;
    }
}

function lire_etudiants(): array {
    if (!file_exists(FICHIER)) {
        return [];
    }

    $liste = [];
    $lignes = file(FICHIER, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lignes as $ligne) {
        $champs = explode(';', $ligne);
        if (count($champs) !== 6) {
            continue;
        }

        [$id, $nom, $prenom, $filiere, $note, $photo] = $champs;
        $id = (int) $id;
        if ($id <= 0) {
            continue;
        }

        $liste[$id] = [
            'id' => $id,
            'nom' => $nom,
            'prenom' => $prenom,
            'filiere' => $filiere,
            'note' => (float) $note,
            'photo' => $photo,
        ];
    }

    return $liste;
}

function sauver_etudiants(array $liste): void {
    $lignes = [];

    foreach ($liste as $etudiant) {
        $lignes[] = implode(';', [
            (string) (int) $etudiant['id'],
            nettoyer((string) $etudiant['nom']),
            nettoyer((string) $etudiant['prenom']),
            nettoyer((string) $etudiant['filiere']),
            (string) $etudiant['note'],
            nettoyer((string) ($etudiant['photo'] ?? '')),
        ]);
    }

    file_put_contents(FICHIER, $lignes ? implode(PHP_EOL, $lignes) . PHP_EOL : '');
}

function nettoyer(string $s): string {
    return trim(str_replace([";", "\r", "\n"], '', $s));
}

function flash(string $message, string $type = 'success'): void {
    $_SESSION['flash'] = ['message' => $message, 'type' => $type];
}

function traiter_photo(?array $fichier, array &$erreurs): ?string {
    if (!$fichier || !isset($fichier['error']) || $fichier['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ($fichier['error'] !== UPLOAD_ERR_OK) {
        $erreurs[] = "Une erreur est survenue pendant l'envoi de la photo.";
        return null;
    }

    if ($fichier['size'] > 1024 * 1024) {
        $erreurs[] = 'La photo ne doit pas dépasser 1 Mo.';
        return null;
    }

    $extension = strtolower(pathinfo($fichier['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, ['jpg', 'jpeg', 'png'], true)) {
        $erreurs[] = 'Format de photo invalide. Utilisez JPG, JPEG ou PNG.';
        return null;
    }

    // Vérification réelle du type MIME, en complément de l'extension.
    $mime = mime_content_type($fichier['tmp_name']);
    $mimesAutorises = ['image/jpeg', 'image/png'];
    if (!in_array($mime, $mimesAutorises, true)) {
        $erreurs[] = 'Le fichier envoyé ne semble pas être une image JPG ou PNG.';
        return null;
    }

    $nomPhoto = uniqid('etu_', true) . '.' . $extension;
    $destination = __DIR__ . '/uploads/' . $nomPhoto;

    if (!move_uploaded_file($fichier['tmp_name'], $destination)) {
        $erreurs[] = "Impossible d'enregistrer la photo sur le serveur.";
        return null;
    }

    return $nomPhoto;
}
