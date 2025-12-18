<?php
// www/index.php
require 'db.php'; // Connexion BDD
session_start();

// --- 1. GESTION DE LA DÉCONNEXION ---
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header("Location: index.php");
    exit;
}

// --- 2. REDIRECTION SI DÉJÀ CONNECTÉ ---
// Si l'utilisateur a déjà une session, on l'envoie direct au Dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

// --- 3. TRAITEMENT DU FORMULAIRE DE CONNEXION ---
$message = "";

// Petit message vert si on vient de s'inscrire
if (isset($_SESSION['success'])) {
    $message = "<span style='color:#a8ffbc; font-weight:bold; text-shadow: 0 1px 2px rgba(0,0,0,0.5);'>".$_SESSION['success']."</span>";
    unset($_SESSION['success']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Recherche de l'utilisateur
    $stmt = $pdo->prepare("SELECT * FROM user WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    // Vérification du mot de passe
    if ($user && password_verify($password, $user['password'])) {
        // Création de la session (Le Token serveur)
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        
        // SUCCÈS : Redirection vers l'application
        header("Location: dashboard.php");
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion CamView</title>
    <style>
        /* Reset de base */
        * { box-sizing: border-box; }

        /* Image de fond */
        body { 
            font-family: 'Segoe UI', sans-serif;
            background: url('img/Background.jpg') no-repeat center center fixed; 
            background-size: cover;
            display: flex; 
            justify-content: center; 
            align-items: center; 
            min-height: 100vh; 
            margin: 0; 
            padding: 20px;
        }

        /* --- CARTE GLASSMORPHISM --- */
        .card { 
            background: rgba(255, 255, 255, 0.1); 
            backdrop-filter: blur(25px); 
            -webkit-backdrop-filter: blur(25px);
            
            border: 1px solid rgba(255, 255, 255, 0.3); 
            border-top: 1px solid rgba(255, 255, 255, 0.5); 
            
            padding: 40px 30px; 
            border-radius: 24px; 
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2); 
            width: 100%;
            max-width: 400px; 
            text-align: center; 
            color: white;
        }

        /* Logo */
        .logo {
            width: 120px;
            height: auto;
            margin-bottom: 20px;
            filter: drop-shadow(0 4px 4px rgba(0,0,0,0.3));
        }

        h1 { 
            margin-bottom: 30px; 
            font-weight: 700; 
            letter-spacing: 1px; 
            font-size: 2rem;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        /* Inputs */
        input { 
            width: 100%; 
            padding: 15px; 
            margin: 12px 0; 
            background: rgba(255, 255, 255, 0.15); 
            border: 1px solid rgba(255, 255, 255, 0.1); 
            border-radius: 50px; 
            color: white; 
            font-size: 16px;
            outline: none;
            text-align: center;
            transition: all 0.3s ease;
        }

        input::placeholder { color: rgba(255, 255, 255, 0.6); }

        input:focus { 
            background: rgba(255, 255, 255, 0.25); 
            border-color: rgba(255, 255, 255, 0.5);
            box-shadow: 0 0 15px rgba(255,255,255,0.2); 
        }

        /* --- BOUTON STYLE FIGMA --- */
        button { 
            width: 100%; 
            padding: 14px;
            margin-top: 25px;
            
            /* Fond transparent */
            background: rgba(255, 255, 255, 0.05);
            color: #ffffff; 
            
            /* Bordure blanche marquée */
            border: 2px solid rgba(255, 255, 255, 0.85);
            border-radius: 50px; 
            
            cursor: pointer; 
            font-size: 18px; 
            font-weight: 600; 
            letter-spacing: 1px; 
            
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            backdrop-filter: blur(5px);
        }

        button:hover { 
            background: rgba(255, 255, 255, 0.2);
            border-color: #ffffff;
            box-shadow: 0 0 20px rgba(255, 255, 255, 0.3), 0 5px 15px rgba(0,0,0,0.3);
            transform: translateY(-3px); 
        }

        /* Liens et Erreurs */
        a.register-link { 
            color: rgba(255, 255, 255, 0.8); 
            text-decoration: none; 
            font-size: 0.95em; 
            display: inline-block; 
            margin-top: 25px; 
            transition: 0.3s;
            font-weight: 500;
        }
        a.register-link:hover { color: white; text-shadow: 0 0 5px rgba(255,255,255,0.5); }

        .error { 
            background: rgba(220, 53, 69, 0.2);
            border: 1px solid rgba(220, 53, 69, 0.5);
            padding: 10px;
            border-radius: 10px;
            color: #ffadad; 
            margin-top: 20px; 
            font-size: 0.9em;
            text-shadow: 0 1px 2px black;
        }
    </style>
</head>
<body>

    <div class="card">
        <img src="img/Icon.png" alt="Logo CamView" class="logo">
        
        <h1>Connexion</h1>
        
        <form method="POST" action="">
            <input type="text" name="username" placeholder="Nom d'utilisateur" required autocomplete="username">
            <input type="password" name="password" placeholder="Mot de passe" required autocomplete="current-password">
            <button type="submit">Se connecter</button>
        </form>
        
        <?php if ($message): ?>
            <div class="error"><?php echo $message; ?></div>
        <?php endif; ?>

        <a href="register.php" class="register-link">Pas encore de compte ? S'inscrire</a>
    </div>

</body>
</html>