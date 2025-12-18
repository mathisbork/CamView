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
    $message = "<span style='color:#28a745; font-weight:bold;'>".$_SESSION['success']."</span>";
    unset($_SESSION['success']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // On cherche l'utilisateur
    $stmt = $pdo->prepare("SELECT * FROM user WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    // VERIFICATION DU HASH
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion CamView</title>
    <style>
        /* Reset de base */
        * { box-sizing: border-box; }

        /* Image de fond pour toute la page */
        body { 
            font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, Helvetica, Arial, sans-serif;
            /* Assure-toi que Background.jpg existe bien dans www/img/ */
            background: url('img/Background.jpg') no-repeat center center fixed; 
            background-size: cover;
            display: flex; 
            justify-content: center; 
            align-items: center; 
            min-height: 100vh; 
            margin: 0; 
            padding: 20px; /* Petit padding pour les petits écrans */
        }

        /* L'effet LIQUID GLASS AMÉLIORÉ */
        .card { 
            /* Fond blanc plus transparent pour plus de profondeur */
            background: rgba(255, 255, 255, 0.1); 
            
            /* Flou plus intense pour l'effet liquide */
            backdrop-filter: blur(25px); 
            -webkit-backdrop-filter: blur(25px);
            
            /* Bordure plus nette et brillante */
            border: 1px solid rgba(255, 255, 255, 0.3); 
            border-top: 1px solid rgba(255, 255, 255, 0.5); /* Lumière venant du haut */
            
            padding: 40px 30px; 
            border-radius: 24px; /* Coins plus arrondis */
            /* Ombre portée douce pour détacher la carte du fond */
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2); 
            width: 100%;
            max-width: 420px; /* Largeur max confortable */
            text-align: center; 
            color: white;
        }

        /* Style du logo CamView */
        .logo {
            width: 130px;
            height: auto;
            margin-bottom: 20px;
            /* Ombre portée sur le logo pour le détacher du verre */
            filter: drop-shadow(0 4px 4px rgba(0,0,0,0.3));
        }

        h1 { 
            margin-bottom: 30px; 
            font-weight: 700; 
            letter-spacing: 1px; 
            font-size: 2rem;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        /* Les champs de saisie (Inputs) en mode verre */
        input { 
            width: 100%; 
            padding: 15px; 
            margin: 12px 0; 
            background: rgba(255, 255, 255, 0.15); /* Un peu plus transparent */
            border: 1px solid rgba(255, 255, 255, 0.1); 
            border-radius: 50px; /* Inputs parfaitement ronds (pills) */
            color: white; 
            font-size: 16px;
            outline: none;
            text-align: center;
            transition: all 0.3s ease;
        }

        /* Couleur du texte "placeholder" */
        input::placeholder { color: rgba(255, 255, 255, 0.6); font-weight: 300; }

        /* Effet au focus dans l'input */
        input:focus { 
            background: rgba(255, 255, 255, 0.25); 
            border-color: rgba(255, 255, 255, 0.5);
            box-shadow: 0 0 15px rgba(255,255,255,0.2); 
        }

        /* Le bouton principal */
        button { 
            width: 100%; 
            padding: 15px; 
            margin-top: 20px;
            /* Dégradé subtil pour un look plus moderne */
            background: linear-gradient(135deg, rgba(40, 167, 69, 0.9), rgba(33, 136, 56, 0.9));
            color: white; 
            border: none; 
            border-radius: 50px; 
            cursor: pointer; 
            font-size: 18px; 
            font-weight: bold; 
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
            letter-spacing: 0.5px;
        }
        button:hover { 
            transform: translateY(-2px); 
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
            background: linear-gradient(135deg, rgba(40, 167, 69, 1), rgba(33, 136, 56, 1));
        }
        
        /* Bouton déconnexion rouge */
        .logout { 
            background: linear-gradient(135deg, rgba(220, 53, 69, 0.9), rgba(200, 35, 51, 0.9));
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
        }
        .logout:hover { 
            background: linear-gradient(135deg, rgba(220, 53, 69, 1), rgba(200, 35, 51, 1));
            box-shadow: 0 6px 20px rgba(220, 53, 69, 0.4);
        }

        /* Liens */
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
        }
        .welcome-icon { font-size: 60px; margin-bottom: 20px; display: block; filter: drop-shadow(0 4px 8px rgba(0,0,0,0.3)); }
    </style>
</head>
<body>

    <div class="card">
        <?php if (isset($_SESSION['username'])): ?>
            <span class="welcome-icon">👤</span>
            <h1>Bienvenue<br><?php echo htmlspecialchars($_SESSION['username']); ?></h1>
            <p style="font-size: 1.1em; opacity: 0.9;">Vous êtes connecté sur CamView.</p>
            
            <a href="index.php?action=logout" style="text-decoration: none;">
                <button class="logout">Se déconnecter</button>
            </a>

        <?php else: ?>
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
        <?php endif; ?>
    </div>

</body>
</html>