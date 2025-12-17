<?php
session_start(); // On démarre la session pour se souvenir du visiteur

// --- 1. CONFIGURATION ---
$host = 'lamp-mysql';
$db   = 'CamView';      // Le nom de ta base (vu sur ta capture)
$user = 'root';
$pass = 'Estiam2025*';  // Ton mot de passe root
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

// --- 2. CONNEXION BDD ---
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("❌ Erreur de connexion : " . $e->getMessage());
}

// --- 3. TRAITEMENT DECONNEXION ---
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header("Location: index.php");
    exit;
}

// --- 4. TRAITEMENT CONNEXION ---
$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // On cherche l'utilisateur dans ta table 'user'
    $stmt = $pdo->prepare("SELECT * FROM user WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    // On vérifie le mot de passe hashé
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id']; // Ta colonne s'appelle user_id
        $_SESSION['username'] = $user['username'];
        // On recharge la page pour passer en mode "connecté"
        header("Location: index.php");
        exit;
    } else {
        $message = "❌ Identifiant ou mot de passe incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Espace Membre CamView</title>
    <style>
        body { font-family: sans-serif; background: #e9ecef; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .card { background: white; padding: 40px; border-radius: 10px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); width: 350px; text-align: center; }
        h1 { color: #333; margin-bottom: 20px; }
        input { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background-color: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; font-weight: bold; }
        button:hover { background-color: #218838; }
        .logout { background-color: #dc3545; }
        .logout:hover { background-color: #c82333; }
        .error { color: #dc3545; margin-top: 15px; font-size: 0.9em; }
        .welcome-icon { font-size: 40px; margin-bottom: 10px; display: block; }
    </style>
</head>
<body>

    <div class="card">
        <?php if (isset($_SESSION['username'])): ?>
            <span class="welcome-icon">👤</span>
            <h1>Bienvenue<br><?php echo htmlspecialchars($_SESSION['username']); ?></h1>
            <p>Vous êtes connecté sur CamView.</p>
            
            <a href="index.php?action=logout" style="text-decoration: none;">
                <button class="logout">Se déconnecter</button>
            </a>

        <?php else: ?>
            <h1>Connexion</h1>
            <form method="POST" action="">
                <input type="text" name="username" placeholder="Utilisateur" required>
                <input type="password" name="password" placeholder="Mot de passe" required>
                <button type="submit">Entrer</button>
            </form>
            
            <?php if ($message): ?>
                <div class="error"><?php echo $message; ?></div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

</body>
</html>