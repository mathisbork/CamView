<?php
// www/index.php
require 'db.php'; // On utilise la connexion partagée
session_start();

// --- TRAITEMENT DECONNEXION ---
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header("Location: index.php");
    exit;
}

// --- TRAITEMENT CONNEXION ---
$message = "";
if (isset($_SESSION['success'])) {
    $message = "<span style='color:green'>".$_SESSION['success']."</span>";
    unset($_SESSION['success']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // On cherche l'utilisateur
    $stmt = $pdo->prepare("SELECT * FROM user WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    // VERIFICATION DU HASH (C'est ici que ça marchera enfin !)
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
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
    <title>Connexion CamView</title>
    <style>
        /* Image de fond pour toute la page */
        body { 
            font-family: 'Segoe UI', sans-serif; 
            /* Image de fond (Nature sombre pour faire ressortir le verre) */
            background: url('img/Background.jpg') no-repeat center center fixed; 
            background-size: cover; /* L'image couvre tout l'écran */
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
            margin: 0; 
        }

        /* L'effet LIQUID GLASS */
        .card { 
            /* Fond blanc très transparent (15%) */
            background: rgba(255, 255, 255, 0.15); 
            
            /* Le flou magique derrière la box */
            backdrop-filter: blur(15px); 
            -webkit-backdrop-filter: blur(15px); /* Pour Safari */
            
            /* Bordure fine et transparente pour l'effet "verre" */
            border: 1px solid rgba(255, 255, 255, 0.2); 
            
            padding: 40px; 
            border-radius: 16px; /* Coins bien arrondis */
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.5); /* Ombre portée */
            width: 400px; 
            text-align: center; 
            color: white; /* Texte en blanc pour le contraste */
        }

        h1 { margin-bottom: 25px; font-weight: 600; letter-spacing: 1px; }

        /* Les champs de saisie (Inputs) aussi en mode verre */
        input { 
            width: 100%; 
            padding: 12px; 
            margin: 10px 0; 
            background: rgba(255, 255, 255, 0.2); /* Fond semi-transparent */
            border: none; 
            border-radius: 30px; /* Inputs arrondis */
            color: white; 
            font-size: 16px;
            box-sizing: border-box;
            outline: none;
            text-align: center;
            transition: 0.3s;
        }

        /* Couleur du texte "placeholder" (ex: "Mot de passe") */
        input::placeholder { color: rgba(255, 255, 255, 0.7); }

        /* Effet au clic dans l'input */
        input:focus { background: rgba(255, 255, 255, 0.4); box-shadow: 0 0 10px rgba(255,255,255,0.5); }

        /* Le bouton */
        button { 
            width: 100%; 
            padding: 12px; 
            margin-top: 15px;
            background-color: rgba(40, 167, 69, 0.8); 
            color: white; 
            border: none; 
            border-radius: 30px; 
            cursor: pointer; 
            font-size: 16px; 
            font-weight: bold; 
            transition: 0.3s;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        button:hover { background-color: #218838; transform: scale(1.02); }
        
        /* Bouton déconnexion rouge */
        .logout { background-color: rgba(220, 53, 69, 0.8); }
        .logout:hover { background-color: #c82333; }

        /* Liens */
        a { color: #fff; text-decoration: none; font-size: 0.9em; display: inline-block; margin-top: 15px; border-bottom: 1px solid transparent; transition: 0.3s;}
        a:hover { border-bottom: 1px solid white; }

        .error { color: #ffadad; margin-top: 15px; text-shadow: 0 1px 2px black;}
        .welcome-icon { font-size: 50px; margin-bottom: 10px; display: block; filter: drop-shadow(0 2px 5px rgba(0,0,0,0.3)); }
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
            <a href="img/icon.png" style="display: block; margin: 0 auto 20px auto; width: 100px; height: 100px;">
                <img src="img/icon.png" alt="Logo CamView" style="width: 100%; height: auto;">
            </a>
            <h1>Connexion</h1>
            <form method="POST" action="">
                <input type="text" name="username" placeholder="Utilisateur" required>
                <input type="password" name="password" placeholder="Mot de passe" required>
                <button type="submit">Entrer</button>
            </form>
            
            <?php if ($message): ?>
                <div class="error"><?php echo $message; ?></div>
            <?php endif; ?>

            <a href="register.php" class="register-link">Pas encore inscrit ? Créer un compte</a>
        <?php endif; ?>
    </div>

</body>
</html>