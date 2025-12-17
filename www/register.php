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
        /* Image de fond pour toute la page */
        body { 
            font-family: 'Segoe UI', sans-serif; 
            /* Image de fond (Nature sombre pour faire ressortir le verre) */
            background: url('https://images.unsplash.com/photo-1472214103451-9374bd1c798e?q=80&w=2070&auto=format&fit=crop') no-repeat center center fixed; 
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
            width: 350px; 
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