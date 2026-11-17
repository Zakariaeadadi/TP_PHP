<?php
require_once __DIR__ . '/functions.php';

if (connecte()) {
    header('Location: liste.php');
    exit;
}

$erreur = '';
$login = $_COOKIE['dernier_user'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim((string) ($_POST['login'] ?? ''));
    $mdp = (string) ($_POST['mdp'] ?? '');

    if (isset(UTILISATEURS[$login]) && hash_equals(UTILISATEURS[$login], $mdp)) {
        session_regenerate_id(true);
        $_SESSION['login'] = $login;
        setcookie('dernier_user', $login, [
            'expires' => time() + 7 * 24 * 60 * 60,
            'path' => '/',
            'samesite' => 'Lax',
        ]);
        flash('Bienvenue ' . $login . ' !');
        header('Location: liste.php');
        exit;
    }

    $erreur = 'Login ou mot de passe incorrect.';
}

$titre = 'Connexion';
include __DIR__ . '/header.php';
?>
<section class="form-card login-card">
    <h1>Connexion</h1>
    <p class="muted">Connectez-vous pour accéder à la gestion des étudiants.</p>

    <?php if ($erreur !== ''): ?>
        <div class="alert error"><?= h($erreur) ?></div>
    <?php endif; ?>

    <form method="post" action="index.php">
        <label for="login">Login</label>
        <input type="text" id="login" name="login" value="<?= h($login) ?>" required autofocus>

        <label for="mdp">Mot de passe</label>
        <input type="password" id="mdp" name="mdp" required>

        <button type="submit" class="btn">Se connecter</button>
    </form>
    <div class="demo">
        <strong>Comptes de démonstration</strong>
        <p>admin / admin123</p>
        <p>prof / prof123</p>
    </div>
</section>
<?php include __DIR__ . '/footer.php'; ?>
