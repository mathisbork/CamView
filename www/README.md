# 📷 Projet CamView - Architecture LAMP Dockerisée

Ce projet héberge l'application **CamView**. Il repose sur une stack technique **LAMP** (Linux, Apache, MySQL, PHP) conteneurisée via Docker et orchestrée par Docker Compose.

L'accès externe est géré par un **Reverse Proxy (Nginx Proxy Manager)** qui assure le SSL et le routage des domaines.

## 🚀 Installation & Démarrage

### 1. Récupération du projet
Clonez le dépôt sur votre machine locale ou le serveur :

```bash
git clone [https://github.com/mathisbork/CamView.git](https://github.com/mathisbork/CamView.git)
cd CamView
```

### 2. Lancement de la stack
Pour construire les images et lancer les conteneurs en arrière-plan :
```bash
docker compose up -d --build
```
## 🌐 Configuration Réseau & Reverse Proxy
L'application doit communiquer avec Nginx Proxy Manager pour être accessible depuis l'extérieur.

### 1.Connexion au réseau Proxy
Si le déploiement ne se fait pas automatiquement sur le réseau externe, vous devez connecter manuellement les conteneurs au réseau du proxy (généralement nommé ```reverse_proxy``` ou ```npm_network```).

Lancez ces commandes si vous obtenez une erreur 502 Bad Gateway :
```bash
# Connecter le serveur Apache (Site Web)
docker network connect reverse_proxy lamp-php

# Connecter phpMyAdmin (Interface BDD)
docker network connect reverse_proxy lamp-pma
```

### 2.Emplacement des configurations Nginx
Si vous avez besoin de debugger les configurations générées par Nginx Proxy Manager directement sur le serveur hôte, les fichiers de conf se trouvent ici :```~/docker/nginx-proxy-manager/data/nginx/proxy_host```

## 💻 Accès aux Services
Une fois la stack démarrée, les services sont accessibles aux adresses suivantes :
- Application Web	```https://camview.fixassist.fr```	Interface principale de CamView
- PhpMyAdmin	```https://mysql.fixassist.frGestion``` de la base de données

## 🛠 Commandes Utiles pour le Développement
Arrêter la stack :
```docker compose down```

Voir les logs en temps réel (Apache/MySQL) :
```docker compose logs -f```

Accéder au shell du conteneur Web :
```docker exec -it lamp-php bash```

Forcer le redémarrage d'un service unique :
```docker compose restart web```