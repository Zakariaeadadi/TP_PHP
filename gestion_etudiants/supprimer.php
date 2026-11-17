<?php
require_once __DIR__ . '/functions.php';
exiger_connexion();

$id = (int) ($_GET['id'] ?? 0);
$liste = lire_etudiants();

if ($id > 0 && isset($liste[$id])) {
    $photo = $liste[$id]['photo'];
    unset($liste[$id]);

    if ($photo !== '' && file_exists(__DIR__ . '/uploads/' . $photo)) {
        unlink(__DIR__ . '/uploads/' . $photo);
    }

    sauver_etudiants($liste);
    flash('Étudiant supprimé avec succès.');
} else {
    flash('Étudiant introuvable.', 'error');
}

header('Location: liste.php');
exit;
