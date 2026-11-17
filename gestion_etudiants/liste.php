<?php
require_once __DIR__ . '/functions.php';
exiger_connexion();

$etudiants = lire_etudiants();
$q = trim((string) ($_GET['q'] ?? ''));
$tri = (string) ($_GET['tri'] ?? 'ajout');

if ($q !== '') {
    $etudiants = array_filter($etudiants, function (array $e) use ($q): bool {
        return stripos($e['nom'], $q) !== false || stripos($e['prenom'], $q) !== false;
    });
}

switch ($tri) {
    case 'nom':
        usort($etudiants, fn($a, $b) => strcasecmp($a['nom'], $b['nom']));
        break;
    case 'filiere':
        usort($etudiants, fn($a, $b) => strcasecmp($a['filiere'], $b['filiere']));
        break;
    case 'note':
        usort($etudiants, fn($a, $b) => $b['note'] <=> $a['note']);
        break;
    default:
        usort($etudiants, fn($a, $b) => $a['id'] <=> $b['id']);
        $tri = 'ajout';
}

$nombre = count($etudiants);
$moyenne = $nombre > 0 ? array_sum(array_column($etudiants, 'note')) / $nombre : 0;
$meilleure = $nombre > 0 ? max(array_column($etudiants, 'note')) : null;

$titre = 'Liste des étudiants';
include __DIR__ . '/header.php';

if (isset($_SESSION['flash'])):
    $message = $_SESSION['flash'];
    unset($_SESSION['flash']);
?>
    <div class="alert <?= h($message['type']) ?>"><?= h($message['message']) ?></div>
<?php endif; ?>

<div class="page-heading">
    <div>
        <h1>Liste des étudiants</h1>
        <p class="muted">Consultez, recherchez, triez et gérez les étudiants.</p>
    </div>
    <a class="btn" href="ajouter.php">+ Ajouter un étudiant</a>
</div>

<form class="filters" method="get" action="liste.php">
    <div class="filter-search">
        <label for="q">Recherche par nom ou prénom</label>
        <input type="search" id="q" name="q" value="<?= h($q) ?>" placeholder="Ex. Sara">
    </div>
    <div>
        <label for="tri">Trier par</label>
        <select id="tri" name="tri">
            <option value="ajout" <?= $tri === 'ajout' ? 'selected' : '' ?>>Ordre d'ajout</option>
            <option value="nom" <?= $tri === 'nom' ? 'selected' : '' ?>>Nom</option>
            <option value="filiere" <?= $tri === 'filiere' ? 'selected' : '' ?>>Filière</option>
            <option value="note" <?= $tri === 'note' ? 'selected' : '' ?>>Note décroissante</option>
        </select>
    </div>
    <button class="btn" type="submit">Appliquer</button>
    <a class="btn btn-light" href="liste.php">Réinitialiser</a>
</form>

<section class="stats">
    <article class="stat"><span>Étudiants affichés</span><strong><?= $nombre ?></strong></article>
    <article class="stat"><span>Moyenne des notes</span><strong><?= $nombre ? number_format($moyenne, 2, ',', ' ') : '—' ?></strong></article>
    <article class="stat"><span>Meilleure note</span><strong><?= $meilleure !== null ? number_format((float) $meilleure, 2, ',', ' ') . '/20' : '—' ?></strong></article>
</section>

<div class="table-wrap">
<table id="students-table">
    <thead>
        <tr><th>Photo</th><th>Nom</th><th>Prénom</th><th>Filière</th><th>Note</th><th>Actions</th></tr>
    </thead>
    <tbody>
    <?php if (!$etudiants): ?>
        <tr><td colspan="6" class="empty">Aucun étudiant</td></tr>
    <?php else: ?>
        <?php foreach ($etudiants as $e): ?>
            <tr>
                <td>
                    <?php if ($e['photo'] !== '' && file_exists(__DIR__ . '/uploads/' . $e['photo'])): ?>
                        <img class="avatar" src="uploads/<?= h($e['photo']) ?>" alt="Photo de <?= h($e['prenom']) ?>">
                    <?php else: ?>
                        <span class="avatar-placeholder"><?= h(strtoupper(substr($e['prenom'], 0, 1))) ?></span>
                    <?php endif; ?>
                </td>
                <td><?= h(strtoupper($e['nom'])) ?></td>
                <td><?= h(ucfirst($e['prenom'])) ?></td>
                <td><span class="tag"><?= h($e['filiere']) ?></span></td>
                <?php $classeNote = $e['note'] >= 10 ? 'note-good' : 'note-bad'; ?>
                <td class="<?= $classeNote ?>"><?= h((string) $e['note']) ?>/20</td>
                <td class="actions">
                    <a class="btn-small" href="ajouter.php?id=<?= (int) $e['id'] ?>">Modifier</a>
                    <a class="btn-small danger-link" href="supprimer.php?id=<?= (int) $e['id'] ?>" data-confirm="Voulez-vous vraiment supprimer cet étudiant ?">Supprimer</a>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>
</div>
<?php include __DIR__ . '/footer.php'; ?>
