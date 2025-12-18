<?php
session_start();

// --- SECURITÉ : LE "TOKEN" ---
// On vérifie si la variable de session existe. 
// C'est ça qui sert de preuve d'identité (le "token" côté serveur).
if (!isset($_SESSION['user_id'])) {
    // Si pas connecté, on redirige vers le login
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CamView - Dashboard</title>
    <style>
        body { 
            font-family: 'Segoe UI', sans-serif;
            background: url('img/Background.jpg') no-repeat center center fixed; 
            background-size: cover;
            margin: 0; 
            padding: 20px;
            color: white;
            min-height: 100vh;
        }

        /* Barre de navigation style Glass */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 15px 30px;
            border-radius: 20px;
            margin-bottom: 30px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.2);
        }

        .logo { font-size: 1.5em; font-weight: bold; display: flex; align-items: center; gap: 10px;}
        .logo img { height: 40px; }

        .user-info { display: flex; align-items: center; gap: 20px; }

        /* Grille des caméras */
        .camera-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        /* Carte Caméra style Glass */
        .camera-card {
            background: rgba(0, 0, 0, 0.4); /* Plus sombre pour mieux voir l'image */
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            overflow: hidden;
            transition: transform 0.3s;
        }
        .camera-card:hover { transform: translateY(-5px); border-color: rgba(255,255,255,0.4);}

        .cam-feed {
            width: 100%;
            height: 200px;
            background: #000;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #555;
        }

        .cam-info { padding: 15px; display: flex; justify-content: space-between; align-items: center; }
        .status-dot { height: 10px; width: 10px; background-color: #28a745; border-radius: 50%; display: inline-block; box-shadow: 0 0 5px #28a745;}

        /* Bouton Déconnexion (Style Figma Rouge) */
        .btn-logout {
            background: rgba(220, 53, 69, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.5);
            color: white;
            padding: 8px 20px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            transition: 0.3s;
        }
        .btn-logout:hover { background: #dc3545; box-shadow: 0 0 15px rgba(220, 53, 69, 0.6); }
    </style>
</head>
<body>

    <nav class="navbar">
        <div class="logo">
            <img src="img/Icon.png" alt="Icon">
            CamView Monitor
        </div>
        <div class="user-info">
            <span>Bonjour, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></span>
            <a href="index.php?action=logout" class="btn-logout">Déconnexion</a>
        </div>
    </nav>

    <div class="camera-grid">
        <div class="camera-card">
            <div class="cam-feed">Flux vidéo en attente...</div>
            <div class="cam-info">
                <span>Caméra Entrée</span>
                <span class="status-dot"></span>
            </div>
        </div>

        <div class="camera-card">
            <div class="cam-feed">Flux vidéo en attente...</div>
            <div class="cam-info">
                <span>Caméra Parking</span>
                <span class="status-dot"></span>
            </div>
        </div>
        
        </div>

</body>
</html>