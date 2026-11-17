<?php
$titre = $titre ?? 'Gestion des étudiants';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($titre) ?> - Gestion des étudiants</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header class="site-header">
    <div class="header-inner">
        <a class="brand" href="<?= connecte() ? 'liste.php' : 'index.php' ?>">Gestion des étudiants</a>
        <?php if (connecte()): ?>
            <nav>
                <a href="liste.php">Liste</a>
                <a href="ajouter.php">Ajouter</a>
                <a href="logout.php">Déconnexion (<?= h((string) $_SESSION['login']) ?>)</a>
            </nav>
        <?php endif; ?>
    </div>
</header>
<main class="container">
