<?php
// www/register.php
require 'db.php'; // On récupère la connexion BDD
session_start();

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Vérification basique
    if (!empty($username) && !empty($password) && !empty($email)) {
        
        // 1. Vérifier si l'utilisateur existe déjà
        $check = $pdo->prepare("SELECT user_id FROM user WHERE username = ? OR email = ?");
        $check->execute([$username, $email]);

        if ($check->rowCount() > 0) {
            $message = "❌ Ce nom d'utilisateur ou cet email est déjà pris.";
        } else {
            // 2. HACHAGE DU MOT DE PASSE (C'est l'étape clé !)
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // 3. Insertion dans la BDD
            try {
                $sql = "INSERT INTO user (username, email, password) VALUES (?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                
                if ($stmt->execute([$username, $email, $hashed_password])) {
                    // Succès : on redirige vers la connexion
                    $_SESSION['success'] = "Compte créé ! Connectez-vous maintenant.";
                    header("Location: index.php");
                    exit;
                }
            } catch (PDOException $e) {
                $message = "Erreur lors de l'inscription : " . $e->getMessage();
            }
        }
    } else {
        $message = "Veuillez remplir tous les champs.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription - CamView</title>
    <style>
        body { font-family: sans-serif; background: #e9ecef; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .card { background: white; padding: 40px; border-radius: 10px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); width: 350px; text-align: center; }
        h1 { color: #333; margin-bottom: 20px; }
        input { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; font-weight: bold; }
        button:hover { background-color: #0056b3; }
        .login-link { margin-top: 15px; display: block; color: #666; text-decoration: none; font-size: 0.9em; }
        .login-link:hover { text-decoration: underline; }
        .error { color: #dc3545; margin-top: 15px; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Inscription</h1>
        <form method="POST" action="">
            <input type="text" name="username" placeholder="Nom d'utilisateur" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Mot de passe" required>
            <button type="submit">S'inscrire</button>
        </form>
        
        <?php if ($message): ?>
            <div class="error"><?php echo $message; ?></div>
        <?php endif; ?>

        <a href="index.php" class="login-link">Déjà un compte ? Se connecter</a>
    </div>
</body>
</html>