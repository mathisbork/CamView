<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Test LAMP Stack</title>
    <style>
        body { font-family: sans-serif; text-align: center; padding: 50px; background: #f4f4f4; }
        .card { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); display: inline-block; }
        .status { font-weight: bold; padding: 5px 10px; border-radius: 5px; }
        .success { background: #d4edda; color: #151f57ff; }
        .error { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="card">
        <h1>🐘 Test de votre pile LAMP</h1>
        <p>Serveur : <strong>Apache</strong></p>
        <p>Version PHP : <strong><?php echo phpversion(); ?></strong></p>

        <?php
        // Configuration de la connexion (selon ton docker-compose.yml)
        $host = 'lamp-mysql'; // Nom du service dans le docker-compose
        $user = 'root';
        $pass = 'Estiam2025*'; // Le mot de passe que tu as mis dans le YAML

        try {
            $conn = new PDO("mysql:host=$host", $user, $pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo '<p class="status success">✅ Connexion MySQL réussie !(Test N.2)</p>';
        } catch(PDOException $e) {
            echo '<p class="status error">❌ Échec MySQL : ' . $e->getMessage() . '</p>';
        }
        ?>
    </div>
</body>
</html>
